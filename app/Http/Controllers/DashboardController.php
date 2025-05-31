<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Venta;
use App\Http\Controllers\Controller;
use TCPDF;
use App\Models\Finanza;
use App\Models\Visita;

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

    public function Ventas()
    {
        $ventasDiarias = $this->obtenerVentasPorDia();

        return view('ventas', compact(
            'ventasDiarias',
        ));
    }

    // Metodos para Finanzas

    public function finanzas()
    {
        // Obtener ingresos del mes actual y año actual desde la tabla Ventas
        $totalIngresoMesActual = Venta::whereYear('fecha', date('Y'))
            ->whereMonth('fecha', date('m'))
            ->sum('monto'); // Asumiendo que 'monto' es el campo que representa el ingreso en la tabla Ventas

        // Guardar en la tabla Finanza solo el ingreso total del mes actual
        Finanza::create([
            'ingreso' => $totalIngresoMesActual,
            'gasto' => 0,
            'fecha' => now(),
            'descripcion' => 'Ingresos del mes ' . date('F Y'), // Campo descripción
        ]);

        // Cálculo de ingresos totales y gastos totales
        $ingresosTotales = Finanza::sum('ingreso');
        $gastosTotales = Finanza::sum('gasto'); // Asegúrate de que esto se ajuste a tu lógica
        $beneficioNeto = $ingresosTotales - $gastosTotales;

        // Obtener los ingresos por mes desde la tabla Ventas
        $ingresosPorMes = Venta::selectRaw('MONTH(fecha) as mes, SUM(monto) as total') // Cambia 'monto' por el campo adecuado
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $ingresosData = $ingresosPorMes->values()->toArray();
        $meses = $ingresosPorMes->keys()->map(function ($mes) {
            return date('F', mktime(0, 0, 0, $mes, 1));
        })->toArray();

        return view('finanzas', [
            'ingresosTotales' => $ingresosTotales,
            'gastosTotales' => $gastosTotales,
            'beneficioNeto' => $beneficioNeto,
            'ingresosData' => $ingresosData,
            'meses' => $meses,
            'ingresosMesActual' => $totalIngresoMesActual,
            'ingresosMesAnterior' => null, // Puedes calcular el ingreso del mes anterior si lo deseas
        ]);
    }

    public function mostrarFinanzas()
    {
        list($ventasData, $meses) = $this->obtenerVentasPorMes();
        return view('finanzas', compact('ventasData', 'meses'));
    }

    private function obtenerVentasPorDia()
    {
        $totalDiaActual = Venta::whereDate('fecha', date('Y-m-d'))
            ->sum('monto');

        return $totalDiaActual;
    }

    // Datos de ventas por semana

    // Datos de ventas por mes

    // Datos de ventas por año

    //Logica para generar Reportes de Ventas Y Finanzas

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
