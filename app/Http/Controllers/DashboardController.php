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
        $ventasSemanales = $this->obtenerVentasPorSemana();
        $ventasMensuales = $this->obtenerVentasPorMes();
        $ventasAnuales = $this->obtenerVentasPorAnio();

        return view('ventas', compact(
            'ventasDiarias',
            'ventasSemanales',
            'ventasMensuales',
            'ventasAnuales'
        ));
    }

    // Metodos para Finanzas

    public function finanzas()
    {
        // Obtener ventas del mes actual y año actual
        $totalVentaMesActual = Venta::whereYear('fecha', date('Y'))
            ->whereMonth('fecha', date('m'))
            ->sum('monto');

        // Guardar en la tabla Finanza solo la venta total del mes actual
        Finanza::create([
            'ingreso' => $totalVentaMesActual,
            'gasto' => 0,
            'fecha' => now(),
            'descripcion' => 'Ventas del mes ' . date('F Y'), //campo descripción
        ]);

        // El resto de tu código original si quieres mantenerlo
        $ingresosTotales = Venta::sum('monto');
        $gastosTotales = Gasto::sum('monto');
        $beneficioNeto = $ingresosTotales - $gastosTotales;

        $ventasPorMes = Venta::selectRaw('MONTH(fecha) as mes, SUM(monto) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $ventasData = $ventasPorMes->values()->toArray();
        $meses = $ventasPorMes->keys()->map(function ($mes) {
            return date('F', mktime(0, 0, 0, $mes, 1));
        })->toArray();

        return view('finanzas', [
            'ingresosTotales' => $ingresosTotales,
            'gastosTotales' => $gastosTotales,
            'beneficioNeto' => $beneficioNeto,
            'ventasData' => $ventasData,
            'meses' => $meses,
            'ventasMesActual' => $totalVentaMesActual,
            'ventasMesAnterior' => null,
        ]);
    }

    public function mostrarFinanzas()
    {
        list($ventasData, $meses) = $this->obtenerVentasPorMes();
        return view('finanzas', compact('ventasData', 'meses'));
    }

    private function obtenerVentasPorDia()
    {
        $ventasPorDia = Venta::selectRaw('DATE(fecha) as dia, SUM(monto) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        // Sumatoria total
        return $ventasPorDia->sum('total');
    }

    // Datos de ventas por semana
    private function obtenerVentasPorSemana()
    {
        $ventasPorSemana = Venta::selectRaw('WEEK(fecha) as semana, YEAR(fecha) as anio, SUM(monto) as total')
            ->groupBy('anio', 'semana')
            ->orderBy('anio')
            ->orderBy('semana')
            ->get();

        return $ventasPorSemana->sum('total');
    }

    // Datos de ventas por mes
    private function obtenerVentasPorMes()
    {
        $ventasPorMes = Venta::selectRaw('MONTH(fecha) as mes, SUM(monto) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return $ventasPorMes->sum('total');
    }

    // Datos de ventas por año
    private function obtenerVentasPorAnio()
    {
        $ventasPorAnio = Venta::selectRaw('YEAR(fecha) as anio, SUM(monto) as total')
            ->groupBy('anio')
            ->orderBy('anio')
            ->get();

        return $ventasPorAnio->sum('total');
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
