<?php

namespace App\Http\Controllers; // ¡VERIFICA ESTE NAMESPACE! (puede ser `App\Http\Controllers\Api` si lo tienes en una subcarpeta)

use App\Models\Factura; // Asegúrate de importar tu modelo Factura
use App\Models\Ticket; // Se necesita si Factura no tiene la relación directa a Ticket
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Para verificar la autenticación
use Carbon\Carbon; // Para formatear fechas
use TCPDF; // Necesitamos importar TCPDF

class InvoiceController extends Controller
{
    /**
     * Descarga el comprobante de la factura en formato PDF por su ID.
     * La factura se muestra directamente en el navegador.
     *
     * @param Factura $factura La instancia de la factura a descargar (inyectada por Laravel).
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoiceById(Factura $factura) // Utiliza Route Model Binding
    {
        // Lógica de autorización:
        // Asegúrate de que solo el propietario de la factura o un administrador puedan descargarla.
        if (Auth::check() && $factura->user_id !== null && Auth::id() !== $factura->user_id) {
            abort(403, 'Acceso no autorizado para descargar esta factura. No eres el propietario.');
        }
        // Si no hay usuario autenticado y la descarga requiere autenticación
        else if (!Auth::check() && $factura->user_id !== null) { // Requiere que la factura tenga user_id para esta restricción
            abort(401, 'Debes estar autenticado para descargar esta factura.');
        }

        // Llamar al método genérico de generación de PDF
        return $this->generatePdfResponse($factura);
    }

    /**
     * Descarga el comprobante de la factura en formato PDF por su número de factura.
     * La factura se muestra directamente en el navegador.
     *
     * @param string $numeroFactura El número único de la factura.
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoiceByNumber(string $numeroFactura)
    {
        // Buscar la factura por su numero_factura
        $factura = Factura::where('numero_factura', $numeroFactura)->firstOrFail();

        // Lógica de autorización:
        // Asegúrate de que solo el propietario de la factura o un administrador puedan descargarla.
        if (Auth::check() && $factura->user_id !== null && Auth::id() !== $factura->user_id) {
            abort(403, 'Acceso no autorizado para descargar esta factura. No eres el propietario.');
        }
        // Si no hay usuario autenticado y la descarga requiere autenticación
        else if (!Auth::check() && $factura->user_id !== null) { // Requiere que la factura tenga user_id para esta restricción
            abort(401, 'Debes estar autenticado para descargar esta factura.');
        }

        // Llamar al método genérico de generación de PDF
        return $this->generatePdfResponse($factura);
    }

    /**
     * Método privado para generar el PDF de la factura.
     * Encapsula toda la lógica de TCPDF.
     *
     * @param Factura $factura La instancia de la factura para la cual generar el PDF.
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
        // PDF_HEADER_LOGO y PDF_HEADER_LOGO_WIDTH deben estar definidos en config/tcpdf.php o definirlos aquí
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
        // Usamos loadMissing para cargar las relaciones si no están ya cargadas
        $factura->loadMissing(['ticket.ticketItems.product']);

        $ticket = $factura->ticket; // Acceder al ticket asociado
        // Acceder a los ítems del ticket a través de la relación ya cargada, o una colección vacía si no hay ticket.
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

        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Nombre del archivo PDF
        $fileName = 'comprobante_compra_' . ($factura->numero_factura ?? $factura->id) . '.pdf';

        // Salida del PDF: 'I' para mostrar en el navegador, 'D' para descargar
        return $pdf->Output($fileName, 'I');
    }
}
