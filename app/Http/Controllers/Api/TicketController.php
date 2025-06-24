<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Ticket; // Usar tu modelo Ticket
use Illuminate\Support\Facades\Auth;
use TCPDF;

class TicketController extends Controller
{
    /**
     * Obtiene los tickets (órdenes) para el usuario autenticado.
     * Incluye los ítems del ticket y sus productos asociados.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myTickets(Request $request)
    {
        // Asegúrate de que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        // Obtener tickets (órdenes) para el usuario autenticado
        // Cargar eager (carga ansiosa) ticketItems y sus productos asociados,
        // y también la factura asociada.
        $tickets = Ticket::where('user_id', Auth::id())
                        ->with([
                            'ticketItems' => function($query) {
                                $query->with('product'); // Cargar el producto para cada ítem del ticket
                            },
                            'factura' // Cargar la relación de factura para obtener su ID y número
                        ])
                        ->orderByDesc('created_at') // Ordenar por los más recientes primero
                        ->get();

        // Formatear los datos para una respuesta JSON más limpia y con todos los detalles
        $formattedTickets = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'order_number' => $ticket->order_number,
                'monto_total' => $ticket->monto_total,
                'payment_method' => $ticket->payment_method,
                'status' => $ticket->status,
                'created_at' => Carbon::parse($ticket->created_at)->format('d/m/Y H:i'),
                'factura_id' => $ticket->factura ? $ticket->factura->id : null, // ID de la factura
                'numero_factura' => $ticket->factura ? $ticket->factura->numero_factura : null, // Número de factura
                'items' => $ticket->ticketItems->map(function ($item) { // Usa ticketItems si esa es la relación
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product ? $item->product->name : 'Producto Desconocido',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                    ];
                }),
            ];
        });

        return response()->json($formattedTickets);
    }

    /**
     * Obtiene los detalles de un ticket (orden) específico.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ticketDetails(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $ticket = Ticket::with(['ticketItems.product', 'factura']) // También cargar factura aquí si se necesita
                        ->where('user_id', Auth::id()) // Asegúrate de que el ticket pertenece al usuario autenticado
                        ->findOrFail($id); // Lanza 404 si no se encuentra

        return response()->json($ticket);
    }

    /**
     * Descarga el comprobante de la factura en formato PDF,
     * ya sea por ID de factura o por número de factura.
     *
     * @param string $identifier El ID de la factura o el número de factura.
     * @param string $type Indica si el identificador es 'id' o 'numero'.
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice(string $identifier, string $type = 'id')
    {
        $factura = null;

        // Determinar cómo buscar la factura
        if ($type === 'id') {
            $factura = Factura::findOrFail($identifier); // Buscar por ID (clave primaria)
        } elseif ($type === 'numero') {
            $factura = Factura::where('numero_factura', $identifier)->firstOrFail(); // Buscar por numero_factura
        } else {
            // Tipo de identificador no válido
            abort(400, 'Tipo de identificador no válido. Use "id" o "numero".');
        }

        // Lógica de autorización:
        // Asegúrate de que solo el propietario de la factura (user_id en Factura)
        // o un usuario autenticado pueda descargarla.
        // Si la factura tiene un user_id y el usuario autenticado no coincide, se deniega.
        if (Auth::check() && $factura->user_id !== null && Auth::id() !== $factura->user_id) {
            abort(403, 'Acceso no autorizado para descargar esta factura. No eres el propietario.');
        }
        // Si la factura no tiene user_id asignado pero se requiere autenticación para cualquier descarga
        else if (!Auth::check()) {
            abort(401, 'Debes estar autenticado para descargar esta factura.');
        }


        // Llamar al método genérico de generación de PDF con la instancia de Factura encontrada
        return $this->generatePdfResponse($factura);
    }

    /**
     * Método privado para generar el PDF de la factura.
     * Encapsula toda la lógica de TCPDF.
     *
     * @param Factura $factura La instancia de la Factura para la cual generar el PDF.
     * @return \Illuminate\Http\Response
     */
    private function generatePdfResponse(Factura $factura)
    {
        // Instancia de TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Configuración básica del documento PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Brinca Este 24 C.A');
        $pdf->SetTitle('Comprobante de Compra - Factura #' . ($factura->numero_factura ?? $factura->id)); // Título dinámico
        $pdf->SetSubject('Comprobante de Compra');
        $pdf->SetKeywords('Factura, Comprobante, Compra, Brinca Este');

        // Configuración de encabezado y pie de página
        // Puedes personalizar PDF_HEADER_LOGO y PDF_HEADER_LOGO_WIDTH en config/tcpdf.php o definirlos aquí
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Comprobante de Compra', 'Brinca Este 24 C.A.');
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // Márgenes
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

        // Salto de página automático
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Escala de imagen para autoescalado
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Habilitar subconfiguración de fuentes para soporte completo de UTF-8
        $pdf->setFontSubsetting(true);

        // Establecer fuente por defecto
        $pdf->SetFont('helvetica', '', 10, '', true);

        // Añadir una página
        $pdf->AddPage();

        // Obtener detalles adicionales para la factura (ticket y sus items)
        // loadMissing asegura que las relaciones se carguen solo si no lo están ya
        $factura->loadMissing(['ticket.ticketItems.product']);

        $ticket = $factura->ticket; // Acceder al ticket asociado
        // Acceder a los ítems del ticket a través de la relación ya cargada
        $ticketItems = $ticket ? $ticket->ticketItems : collect();

        // Construcción del contenido HTML para el PDF
        $html = '
        <h1 style="text-align: center; color: #333;">COMPROBANTE DE COMPRA</h1>
        <hr style="border-top: 1px solid #ccc; margin: 15px 0;">
        <table cellspacing="0" cellpadding="2" style="width: 100%;">
            <tr>
                <td style="width: 50%;"><strong>Número de Factura:</strong> ' . ($factura->numero_factura ?? 'N/A') . '</td>
                <td style="width: 50%;"><strong>Fecha de Emisión:</strong> ' . (Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i:s') ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Número de Orden:</strong> ' . ($ticket ? $ticket->order_number : 'N/A') . '</td>
                <td><strong>Monto Total:</strong> $' . number_format($factura->monto_total, 2, ',', '.') . '</td>
            </tr>
        </table>
        <br>
        <h2 style="color: #555;">Detalles del Cliente:</h2>
        <p><strong>Nombre:</strong> ' . ($factura->nombre_completo ?? 'N/A') . '</p>
        <p><strong>Correo:</strong> ' . ($factura->correo ?? 'N/A') . '</p>
        <p><strong>Teléfono:</strong> ' . ($factura->telefono ?? 'N/A') . '</p>
        <p><strong>Dirección:</strong> ' . ($factura->direccion ?? 'N/A') . ', ' . ($factura->ciudad ?? 'N/A') . '</p>
        <p><strong>Código Postal:</strong> ' . ($factura->codigo_postal ?? 'N/A') . '</p>
        <br>
        <h2 style="color: #555;">Detalle de Productos:</h2>
        <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="width: 10%; text-align: center; border: 1px solid #ddd;">ID</th>
                    <th style="width: 40%; text-align: left; border: 1px solid #ddd;">Producto</th>
                    <th style="width: 15%; text-align: right; border: 1px solid #ddd;">Cantidad</th>
                    <th style="width: 15%; text-align: right; border: 1px solid #ddd;">Precio Unitario</th>
                    <th style="width: 20%; text-align: right; border: 1px solid #ddd;">Subtotal</th>
                </tr>
            </thead>
            <tbody>';

            if ($ticketItems->isNotEmpty()) {
                foreach ($ticketItems as $item) {
                    $html .= '
                    <tr>
                        <td style="width: 10%; text-align: center; border: 1px solid #ddd;">' . ($item->product_id ?? 'N/A') . '</td>
                        <td style="width: 40%; border: 1px solid #ddd;">' . ($item->product ? $item->product->name : 'Producto Desconocido') . '</td>
                        <td style="width: 15%; text-align: right; border: 1px solid #ddd;">' . ($item->quantity ?? 'N/A') . '</td>
                        <td style="width: 15%; text-align: right; border: 1px solid #ddd;">$' . number_format(($item->price ?? 0), 2, ',', '.') . '</td>
                        <td style="width: 20%; text-align: right; border: 1px solid #ddd;">$' . number_format(($item->subtotal ?? 0), 2, ',', '.') . '</td>
                    </tr>';
                }
            } else {
                $html .= '<tr><td colspan="5" style="text-align: center; border: 1px solid #ddd;">No hay ítems registrados para esta factura.</td></tr>';
            }


            $html .= '
            </tbody>
        </table>
        <br>
        <p style="text-align: right; font-weight: bold; font-size: 1.2em; color: #333;">TOTAL A PAGAR: $' . number_format($factura->monto_total, 2, ',', '.') . '</p>
        <br><br>
        <p style="text-align: center; font-size: 0.9em; color: #666;">Gracias por tu compra en Brinca Este 24 C.A.</p>
        ';

        // Escribir el HTML en el PDF
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Nombre del archivo PDF para la descarga
        $fileName = 'comprobante_compra_' . ($factura->numero_factura ?? $factura->id) . '.pdf';

        // Salida del PDF: 'I' para mostrar en el navegador, 'D' para forzar la descarga.
        // Usamos 'I' para "Inline", lo que lo muestra en el navegador por defecto.
        return $pdf->Output($fileName, 'I');
    }
}