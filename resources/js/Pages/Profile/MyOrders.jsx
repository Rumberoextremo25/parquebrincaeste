// resources/js/Pages/MyOrders.jsx

import React, { useEffect, useState } from 'react';
import axios from 'axios';
import BannerHero from "@/Components/Hero/BannerHero";
import ValidationErrors from "@/Components/ValidationErrors";
import Layout from "@/Layouts/Layout";
import { Link, usePage } from "@inertiajs/react";

const MyOrders = () => {
    // Extrae 'auth' de las props de la página para acceder al usuario autenticado
    const { errors, auth } = usePage().props;
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    // Nuevo estado para el mensaje de error de descarga
    const [downloadErrorMessage, setDownloadErrorMessage] = useState('');

    // Función para manejar la descarga del comprobante (similar a Success.jsx)
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
            // Muestra un mensaje de error en la interfaz de usuario en lugar de un alert()
            setDownloadErrorMessage('No se encontró información de factura para descargar. Por favor, intente recargar la página o contacte a soporte.');
            console.error('No se encontró información de factura para descargar.');
        }
    };

    useEffect(() => {
        const fetchOrders = async () => {
            // Verifica si el usuario está autenticado en el frontend antes de intentar cargar las órdenes
            // Si auth.user es null, no tiene sentido hacer la petición API
            if (!auth.user) {
                setError('No estás autenticado para ver tus órdenes. Por favor, inicia sesión.');
                setLoading(false);
                return;
            }

            try {
                // La URL de la API que creaste en Laravel (`routes/api.php`)
                // Debería ser `/api/my-tickets` según tu controlador
                const response = await axios.get('/api/my-tickets', {
                    // Esto es CRUCIAL para la autenticación basada en sesión/Sanctum
                    // Asegura que las cookies de sesión y el token XSRF-TOKEN se envíen
                    withCredentials: true,
                });
                setOrders(response.data);
            } catch (err) {
                console.error("Error al cargar las órdenes:", err);
                // Si el error es 401 (Unauthenticated), muestra un mensaje específico
                if (err.response && err.response.status === 401) {
                    setError('Tu sesión ha expirado o no estás autorizado. Por favor, inicia sesión nuevamente.');
                } else {
                    setError(err.response?.data?.message || 'Error al cargar las órdenes. Inténtelo de nuevo más tarde.');
                }
            } finally {
                setLoading(false);
            }
        };

        // Llama a la función para cargar órdenes cuando el componente se monta
        // o cuando el estado de autenticación del usuario cambia.
        fetchOrders();
    }, [auth.user]); // El efecto se ejecuta cuando auth.user cambia

    return (
        <Layout title="Mis Compras">
            {/* El título del BannerHero debería acceder a auth.user de forma segura */}
            <BannerHero title={`Bienvenido, ${auth.user ? auth.user.name : 'Invitado'}`} />
            <div className="container py-section">
                <div className="grid grid-cols-12 md:gap-4 gap-y-10">
                    <div className="col-span-12 md:col-span-3">
                        <h3 className="text-3xl font-primary font-bold">Mi cuenta</h3>
                        <div className="flex flex-col mt-6">
                            <Link
                                href={route("profile.my_account")}
                                preserveScroll
                                className="block py-3 pl-4 border-l-4 font-medium border-primary-100 hover:border-blue-500 hover:text-blue-500"
                            >
                                Dashboard
                            </Link>
                            <Link
                                href={route("profile.account_details")}
                                preserveScroll
                                className="block py-3 pl-4 border-l-4 font-medium border-primary-100 hover:border-blue-500 hover:text-blue-500"
                            >
                                Detalles de cuenta
                            </Link>
                            <Link
                                href={route("profile.change_password")}
                                preserveScroll
                                className="block py-3 pl-4 border-l-4 font-medium border-primary-100 hover:border-blue-500 hover:text-blue-500"
                            >
                                Cambiar contraseña
                            </Link>
                            {/* Enlace para "Mis Compras" */}
                            <Link
                                href={route("profile.my_orders")} // Asumiendo que esta es la ruta a este componente
                                preserveScroll
                                className="block py-3 pl-4 border-l-4 font-medium border-blue-500 text-blue-500" // Estilo para indicar que es la página activa
                            >
                                Mis Compras
                            </Link>
                        </div>
                    </div>
                    <div className="col-span-12 md:col-span-9">
                        <div>
                            <ValidationErrors errors={errors} />
                            {/* Mensaje de error para la descarga (si aplica) */}
                            {downloadErrorMessage && (
                                <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                                    <p className="font-bold">Error al descargar:</p>
                                    <p>{downloadErrorMessage}</p>
                                </div>
                            )}
                            <div className="border-b pb-4 mb-4">
                                <h2 className="text-2xl font-semibold">Mis Compras</h2>
                            </div>
                            {/* Manejo de estados de carga, error y órdenes */}
                            {loading ? (
                                <div className="text-gray-600">Cargando órdenes...</div>
                            ) : error ? (
                                <div className="text-red-500 p-4 bg-red-50 rounded-md border border-red-200">{error}</div>
                            ) : orders.length === 0 ? (
                                <div className="text-gray-600 p-4 bg-gray-50 rounded-md border border-gray-200">No tienes órdenes realizadas aún.</div>
                            ) : (
                                <div className="space-y-6">
                                    {orders.map(order => (
                                        <div key={order.id} className="bg-white shadow rounded-lg p-6 border border-gray-200">
                                            <div className="flex justify-between items-center mb-4">
                                                <h3 className="text-xl font-bold text-gray-800">Orden #{order.order_number}</h3>
                                                <span className={`px-3 py-1 text-sm font-semibold rounded-full ${
                                                    order.status === 'pending_payment_cash' ? 'bg-yellow-100 text-yellow-800' :
                                                    order.status === 'pending_payment_mobile' ? 'bg-orange-100 text-orange-800' :
                                                    order.status === 'completed' ? 'bg-green-100 text-green-800' :
                                                    'bg-gray-100 text-gray-800'
                                                }`}>
                                                    {order.status.replace(/_/g, ' ').toUpperCase()} {/* Formatea el status */}
                                                </span>
                                            </div>
                                            <p className="text-gray-600 mb-2"><strong>Fecha:</strong> {order.created_at}</p>
                                            <p className="text-gray-600 mb-2"><strong>Método de Pago:</strong> {order.payment_method === 'in-store' ? 'Pago en Caja' : 'Pago Móvil'}</p>
                                            {/* SOLUCIÓN: Convertir monto_total a float antes de toFixed */}
                                            <p className="text-gray-800 text-lg font-bold mb-4">
                                                <strong>Total:</strong> ${order.monto_total ? parseFloat(order.monto_total).toFixed(2) : '0.00'}
                                            </p>

                                            <h4 className="text-md font-semibold text-gray-700 mb-2">Items de la Orden:</h4>
                                            <ul className="list-disc list-inside mb-4 space-y-1 text-gray-700">
                                                {order.items.length > 0 ? (
                                                    order.items.map(item => (
                                                        <li key={item.product_id || item.id}> {/* Usa item.id como fallback si product_id no es único */}
                                                            {/* SOLUCIÓN: Convertir item.price y item.subtotal a float antes de toFixed */}
                                                            {item.product_name} (x{item.quantity}) - ${item.price ? parseFloat(item.price).toFixed(2) : '0.00'} cada uno. Subtotal: ${item.subtotal ? parseFloat(item.subtotal).toFixed(2) : '0.00'}
                                                        </li>
                                                    ))
                                                ) : (
                                                    <li>No hay ítems para esta orden.</li>
                                                )}
                                            </ul>

                                            {/* Botón de descarga de comprobante */}
                                            {(order.factura_id || order.numero_factura) && ( // Muestra el botón si hay ID o número de factura
                                                <div className="mt-4">
                                                    <button
                                                        onClick={() => handleDownloadInvoice(order.factura_id, order.numero_factura)}
                                                        className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                                                    >
                                                        <svg className="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                        Descargar Comprobante
                                                    </button>
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default MyOrders;