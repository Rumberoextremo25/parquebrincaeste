<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Venta;
use App\Http\Controllers\Controller;
use TCPDF;
use App\Models\Finanza;
use App\Models\Visita;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function home(Request $request): View
    {
        // Obtener el total de usuarios registrados
        $totalUsers = User::count();

        // Obtener el total de visitas registradas
        $totalVisits = Visita::count();

        // --- Lógica para visitas y usuarios de hoy/ayer y porcentajes ---

        // Visitas de hoy
        $visitsToday = Visita::whereDate('visited_at', Carbon::today())->count();

        // Visitas de ayer
        $visitsYesterday = Visita::whereDate('visited_at', Carbon::yesterday())->count();

        // Calcular porcentaje de cambio de visitas (hoy vs ayer)
        $percentageChangeVisits = 0;
        if ($visitsYesterday > 0) {
            $percentageChangeVisits = (($visitsToday - $visitsYesterday) / $visitsYesterday) * 100;
        } elseif ($visitsToday > 0) {
            $percentageChangeVisits = 100; // Si no hubo visitas ayer pero sí hoy, es un aumento del 100%
        }
        // Formatear porcentaje para mostrar con un signo
        $percentageChangeVisitsFormatted = number_format($percentageChangeVisits, 2) . '%';
        if ($percentageChangeVisits > 0) {
            $percentageChangeVisitsFormatted = '+' . $percentageChangeVisitsFormatted;
        }

        // Nuevos usuarios registrados hoy
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();

        // Nuevos usuarios registrados ayer
        $newUsersYesterday = User::whereDate('created_at', Carbon::yesterday())->count();

        // Calcular porcentaje de cambio de usuarios (hoy vs ayer)
        $percentageChangeUsers = 0;
        if ($newUsersYesterday > 0) {
            $percentageChangeUsers = (($newUsersToday - $newUsersYesterday) / $newUsersYesterday) * 100;
        } elseif ($newUsersToday > 0) {
            $percentageChangeUsers = 100; // Si no hubo usuarios nuevos ayer pero sí hoy, es un aumento del 100%
        }
        // Formatear porcentaje para mostrar con un signo
        $percentageChangeUsersFormatted = number_format($percentageChangeUsers, 2) . '%';
        if ($percentageChangeUsers > 0) {
            $percentageChangeUsersFormatted = '+' . $percentageChangeUsersFormatted;
        }


        // Pasa las variables actualizadas a la vista
        return view('dashboard', [
            'totalUsers' => $totalUsers,
            'totalVisits' => $totalVisits,
            'visitsToday' => $visitsToday,
            'newUsersToday' => $newUsersToday,
            'percentageChangeVisits' => $percentageChangeVisitsFormatted,
            'percentageChangeUsers' => $percentageChangeUsersFormatted,
            'percentageChangeVisitsRaw' => $percentageChangeVisits, // Puedes pasar el valor raw para la lógica de color
            'percentageChangeUsersRaw' => $percentageChangeUsers, // Puedes pasar el valor raw para la lógica de color
        ]);
    }

    public function myAccount()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Retornar la vista 'my_account' con los datos del usuario
        return view('my_account', compact('user'));
    }

    public function changePassword(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar la contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save(); // Guardar el nuevo password

        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', 'Contraseña cambiada correctamente.');
    }

    public function updateAccount(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Actualizar los datos del usuario
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save(); // Guardar en la base de datos

        // Redirigir con un mensaje de éxito
        return redirect()->back()->with('success', 'Datos actualizados correctamente.');
    }

    // Metodos para Ventas

    public function Ventas(Request $request): View
    {
        // Obtener ventas del día actual para la tarjeta
        $ventasDiarias = $this->obtenerVentasPorDia();

        // --- Lógica para el gráfico de ventas mensuales ---
        $currentYear = Carbon::now()->year;

        // Obtener ventas por mes del año actual
        $ventasMensualesColeccion = Venta::selectRaw('MONTH(fecha) as mes, SUM(monto) as total_ventas')
            ->whereYear('fecha', $currentYear)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_ventas', 'mes');

        $mesesNombres = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $labelsMeses = [];
        $ventasData = [];

        // Rellenar con 0 para los meses sin ventas
        for ($i = 1; $i <= 12; $i++) {
            $labelsMeses[] = $mesesNombres[$i];
            $ventasData[] = $ventasMensualesColeccion->get($i, 0); // Si no hay ventas, usa 0
        }

        // Pasa todas las variables a la vista
        return view('ventas', [
            'ventasDiarias' => $ventasDiarias,
            'labelsMeses' => $labelsMeses,
            'ventasData' => $ventasData,
        ]);
    }

    // Metodos para Finanzas

    public function finanzas(): View
    {
        // --- 1. Calcular Ingresos, Gastos y Beneficio Neto Global desde la tabla Finanza ---
        $ingresosTotales = Finanza::sum('ingreso');
        $gastosTotales = Finanza::sum('gasto');
        $beneficioNeto = $ingresosTotales - $gastosTotales;

        // --- 2. (Opcional, basado en tu lógica anterior) Registrar/Actualizar Ingresos del Mes Actual en Finanza ---
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Sumar ingresos del mes actual desde 'Venta' para actualizar 'Finanza'
        $totalIngresoMesActualDeVentas = Venta::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('monto');

        // Busca si ya existe un registro de Finanza para el mes actual
        $finanzaMesActual = Finanza::whereYear('fecha', $currentYear)
                                    ->whereMonth('fecha', $currentMonth)
                                    ->first();

        if ($finanzaMesActual) {
            // Si ya existe, actualiza el ingreso para el mes actual en Finanza
            $finanzaMesActual->update([
                'ingreso' => $totalIngresoMesActualDeVentas,
                // Si también gestionas gastos diarios/mensuales y los quieres consolidar aquí,
                // tendrías que calcularlos y actualizarlos también.
            ]);
        } else {
            // Si no existe, crea un nuevo registro para el mes actual
            Finanza::create([
                'ingreso' => $totalIngresoMesActualDeVentas,
                'gasto' => 0, // Inicia con 0, a menos que tengas otra fuente para los gastos de este mes
                'fecha' => Carbon::now()->startOfMonth(), // Al inicio del mes
                'descripcion' => 'Ingresos consolidados de ventas para ' . Carbon::now()->format('F Y'),
            ]);
        }

        // --- 3. Obtener Datos Mensuales para los Gráficos directamente desde la tabla Finanza ---

        // Obtener ingresos por mes del año actual desde la tabla Finanza
        $ingresosPorMesColeccion = Finanza::selectRaw('MONTH(fecha) as mes, SUM(ingreso) as total_ingresos')
            ->whereYear('fecha', $currentYear) // Asegúrate de filtrar por el año actual
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_ingresos', 'mes');

        // Obtener gastos por mes del año actual desde la tabla Finanza
        $gastosPorMesColeccion = Finanza::selectRaw('MONTH(fecha) as mes, SUM(gasto) as total_gastos')
            ->whereYear('fecha', $currentYear) // Asegúrate de filtrar por el año actual
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_gastos', 'mes');

        $mesesNombres = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $ingresosData = [];
        $gastosData = [];
        $beneficioNetoData = [];
        $labelsMeses = [];

        for ($i = 1; $i <= 12; $i++) {
            $mesNombre = $mesesNombres[$i];
            $labelsMeses[] = $mesNombre;

            // Obtener ingresos y gastos para el mes 'i' desde las colecciones (que ya vienen de Finanza)
            $ingreso = $ingresosPorMesColeccion->get($i, 0);
            $gasto = $gastosPorMesColeccion->get($i, 0);

            $ingresosData[] = $ingreso;
            $gastosData[] = $gasto;
            $beneficioNetoData[] = $ingreso - $gasto;
        }

        // Pasa las variables a la vista
        return view('finanzas', [
            'ingresosTotales' => $ingresosTotales,
            'gastosTotales' => $gastosTotales,
            'beneficioNeto' => $beneficioNeto,
            'ingresosData' => $ingresosData,
            'gastosData' => $gastosData,
            'beneficioNetoData' => $beneficioNetoData,
            'labelsMeses' => $labelsMeses,
        ]);
    }

    public function mostrarFinanzas()
    {
        list($ventasData, $meses) = $this->obtenerVentasPorMes();
        return view('finanzas', compact('ventasData', 'meses'));
    }

    private function obtenerVentasPorDia(): float
    {
        $totalDiaActual = Venta::whereDate('fecha', Carbon::today())
            ->sum('monto');

        return $totalDiaActual;
    }

    public function generarPDFVentas()
    {
        $pdf = new TCPDF();

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Brinca Este 24 C.A');
        $pdf->SetTitle('Reporte de Ventas');
        $pdf->SetHeaderData('', 0, 'Reporte de Ventas', 'Brinca Este');
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        // Obtener los datos de las ventas
        $ventas = Venta::all();

        // Encabezados de la tabla
        $html = '<h1>Reporte de Ventas</h1>';
        $html .= '<table border="1" cellpadding="4"><tr>';
        $html .= '<th>ID</th>';
        $html .= '<th>Producto</th>';   // Solo el nombre del producto
        $html .= '<th>monto</th>';
        $html .= '<th>Cantidad</th>';
        $html .= '<th>Fecha</th>';
        $html .= '</tr>';

        // Filas con datos
        foreach ($ventas as $venta) {
            // Decodificar el JSON del campo producto
            $productoArray = json_decode($venta->producto, true);
            // Obtener solo el nombre o un valor por defecto si no existe
            $nombreProducto = $productoArray && isset($productoArray['name']) ? $productoArray['name'] : '';

            $html .= '<tr>';
            $html .= '<td>' . $venta->id . '</td>';
            $html .= '<td>' . $nombreProducto . '</td>'; // Mostrar solo el nombre
            $html .= '<td>' . $venta->monto . '</td>';
            $html .= '<td>' . $venta->cantidad . '</td>';
            $html .= '<td>' . $venta->fecha . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('reporte_ventas.pdf', 'I'); // Mostrar en navegador
    }

    public function obtenerUsuariosYVisitantes()
    {
        // Cantidad de usuarios registrados
        $cantidadUsuarios = User::count();

        // Cantidad de visitantes únicos (por IP)
        $cantidadVisitantes = Visita::distinct('ip')->count('ip');

        return [
            'usuarios' => $cantidadUsuarios,
            'visitantes' => $cantidadVisitantes,
        ];
    }

    public function registrarVisita(Request $request)
    {
        Visita::create([
            'ip' => $request->ip()
        ]);
    }

    public function dashboardData()
    {
        $usuario = User::count();
        $visitante = Visita::distinct('ip')->count('ip');
        return view('dashboard', compact('usuario', 'visitante'));
    }
}
