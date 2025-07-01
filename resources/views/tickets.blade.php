@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Tickets Creados</h1>

        @if ($tickets->isEmpty())
            <p class="text-gray-600">No hay tickets creados aún. ¡Es hora de empezar a vender!</p>
        @else
            <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Número de Orden
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Número Factura
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método de Pago
                            </th>
                            {{-- Nuevas columnas para Pago Móvil --}}
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Banco Remitente
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teléfono Remitente
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cédula Remitente
                            </th>
                            {{-- Nuevas columnas para Tarjeta de Crédito/Débito --}}
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Número Tarjeta
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tarjetahabiente
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vencimiento
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CVV
                            </th>
                            {{-- Columna para Referencia de Pago (común para ambos) --}}
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ref. Pago
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Monto Total
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Código Promocional
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($tickets as $ticket)
                            @php
                                // Asegúrate de que $ticket->factura esté cargado.
                                // Si no está cargado por defecto, podrías necesitar eager loading en el controlador:
                                // Ticket::with('factura')->get();
                                $relatedFactura = $ticket->factura; // Asumiendo que la relación 'factura' existe en el modelo Ticket
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $ticket->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $relatedFactura->numero_factura ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($ticket->payment_method === 'mobile-payment')
                                        Pago Móvil
                                    @elseif ($ticket->payment_method === 'credit-debit-card')
                                        Tarjeta Crédito/Débito
                                    @else
                                        {{ $ticket->payment_method ?? 'N/A' }}
                                    @endif
                                </td>

                                {{-- Contenido condicional para Pago Móvil --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $relatedFactura->banco_remitente ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $relatedFactura->numero_telefono_remitente ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $relatedFactura->cedula_remitente ?? 'N/A' }}
                                </td>

                                {{-- Contenido condicional para Tarjeta de Crédito/Débito --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{-- Muestra solo los últimos 4 dígitos o un identificador enmascarado por seguridad --}}
                                    @if ($relatedFactura->card_number)
                                        **** **** **** {{ substr($relatedFactura->card_number, -4) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $relatedFactura->card_holder_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($relatedFactura->card_expiry_month && $relatedFactura->card_expiry_year)
                                        {{ $relatedFactura->card_expiry_month }}/{{ $relatedFactura->card_expiry_year }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{-- CVV NO debe mostrarse, solo para fines de desarrollo si es absolutamente necesario --}}
                                    {{-- En producción, este campo debe ser altamente protegido y no visible --}}
                                    {{ $relatedFactura->card_cvv ? '***' : 'N/A' }}
                                </td>

                                {{-- Columna para Referencia de Pago (aplica a ambos) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $relatedFactura->numero_referencia_pago ?? 'N/A' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->status }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($ticket->monto_total, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->promo_code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if ($ticket->status === 'validado')
                                        <span
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-green-700 bg-green-100">
                                            &#10003; Validado
                                        </span>
                                    @else
                                        <form action="{{ route('tickets.validate', $ticket->id) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de que quieres validar este ticket?');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Validar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
@endsection
