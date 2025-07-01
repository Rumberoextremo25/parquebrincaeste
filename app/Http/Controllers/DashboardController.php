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

    public function generarPDFVentas(Request $request) // <-- Ahora acepta el objeto Request
    {
        // Obtener fechas del Request o establecer un valor por defecto (ej. todo el historial)
        // Carbon::parse asegura que la cadena de fecha se convierta en un objeto Carbon
        // ->startOfDay() y ->endOfDay() aseguran que el rango incluya todo el día seleccionado
        $fromDate = $request->input('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : null;
        $toDate = $request->input('to_date') ? Carbon::parse($request->input('to_date'))->endOfDay() : null;

        // Obtener la tasa BCV del modelo ExchangeRate
        $currentExchangeRate = ExchangeRate::current();
        $bcvRate = (float) ($currentExchangeRate->rate ?? 0);

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
        // MODIFICACIÓN: Eager load the 'ticket' relationship
        $query = Venta::with('product', 'ticket'); // Carga la relación con el ticket

        // ¡APLICAR LOS FILTROS DE FECHA AQUÍ!
        if ($fromDate && $toDate) {
            $query->whereBetween('fecha', [$fromDate, $toDate]);
            $reportDateRange = ' desde ' . $fromDate->format('d/m/Y') . ' hasta ' . $toDate->format('d/m/Y');
        } elseif ($fromDate) {
            $query->where('fecha', '>=', $fromDate);
            $reportDateRange = ' desde ' . $fromDate->format('d/m/Y');
        } elseif ($toDate) {
            $query->where('fecha', '<=', $toDate);
            $reportDateRange = ' hasta ' . $toDate->format('d/m/Y');
        } else {
            $reportDateRange = ' (Histórico Completo)'; // Si no hay filtros, se muestra todo
        }

        $ventas = $query->orderBy('fecha', 'asc')->get(); // Ordenar por fecha para mejor lectura del reporte

        // Inicializar contadores para los totales
        $totalVendidoUSD = 0;
        $totalVendidoBs = 0;
        $cantidadTotalProductos = 0;
        $cantidadCalcetines = 0;
        $cantidadBrazaletes = 0;

        // --- Encabezado del reporte con la tasa BCV y el rango de fechas ---
        $html = '<h1>Reporte de Ventas</h1>';
        $html .= '<p><strong>Periodo del Reporte:</strong> ' . Carbon::now()->format('d/m/Y H:i') . ' ' . $reportDateRange . '</p>'; // Mostrar el rango seleccionado
        if ($bcvRate > 0) {
            $html .= '<p><strong>Tasa BCV (referencial):</strong> 1 USD = ' . number_format($bcvRate, 2, ',', '.') . ' Bs</p>';
        } else {
            $html .= '<p style="color: red;"><strong>Advertencia:</strong> Tasa BCV no disponible. Los montos en Bolívares pueden no ser precisos.</p>';
        }
        $html .= '<br>'; // Espacio

        // 3. Encabezados de la tabla
        $html .= '<table border="1" cellpadding="4" cellspacing="0">'; // Añadido cellspacing para mejor visualización
        $html .= '<thead><tr>';
        $html .= '<th style="width: 8%;">ID Venta</th>';
        $html .= '<th style="width: 20%;">Nombre Producto</th>';
        $html .= '<th style="width: 8%;">Cant.</th>';
        $html .= '<th style="width: 12%;">Precio Unit. (USD)</th>';
        $html .= '<th style="width: 12%;">Subtotal (USD)</th>';
        $html .= '<th style="width: 12%;">Subtotal (Bs)</th>'; // Nueva columna para subtotal en Bs
        // MODIFICACIÓN: Nueva columna para el método de pago
        $html .= '<th style="width: 15%;">Método Pago</th>';
        $html .= '<th style="width: 13%;">Fecha Venta</th>';
        $html .= '</tr></thead><tbody>';

        // 4. Filas con datos y cálculo de totales por categoría
        foreach ($ventas as $venta) {
            $nombreProducto = $venta->product ? $venta->product->name : 'N/A';
            $productCategory = $venta->product ? $venta->product->category : null;
            // MODIFICACIÓN: Obtener el método de pago del ticket relacionado
            $paymentMethod = $venta->ticket ? $venta->ticket->payment_method : 'N/A';

            // Formatear el método de pago para mejor lectura
            switch ($paymentMethod) {
                case 'mobile-payment':
                    $paymentMethodDisplay = 'Pago Móvil';
                    break;
                case 'credit-debit-card':
                    $paymentMethodDisplay = 'T. Crédito/Débito';
                    break;
                default:
                    $paymentMethodDisplay = ucfirst(str_replace('-', ' ', $paymentMethod)); // Capitalizar y reemplazar guiones
                    break;
            }

            $precioUnitarioUSD = $venta->price;
            $subtotalLineaUSD = $venta->subtotal;
            $subtotalLineaBs = $subtotalLineaUSD * $bcvRate;

            $html .= '<tr>';
            $html .= '<td style="width: 8%;">#' . $venta->id . '</td>';
            $html .= '<td style="width: 20%;">' . htmlspecialchars($nombreProducto) . '</td>';
            $html .= '<td style="width: 8%; text-align: center;">' . $venta->quantity . '</td>';
            $html .= '<td style="width: 12%; text-align: right;">$' . number_format($precioUnitarioUSD, 2, ',', '.') . '</td>';
            $html .= '<td style="width: 12%; text-align: right;">$' . number_format($subtotalLineaUSD, 2, ',', '.') . '</td>';
            $html .= '<td style="width: 12%; text-align: right;">' . number_format($subtotalLineaBs, 2, ',', '.') . ' Bs</td>';
            // MODIFICACIÓN: Mostrar el método de pago
            $html .= '<td style="width: 15%; text-align: center;">' . $paymentMethodDisplay . '</td>';
            $html .= '<td style="width: 13%; text-align: center;">' . ($venta->fecha ? Carbon::parse($venta->fecha)->format('d/m/Y') : Carbon::parse($venta->created_at)->format('d/m/Y')) . '</td>';
            $html .= '</tr>';

            $totalVendidoUSD += $subtotalLineaUSD;
            $totalVendidoBs += $subtotalLineaBs;
            $cantidadTotalProductos += $venta->quantity;

            if ($productCategory === 'Medias') {
                $cantidadCalcetines += $venta->quantity;
            } elseif ($productCategory === 'Brazalete') {
                $cantidadBrazaletes += $venta->quantity;
            }
        }

        $html .= '</tbody></table>';

        // 5. Mostrar Totales al final de la tabla
        $html .= '<br><br>';
        $html .= '<h3>Resumen del Reporte</h3>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0" style="width: 50%;">';
        $html .= '<tr><td><strong>Total Vendido (USD):</strong></td><td style="text-align: right;">$' . number_format($totalVendidoUSD, 2, ',', '.') . '</td></tr>';
        $html .= '<tr><td><strong>Total Vendido (Bs):</strong></td><td style="text-align: right;">' . number_format($totalVendidoBs, 2, ',', '.') . ' Bs</td></tr>';
        $html .= '<tr><td><strong>Cantidad Total de Productos:</strong></td><td style="text-align: right;">' . $cantidadTotalProductos . ' unidades</td></tr>';
        $html .= '<tr><td><strong>Cantidad de Medias Vendidas:</strong></td><td style="text-align: right;">' . $cantidadCalcetines . ' unidades</td></tr>';
        $html .= '<tr><td><strong>Cantidad de Brazaletes Vendidos:</strong></td><td style="text-align: right;">' . $cantidadBrazaletes . ' unidades</td></tr>';
        $html .= '</table>';

        // 6. Escribir el HTML al PDF y generar la salida
        $pdf->writeHTML($html, true, false, true, false, '');

        // Cierra y genera el documento PDF (I para mostrar en el navegador, D para descargar)
        $pdf->Output('reporte_ventas_' . Carbon::now()->format('Ymd_His') . '.pdf', 'I');
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
