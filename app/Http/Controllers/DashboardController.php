<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Factura;
use App\Models\Subscriber;
use App\Models\Ticket;
use App\Services\BcvService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Venta;
use App\Http\Controllers\Controller;
use TCPDF;
use App\Models\Finanza;
use App\Models\Product;
use App\Models\Visita;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function home(Request $request): View
    {
        $TotalUsers = User::count();
        $TotalVisits = Visita::count();
        $TotalSubscribers = Subscriber::count();

        return view('dashboard')
            ->with('TotalUsers', $TotalUsers)          // Coincide con $TotalUsers en la vista
            ->with('TotalVisits', $TotalVisits)        // Coincide con $TotalVisits en la vista
            ->with('TotalSubscribers', $TotalSubscribers); // Coincide con $TotalSubscribers en la vista
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
        // Obtener la tasa BCV del modelo ExchangeRate, que ahora provee la tasa manual.
        // Aseguramos que sea siempre un float para evitar errores.
        $currentExchangeRate = ExchangeRate::current();
        $bcvRate = (float) ($currentExchangeRate->rate ?? 0); // Si no hay tasa, usará 0

        // --- 1. Calcular Ingresos, Gastos y Beneficio Neto Global desde la tabla Finanza (en USD) ---
        $ingresosTotalesUSD = Finanza::sum('ingreso');
        $gastosTotalesUSD = Finanza::sum('gasto');
        $beneficioNetoUSD = $ingresosTotalesUSD - $gastosTotalesUSD;

        // --- Calcular Ingresos, Gastos y Beneficio Neto Global en Bolívares ---
        $ingresosTotalesBs = $ingresosTotalesUSD * $bcvRate;
        $gastosTotalesBs = $gastosTotalesUSD * $bcvRate;
        $beneficioNetoBs = $beneficioNetoUSD * $bcvRate;

        // --- 2. (Opcional, basado en tu lógica anterior) Registrar/Actualizar Ingresos del Mes Actual en Finanza ---
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Sumar ingresos del mes actual desde 'Venta' para actualizar 'Finanza' (en USD)
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

        // Datos para gráficos en Bolívares
        $ingresosDataBs = [];
        $gastosDataBs = [];
        $beneficioNetoDataBs = [];


        for ($i = 1; $i <= 12; $i++) {
            $mesNombre = $mesesNombres[$i];
            $labelsMeses[] = $mesNombre;

            // Obtener ingresos y gastos para el mes 'i' desde las colecciones (que ya vienen de Finanza)
            $ingresoUSD = $ingresosPorMesColeccion->get($i, 0);
            $gastoUSD = $gastosPorMesColeccion->get($i, 0);

            $ingresosData[] = $ingresoUSD;
            $gastosData[] = $gastoUSD;
            $beneficioNetoData[] = $ingresoUSD - $gastoUSD;

            // Calcular y almacenar datos para gráficos en Bolívares
            $ingresosDataBs[] = $ingresoUSD * $bcvRate;
            $gastosDataBs[] = $gastoUSD * $bcvRate;
            $beneficioNetoDataBs[] = ($ingresoUSD - $gastoUSD) * $bcvRate;
        }

        // Pasa las variables a la vista
        return view('finanzas', [
            'ingresosTotalesUSD' => $ingresosTotalesUSD,
            'gastosTotalesUSD' => $gastosTotalesUSD,
            'beneficioNetoUSD' => $beneficioNetoUSD,
            'ingresosTotalesBs' => $ingresosTotalesBs, // Nuevo: Ingresos totales en Bs
            'gastosTotalesBs' => $gastosTotalesBs,     // Nuevo: Gastos totales en Bs
            'beneficioNetoBs' => $beneficioNetoBs,     // Nuevo: Beneficio neto en Bs
            'ingresosData' => $ingresosData,
            'gastosData' => $gastosData,
            'beneficioNetoData' => $beneficioNetoData,
            'labelsMeses' => $labelsMeses,
            'ingresosDataBs' => $ingresosDataBs,       // Nuevo: Datos de ingresos por mes en Bs
            'gastosDataBs' => $gastosDataBs,           // Nuevo: Datos de gastos por mes en Bs
            'beneficioNetoDataBs' => $beneficioNetoDataBs, // Nuevo: Datos de beneficio neto por mes en Bs
            'bcvRate' => $bcvRate,                      // Pasa la tasa BCV a la vista
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
        // Obtener la tasa BCV del modelo ExchangeRate, que ahora provee la tasa manual.
        // Aseguramos que sea siempre un float para evitar errores.
        $currentExchangeRate = ExchangeRate::current();
        $bcvRate = (float) ($currentExchangeRate->rate ?? 0); // Si no hay tasa, usará 0

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
        // Asegúrate que 'Venta' y 'product' existen y están bien relacionados en tu modelo
        $ventas = Venta::with('product')->get();

        // Inicializar contadores para los totales
        $totalVendidoUSD = 0; // Renombrado para claridad
        $totalVendidoBs = 0; // Nuevo total en Bs
        $cantidadTotalProductos = 0;
        $cantidadCalcetines = 0;
        $cantidadBrazaletes = 0;

        // --- Encabezado del reporte con la tasa BCV ---
        $html = '<h1>Reporte de Ventas</h1>';
        $html .= '<p><strong>Fecha del Reporte:</strong> ' . Carbon::now()->format('d/m/Y H:i') . '</p>';
        if ($bcvRate > 0) {
            $html .= '<p><strong>Tasa BCV (referencial):</strong> 1 USD = ' . number_format($bcvRate, 2, ',', '.') . ' Bs</p>';
        } else {
            $html .= '<p style="color: red;"><strong>Advertencia:</strong> Tasa BCV no disponible. Los montos en Bolívares pueden no ser precisos.</p>';
        }
        $html .= '<br>'; // Espacio

        // 3. Encabezados de la tabla
        $html .= '<table border="1" cellpadding="4" cellspacing="0">'; // Añadido cellspacing para mejor visualización
        $html .= '<thead><tr>';
        $html .= '<th style="width: 10%;">ID Venta</th>';
        $html .= '<th style="width: 25%;">Nombre Producto</th>';
        $html .= '<th style="width: 10%;">Cant.</th>';
        $html .= '<th style="width: 15%;">Precio Unit. (USD)</th>';
        $html .= '<th style="width: 15%;">Subtotal (USD)</th>';
        $html .= '<th style="width: 15%;">Subtotal (Bs)</th>'; // Nueva columna para subtotal en Bs
        $html .= '<th style="width: 10%;">Fecha Venta</th>';
        $html .= '</tr></thead><tbody>';

        // 4. Filas con datos y cálculo de totales por categoría
        foreach ($ventas as $venta) {
            $nombreProducto = $venta->product ? $venta->product->name : 'N/A';
            $productCategory = $venta->product ? $venta->product->category : null;

            // Calcula el precio unitario y subtotal en USD para esta línea (si no vienen directamente de la DB)
            $precioUnitarioUSD = $venta->price; // Asumiendo que $venta->price es el precio unitario en USD
            $subtotalLineaUSD = $venta->subtotal; // Asumiendo que $venta->subtotal es el subtotal de la línea en USD

            // Calcular subtotal en Bolívares
            $subtotalLineaBs = $subtotalLineaUSD * $bcvRate;

            $html .= '<tr>';
            $html .= '<td style="width: 10%;">#' . $venta->id . '</td>';
            $html .= '<td style="width: 25%;">' . htmlspecialchars($nombreProducto) . '</td>'; // Usar htmlspecialchars para seguridad
            $html .= '<td style="width: 10%; text-align: center;">' . $venta->quantity . '</td>';
            $html .= '<td style="width: 15%; text-align: right;">$' . number_format($precioUnitarioUSD, 2, ',', '.') . '</td>';
            $html .= '<td style="width: 15%; text-align: right;">$' . number_format($subtotalLineaUSD, 2, ',', '.') . '</td>';
            $html .= '<td style="width: 15%; text-align: right;">' . number_format($subtotalLineaBs, 2, ',', '.') . ' Bs</td>'; // Monto en Bs
            $html .= '<td style="width: 10%; text-align: center;">' . ($venta->fecha ? Carbon::parse($venta->fecha)->format('d/m/Y') : Carbon::parse($venta->created_at)->format('d/m/Y')) . '</td>';
            $html .= '</tr>';

            // Acumular los totales generales
            $totalVendidoUSD += $subtotalLineaUSD;
            $totalVendidoBs += $subtotalLineaBs; // Acumular total en Bs
            $cantidadTotalProductos += $venta->quantity;

            // Acumular cantidades por las categorías específicas
            if ($productCategory === 'Medias') { // Ajustado a 'Medias' (capitalización)
                $cantidadCalcetines += $venta->quantity;
            } elseif ($productCategory === 'Brazalete') { // Ajustado a 'Brazalete' (capitalización)
                $cantidadBrazaletes += $venta->quantity;
            }
        }

        $html .= '</tbody></table>';

        // 5. Agregar la sección de totales al final del reporte
        $html .= '<br><br>';
        $html .= '<table border="0" cellpadding="4" cellspacing="0" style="width: 100%;">'; // Usar el 100% del ancho
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right; font-weight: bold;">TOTAL VENDIDO (USD):</td>'; // Etiqueta clara
        $html .= '<td style="width: 20%; text-align: right; font-weight: bold;">$' . number_format($totalVendidoUSD, 2, ',', '.') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right; font-weight: bold;">TOTAL VENDIDO (Bs):</td>'; // Nueva fila para total en Bs
        $html .= '<td style="width: 20%; text-align: right; font-weight: bold;">' . number_format($totalVendidoBs, 2, ',', '.') . ' Bs</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right; font-weight: bold;">CANTIDAD TOTAL DE PRODUCTOS:</td>';
        $html .= '<td style="width: 20%; text-align: right; font-weight: bold;">' . number_format($cantidadTotalProductos, 0, ',', '.') . '</td>';
        $html .= '</tr>';
        // Filas para el desglose por categorías específicas
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right;">- Cantidad de Calcetines:</td>';
        $html .= '<td style="width: 20%; text-align: right;">' . number_format($cantidadCalcetines, 0, ',', '.') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="width: 80%; text-align: right;">- Cantidad de Brazaletes:</td>';
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

    public function tickets()
    {
        $tickets = Ticket::paginate(20);
        $factura = Factura::all();
        $products = Product::all();

        return view('tickets', compact('tickets', 'factura', 'products'));
    }
}
