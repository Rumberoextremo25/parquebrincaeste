// resources/js/Pages/Checkout/Success.jsx

import React, { useState } from 'react'; // Importamos useState
import Layout from '@/Layouts/Layout';
import { Head, Link } from '@inertiajs/react';

// Recibe 'factura_id' y 'numero_factura' de las props del backend
const Success = ({ order_number, payment_method, factura_id, numero_factura, total_amount }) => {
    // Definimos un estado local para el mensaje de error si no hay ID/número de factura para descargar
    const [downloadErrorMessage, setDownloadErrorMessage] = useState('');

    let statusMessage = '';
    let instructionsMessage = '';

    // Lógica para determinar el mensaje de estado e instrucciones basada en el método de pago
    if (payment_method === 'in-store') {
        statusMessage = `¡Compra exitosa! Tu pedido #${order_number} ha sido generado.`;
        instructionsMessage = (
            <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
                <p className="font-bold">Importante:</p>
                <p>Lleva tu <strong className="font-bold">número de orden ({order_number})</strong> a la caja para cancelar y retirar tus entradas.</p>
                {total_amount !== undefined && total_amount !== null && ( // Verificación más robusta para total_amount
                    <p className="mt-2">Monto pendiente a cancelar: <strong className="font-bold">${total_amount.toFixed(2)}</strong></p>
                )}
            </div>
        );
    } else if (payment_method === 'mobile-payment') {
        statusMessage = `¡Compra exitosa! Tu pago móvil para el pedido #${order_number} está en proceso.`;
        instructionsMessage = (
            <div className="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
                <p className="font-bold">Confirmación:</p>
                <p>Lleva tu <strong className="font-bold">comprobante de pago y número de orden ({order_number})</strong> a la caja y retira tus entradas.</p>
                <p className="mt-2">Recibirás una confirmación adicional cuando se verifique tu pago.</p>
            </div>
        );
    } else {
        // Fallback para cualquier otro método de pago inesperado
        statusMessage = `¡Compra exitosa! Tu pedido #${order_number} ha sido procesado.`;
        instructionsMessage = (
            <div className="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4 mb-6 rounded">
                <p className="font-bold">Instrucciones:</p>
                <p>Tu orden ha sido generada con éxito. Por favor, sigue las instrucciones de tu método de pago.</p>
            </div>
        );
    }

    // Función para manejar el clic del botón de ver/descargar comprobante
    const handleViewInvoice = () => {
        setDownloadErrorMessage(''); // Limpiar cualquier mensaje de error previo
        let urlToOpen = null;

        // Prioriza el 'numero_factura' para construir la URL, ya que es más amigable
        if (numero_factura) {
            // Construye la URL para la ruta de descarga por número de factura
            // Coincide con Route::get('/invoice/numero/{numero_factura}/download', ...) en web.php
            urlToOpen = `/invoice/numero/${numero_factura}/download`;
        } else if (factura_id) {
            // Como fallback, si no hay numero_factura, usa el factura_id
            // Coincide con Route::get('/invoice/{factura}/download', ...) en web.php
            urlToOpen = `/invoice/${factura_id}/download`;
        }

        if (urlToOpen) {
            // Abre el PDF en una nueva pestaña del navegador
            window.open(urlToOpen, '_blank');
        } else {
            // Muestra un mensaje de error en la interfaz de usuario si no se encontró información
            setDownloadErrorMessage('No se encontró información de factura (ID o número) para visualizar. Por favor, intente recargar la página o contacte a soporte.');
            console.error('No se pudo construir la URL de descarga de la factura. factura_id:', factura_id, 'numero_factura:', numero_factura);
        }
    };

    return (
        <Layout>
            <Head title="Pedido Confirmado" />
            <div className="container mx-auto p-4 md:p-8 text-center min-h-screen flex items-center justify-center">
                <div className="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
                    {/* Icono de éxito */}
                    <svg className="mx-auto h-24 w-24 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 className="text-3xl font-bold text-gray-800 mt-4 mb-2">{statusMessage}</h1>
                    <p className="text-gray-600 mb-4">¡Gracias por tu compra!</p>

                    {/* Mensaje de error para la descarga (si aplica) */}
                    {downloadErrorMessage && (
                        <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                            <p className="font-bold">Error al descargar:</p>
                            <p>{downloadErrorMessage}</p>
                        </div>
                    )}

                    <div className="text-left bg-gray-100 p-4 rounded-md mb-6">
                        <p className="text-lg font-semibold text-gray-700">Detalles de tu Pedido:</p>
                        <p><strong>Número de Orden:</strong> #{order_number}</p>
                        {total_amount !== undefined && total_amount !== null && ( // Verificación para total_amount
                            <p><strong>Monto Total:</strong> ${parseFloat(total_amount).toFixed(2)}</p>
                        )}
                        <p><strong>Método de Pago:</strong> {payment_method === 'in-store' ? 'Pago en Caja' : 'Pago Móvil'}</p>
                        {numero_factura && <p><strong>Número de Factura:</strong> {numero_factura}</p>} {/* Muestra el número de factura */}
                    </div>

                    {instructionsMessage} {/* Muestra las instrucciones dinámicas */}

                    <div className="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                        {/* El botón de descarga ahora SIEMPRE se muestra */}
                        <button
                            onClick={handleViewInvoice}
                            className="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 flex items-center justify-center"
                        >
                            <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 0 11-6 0 3 3 0 016 0z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Ver Comprobante
                        </button>
                        {/* Botón para volver a la tienda */}
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
