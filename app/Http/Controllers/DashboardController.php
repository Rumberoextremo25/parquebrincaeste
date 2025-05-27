<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\Gasto;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use TCPDF;
use App\Models\Finanza;

class DashboardController extends Controller
{
    private $db;
    public function home()
    {
        // Obtener el total de usuarios registrados
        $totalUsuario = User::count(); // Asegúrate de que tienes un modelo User

        // Pasar los datos a la vista
        return view('dashboard', compact('totalVentas', 'ventasDiarias', 'totalTickets', 'totalUsuario')); // Cambia 'totalUsuarios' a 'userCount'
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

    public function ventas()
    {
        // Obtener la fecha actual
        $hoy = Carbon::now();

        // Ventas diarias
        $ventasDiarias = Venta::whereDate('created_at', $hoy)->sum('monto');

        // Ventas semanales
        $ventasSemanales = Venta::where('created_at', '>=', $hoy->startOfWeek())
            ->sum('monto');

        // Ventas mensuales
        $ventasMensuales = Venta::whereMonth('created_at', $hoy->month)
            ->whereYear('created_at', $hoy->year)
            ->sum('monto');

        // Ventas anuales
        $ventasAnuales = Venta::whereYear('created_at', $hoy->year)
            ->sum('monto');

        // Pasar datos a la vista
        return view('ventas', [
            'ventasDiarias' => $ventasDiarias,
            'ventasSemanales' => $ventasSemanales,
            'ventasMensuales' => $ventasMensuales,
            'ventasAnuales' => $ventasAnuales,
        ]);
    }

    // Metodos para Finanzas

    public function finanzas()
    {
        // Obtener los ingresos totales (sumar todos los montos de ventas)  
        $ingresosTotales = Venta::sum('monto');

        // Obtener los gastos totales (sumar todos los montos de gastos)  
        $gastosTotales = Gasto::sum('monto');

        // Calcular el beneficio neto  
        $beneficioNeto = $ingresosTotales - $gastosTotales;

        // Almacenar los ingresos totales y el ingreso neto en la tabla finanzas
        Finanza::create([
            'ingreso' => $ingresosTotales,
            'gasto' => $gastosTotales, // Puedes almacenar los gastos si es necesario
            'fecha' => now(), // O la fecha que desees almacenar
        ]);

        // Obtener datos para el gráfico de ventas (agrupando por mes)  
        $ventasPorMes = Venta::selectRaw('MONTH(fecha) as mes, SUM(monto) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Convertir los datos de ventas por mes a un arreglo  
        $ventasData = $ventasPorMes->values()->toArray();
        $meses = $ventasPorMes->keys()->map(function ($mes) {
            return date('F', mktime(0, 0, 0, $mes, 1)); // Convertir número de mes a nombre  
        })->toArray();

        // Retornar la vista 'finanzas' con los datos recolectados  
        return view('finanzas', [
            'ingresosTotales' => $ingresosTotales,
            'gastosTotales' => $gastosTotales,
            'beneficioNeto' => $beneficioNeto,
            'ventasData' => $ventasData,
            'meses' => $meses,
        ]);
    }

    public function mostrarFinanzas()
    {
        list($ventasData, $meses) = $this->obtenerVentasPorMes();
        return view('finanzas', compact('ventasData', 'meses'));
    }

    private function obtenerVentasPorMes()
    {
        // Obtener datos para el gráfico de ventas (agrupando por mes)  
        $ventasPorMes = Venta::selectRaw('MONTH(fecha) as mes, SUM(monto) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Convertir los datos de ventas por mes a un arreglo  
        $ventasData = $ventasPorMes->values()->toArray();
        $meses = $ventasPorMes->keys()->map(function ($mes) {
            return date('F', mktime(0, 0, 0, $mes, 1)); // Convertir número de mes a nombre  
        })->toArray();

        // Llenar los meses vacíos con ceros en caso de no haber ventas  
        $ventasDataCompleto = [];
        $mesesCompleto = [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ];

        foreach ($mesesCompleto as $mesCompleto) {
            $mesIndex = array_search($mesCompleto, $meses);
            $ventasDataCompleto[] = $mesIndex !== false ? $ventasData[$mesIndex] : 0;
        }

        return [$ventasDataCompleto, $mesesCompleto];
    }

    //Logica para generar Reportes de Ventas Y Finanzas

    public function generarPDFVentas()
    {
        // Crear una nueva instancia de TCPDF
        $pdf = new TCPDF();

        // Configuración del PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Brinca Este 24 C.A');
        $pdf->SetTitle('Reporte de Ventas');
        $pdf->SetHeaderData('', 0, 'Reporte de Ventas', 'Generado por Tu Aplicación');
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        // Obtener los datos de las ventas
        $ventas = Venta::all();

        // Generar contenido HTML para el reporte de ventas
        $html = '<h1>Reporte de Ventas</h1>';
        $html .= '<table border="1" cellpadding="4"><tr><th>ID</th><th>Producto</th><th>Monto</th><th>Fecha</th></tr>';
        foreach ($ventas as $venta) {
            $html .= '<tr>';
            $html .= '<td>' . $venta->id . '</td>';
            $html .= '<td>' . $venta->producto . '</td>';
            $html .= '<td>' . $venta->monto . '</td>';
            $html .= '<td>' . $venta->fecha . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        // Escribir el contenido HTML en el PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Cerrar y generar el PDF
        $pdf->Output('reporte_ventas.pdf', 'I'); // 'I' para mostrar en el navegador
    }
}
