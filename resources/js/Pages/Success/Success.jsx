// resources/js/Pages/Checkout/Success.jsx

import React, { useState } from 'react';
import Layout from '@/Layouts/Layout';
import { Head, Link, usePage } from '@inertiajs/react';
import BannerHero from '@/Components/Hero/BannerHero';

const Success = ({ order_number, payment_method, factura_id, numero_factura, total_amount }) => {
    const [downloadErrorMessage, setDownloadErrorMessage] = useState('');

    const { auth } = usePage().props;
    const userIsLoggedIn = auth && auth.user;

    const handleDownloadInvoice = (facturaId, numeroFactura) => {
        setDownloadErrorMessage(''); // Limpiar cualquier mensaje de error previo
        let urlToOpen = null;

        // Prioriza 'numero_factura' para la URL
        if (numeroFactura) {
            urlToOpen = `/invoice/numero/${numeroFactura}/download`; // Asume esta ruta en Laravel
        } else if (facturaId) {
            // Como fallback, usa el factura_id
            urlToOpen = `/invoice/${facturaId}/download`; // Asume esta ruta en Laravel
        }

        if (urlToOpen) {
            window.open(urlToOpen, '_blank');
        } else {
            // Muestra un mensaje de error en la interfaz de usuario
            setDownloadErrorMessage('No se encontró información de factura para descargar. Por favor, intente recargar la página o contacte a soporte.');
            console.error('No se encontró información de factura para descargar.');
        }
    };

    let statusMessage = '';
    let instructionsMessage = '';

    if (payment_method === 'in-store') {
        statusMessage = `¡Compra exitosa! Tu pedido #${order_number} ha sido generado.`;
        instructionsMessage = (
            <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
                <p className="font-bold">Importante:</p>
                <p>Lleva tu <strong className="font-bold">número de orden ({order_number})</strong> a la caja para cancelar y retirar tus entradas.</p>
                {total_amount !== undefined && total_amount !== null && (
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
        statusMessage = `¡Compra exitosa! Tu pedido #${order_number} ha sido procesado.`;
        instructionsMessage = (
            <div className="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4 mb-6 rounded">
                <p className="font-bold">Instrucciones:</p>
                <p>Tu orden ha sido generada con éxito. Por favor, sigue las instrucciones de tu método de pago.</p>
            </div>
        );
    }

    return (
        <Layout>
            <Head title="Pedido Confirmado" />
            <div className="container mx-auto p-4 md:p-8 text-center min-h-screen flex items-center justify-center">
                <div className="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
                    <svg className="mx-auto h-24 w-24 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h1 className="text-3xl font-bold text-gray-800 mt-4 mb-2">{statusMessage}</h1>
                    <p className="text-gray-600 mb-4">¡Gracias por tu compra!</p>

                    {downloadErrorMessage && (
                        <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                            <p className="font-bold">Error al descargar:</p>
                            <p>{downloadErrorMessage}</p>
                            {userIsLoggedIn && (
                                <p className="mt-2">También puedes descargar tu comprobante desde la sección "<Link href="/profile/my-orders" className="underline font-semibold">Mis Pedidos</Link>" en tu perfil de usuario.</p>
                            )}
                        </div>
                    )}

                    <div className="text-left bg-gray-100 p-4 rounded-md mb-6">
                        <p className="text-lg font-semibold text-gray-700">Detalles de tu Pedido:</p>
                        <p><strong>Número de Orden:</strong> #{order_number}</p>
                        {total_amount !== undefined && total_amount !== null && (
                            <p><strong>Monto Total:</strong> ${parseFloat(total_amount).toFixed(2)}</p>
                        )}
                        <p><strong>Método de Pago:</strong> {payment_method === 'in-store' ? 'Pago en Caja' : 'Pago Móvil'}</p>
                        {numero_factura && <p><strong>Número de Factura:</strong> {numero_factura}</p>}
                    </div>

                    {instructionsMessage}

                    {/* El botón de descarga ahora se muestra siempre, sin condición */}
                    <div className="mt-4">
                        <button
                            onClick={() => handleDownloadInvoice(factura_id, numero_factura)}
                            className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Descargar Comprobante
                        </button>
                    </div>

                    {userIsLoggedIn && !downloadErrorMessage && (
                        <div className="bg-blue-50 border-l-4 border-blue-300 text-blue-700 p-3 mb-6 rounded text-sm">
                            <p>¡Hola, {auth.user.name}! Puedes encontrar y descargar este y otros comprobantes en la sección "<Link href="/profile/my-orders" className="underline font-semibold">Mis Pedidos</Link>" de tu perfil.</p>
                        </div>
                    )}

                    <div className="flex flex-col sm:flex-row justify-center gap-4 mt-8">
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
