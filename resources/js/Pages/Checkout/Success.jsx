import Layout from '@/Layouts/Layout';
import React, { useEffect, useState } from 'react';
import BannerHero from '@/Components/Hero/BannerHero';

const Success = ({ paymentMethod }) => {
    const [invoice, setInvoice] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchInvoiceData = async () => {
            try {
                let response;
                // Cambiar las rutas según la configuración de Laravel
                if (paymentMethod === 'caja') {
                    // Llamada al controlador Checkout
                    response = await fetch('/api/checkout'); // Asegúrate de que esta ruta exista
                } else {
                    // Llamada al controlador Payment
                    response = await fetch('/api/payment'); // Asegúrate de que esta ruta exista
                }

                if (!response.ok) {
                    throw new Error('Error al cargar la información de la factura');
                }

                const data = await response.json();
                setInvoice(data);
            } catch (err) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchInvoiceData();
    }, [paymentMethod]);

    // Verificar si está cargando
    if (loading) return <p>Cargando...</p>;

    // Verificar si hubo un error
    if (error) return <p>Error: {error}</p>;

    // Verificar si invoice está definido
    const customerName = invoice?.customer_name || 'Cliente desconocido';
    const invoiceId = invoice?.id || 'ID no disponible';
    const amount = invoice?.amount || '0.00';
    const date = invoice?.date || 'Fecha no disponible';

    return (
        <Layout>
            <BannerHero
                img="https://wallpaperbat.com/img/423222-eagle-mountain-sunset-minimalist-1366x768-resolution.jpg"
                title="Confirmación de Compra"
            />
            <div className="flex justify-center items-center min-h-screen bg-gray-100">
                <div className="bg-white shadow-lg rounded-lg p-8 max-w-md mx-auto">
                    <h1 className="text-2xl font-bold text-center text-gray-800">Compra Exitosa</h1>
                    <p className="mt-4 text-center text-gray-600">Gracias por su compra, <strong>{customerName}</strong>.</p>
                    <p className="mt-2 text-center text-gray-600">Su factura ID es: <strong>{invoiceId}</strong></p>
                    <p className="mt-2 text-center text-gray-600">Monto Total: <strong>${amount.toFixed(2)}</strong></p>
                    <p className="mt-2 text-center text-gray-600">Fecha: <strong>{date}</strong></p>
                </div>
            </div>
        </Layout>
    );
};

export default Success;
