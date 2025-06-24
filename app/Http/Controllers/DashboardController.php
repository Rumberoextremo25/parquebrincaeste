<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
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
        // Puedes obtener el usuario autenticado si lo necesitas en el controlador
        // $user = Auth::user();

        // Obtener el total de usuarios registrados desde la tabla 'users'
        $TotalUsers = User::count(); // Cambiado a $TotalUsers para consistencia

        // Obtener el total de visitas registradas desde la tabla 'visitas'
        // Si no tienes un modelo 'Visit' o tabla 'visitas', puedes inicializar a 0 o manejar de otra forma
        $TotalVisits = Visita::count();

        // Obtener el total de suscriptores del newsletter desde la tabla 'subscribers'
        // Si no tienes un modelo 'Subscriber' o tabla 'subscribers', puedes inicializar a 0 o manejar de otra forma
        $TotalSubscribers = Subscriber::count();

        // Los nombres de las variables en compact() deben coincidir
        // con los nombres de las variables que se esperan en la vista Blade.
        return view('dashboard', compact(
            'TotalUsers', // <-- ¡SOLUCIÓN APLICADA AQUÍ! Coincide con la vista Blade
            'TotalVisits',
            'TotalSubscribers',
        ));
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
        $ventasMensualesColeccion = Venta::selectRaw('MONTH(fecha) as mes, SUM(subtotal) as total_ventas')
            ->whereYear('fecha', $currentYear)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total_ventas', 'mes');

        $mesesNombres = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
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
            ->sum('subtotal');

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
            1 => 'Ene',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dic'
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
            ->sum('subtotal');

        return $totalDiaActual;
    }

    public function generarPDFVentas()
    {
        // 1. Configuración básica de TCPDF
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

        // 2. Obtener los datos de las ventas con sus productos relacionados
        $ventas = Venta::with('product')->get();

        // Inicializar contadores para los totales
        $totalVendido = 0;
        $cantidadTotalProductos = 0;
        $cantidadCalcetines = 0;   // Contador para calcetines
        $cantidadBrazaletes = 0;   // Contador para brazaletes

        // 3. Encabezados de la tabla
        $html = '<h1>Reporte de Ventas</h1>';
        $html .= '<table border="1" cellpadding="4"><tr>';
        $html .= '<th>ID de Venta</th>';
        $html .= '<th>Nombre del Producto</th>';
        $html .= '<th>Precio Unitario</th>';
        $html .= '<th>Cantidad</th>';
        $html .= '<th>Subtotal Línea</th>';
        $html .= '<th>Fecha de Venta</th>';
        $html .= '</tr>';

        // 4. Filas con datos y cálculo de totales por categoría
        foreach ($ventas as $venta) {
            $nombreProducto = $venta->product ? $venta->product->name : 'N/A';
            // Accedemos a la categoría del producto desde la relación
            $productCategory = $venta->product ? $venta->product->category : null;

            $html .= '<tr>';
            $html .= '<td>' . $venta->id . '</td>';
            $html .= '<td>' . $nombreProducto . '</td>';
            $html .= '<td>' . number_format($venta->price, 2, ',', '.') . '</td>';
            $html .= '<td>' . $venta->quantity . '</td>';
            $html .= '<td>' . number_format($venta->subtotal, 2, ',', '.') . '</td>';
            $html .= '<td>' . ($venta->fecha ? Carbon::parse($venta->fecha)->format('d/m/Y') : Carbon::parse($venta->created_at)->format('d/m/Y')) . '</td>';
            $html .= '</tr>';

            // Acumular los totales generales
            $totalVendido += $venta->subtotal;
            $cantidadTotalProductos += $venta->quantity;

            // Acumular cantidades por las categorías específicas
            if ($productCategory === 'Calcetines') { // <-- Ajustado a 'calcetines'
                $cantidadCalcetines += $venta->quantity;
            } elseif ($productCategory === 'Brazalete') { // <-- Ajustado a 'brazaletes'
                $cantidadBrazaletes += $venta->quantity;
            }
        }

        $html .= '</table>';

        // 5. Agregar la sección de totales al final del reporte
        $html .= '<br><br>';
        $html .= '<table border="0" cellpadding="4">';
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right; font-weight: bold;">TOTAL VENDIDO:</td>';
        $html .= '<td style="width: 20%; text-align: right; font-weight: bold;">' . number_format($totalVendido, 2, ',', '.') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right; font-weight: bold;">CANTIDAD TOTAL DE PRODUCTOS:</td>';
        $html .= '<td style="width: 20%; text-align: right; font-weight: bold;">' . number_format($cantidadTotalProductos, 0, ',', '.') . '</td>';
        $html .= '</tr>';
        // Filas para el desglose por categorías específicas
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right;">- Cantidad de Calcetines:</td>'; // <-- Etiqueta ajustada
        $html .= '<td style="width: 20%; text-align: right;">' . number_format($cantidadCalcetines, 0, ',', '.') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right;">- Cantidad de Brazaletes:</td>'; // <-- Etiqueta ajustada
        $html .= '<td style="width: 20%; text-align: right;">' . number_format($cantidadBrazaletes, 0, ',', '.') . '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        // 6. Escribir HTML y generar PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('reporte_ventas.pdf', 'I');
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
