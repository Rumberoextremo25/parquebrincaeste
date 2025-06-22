import React from 'react';
import Layout from '@/Layouts/Layout';
import { Head, Link } from '@inertiajs/react';

const Success = ({ order_number, payment_method, invoice_id, total_amount }) => { // Updated props
    let statusMessage = '';
    let instructionsMessage = '';

    // Determine the status and instructions message based on the payment method
    if (payment_method === 'in-store') {
        statusMessage = `¡Compra exitosa! Tu pedido #${order_number} ha sido generado.` ;
        instructionsMessage = (
            <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p className="font-bold">Importante:</p>
                <p>Lleva tu **número de orden ({order_number})** a la caja para cancelar y retirar tus entradas.</p>
                {total_amount && <p className="mt-2">Monto pendiente a cancelar: **${total_amount.toFixed(2)}**</p>}
            </div>
        );
    } else if (payment_method === 'mobile-payment') {
        statusMessage = `¡Compra exitosa! Tu pago móvil para el pedido #${order_number} está en proceso.`;
        instructionsMessage = (
            <div className="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <p className="font-bold">Confirmación:</p>
                <p>Lleva tu **comprobante de pago y número de orden ({order_number})** a la caja y retira tus entradas.</p>
                <p className="mt-2">Recibirás una confirmación adicional cuando se verifique tu pago.</p>
            </div>
        );
    } else {
        // Fallback for any other unexpected payment method
        statusMessage = `¡Compra exitosa! Tu pedido #${order_number} ha sido procesado.`;
        instructionsMessage = (
             <div className="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4 mb-6" role="alert">
                <p className="font-bold">Instrucciones:</p>
                <p>Tu orden ha sido generada con éxito. Por favor, sigue las instrucciones de tu método de pago.</p>
            </div>
        );
    }

    // Function to handle the invoice view button click
    const handleViewInvoice = () => {
        if (invoice_id) {
            // Manually construct the URL.
            // Assumes your Laravel app is served from the root of the domain.
            // The route is defined as /invoice/{id}/download
            const invoiceUrl = `/invoice/${invoice_id}/download`; // <-- THIS IS THE CHANGE

            window.open(invoiceUrl, '_blank');
        } else {
            alert('No se encontró ID de factura para visualizar.');
        }
    };

    return (
        <Layout>
            <Head title="Pedido Confirmado" /> {/* Updated page title */}
            <div className="container mx-auto p-4 md:p-8 text-center min-h-screen flex items-center justify-center">
                <div className="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
                    <svg className="mx-auto h-24 w-24 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 className="text-3xl font-bold text-gray-800 mt-4 mb-2">{statusMessage}</h1>
                    <p className="text-gray-600 mb-4">¡Gracias por tu compra!</p>

                    <div className="text-left bg-gray-100 p-4 rounded-md mb-6">
                        <p className="text-lg font-semibold text-gray-700">Detalles de tu Pedido:</p>
                        <p><strong>Número de Orden:</strong> #{order_number}</p>
                        {total_amount && <p><strong>Monto Total:</strong> ${total_amount.toFixed(2)}</p>}
                        <p><strong>Método de Pago:</strong> {payment_method === 'in-store' ? 'Pago en Caja' : 'Pago Móvil'}</p>
                    </div>

                    {instructionsMessage} {/* Display the dynamic instructions */}

                    <div className="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                        {invoice_id && ( // Only show the button if invoice_id is provided
                            <button
                                onClick={handleViewInvoice}
                                className="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 flex items-center justify-center"
                            >
                                <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Ver Comprobante
                            </button>
                        )}
                        <Link href="/" className="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 flex items-center justify-center">
                            Volver a la Tienda
                        </Link>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Success;
