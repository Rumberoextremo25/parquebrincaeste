// resources/js/Pages/MyOrders.jsx

import React, { useEffect, useState, useCallback } from 'react'; // Agregamos useCallback
import axios from 'axios';
import BannerHero from "@/Components/Hero/BannerHero";
import ValidationErrors from "@/Components/ValidationErrors";
import Layout from "@/Layouts/Layout";
import { Link, usePage } from "@inertiajs/react";

const MyOrders = () => {
    const { errors, auth } = usePage().props;
    const [orders, setOrders] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [downloadErrorMessage, setDownloadErrorMessage] = useState('');

    // --- NUEVOS ESTADOS PARA PAGINACIÓN ---
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [paginationLinks, setPaginationLinks] = useState([]);
    // --- FIN NUEVOS ESTADOS PARA PAGINACIÓN ---

    const handleDownloadInvoice = (facturaId, numeroFactura) => {
        setDownloadErrorMessage('');
        let urlToOpen = null;

        if (numeroFactura) {
            urlToOpen = `/invoice/numero/${numeroFactura}/download`;
        } else if (facturaId) {
            urlToOpen = `/invoice/${facturaId}/download`;
        }

        if (urlToOpen) {
            window.open(urlToOpen, '_blank');
        } else {
            setDownloadErrorMessage('No se encontró información de factura para descargar. Por favor, intente recargar la página o contacte a soporte.');
            console.error('No se encontró información de factura para descargar.');
        }
    };

    // Modificamos fetchOrders para aceptar el número de página
    const fetchOrders = useCallback(async (page = 1) => { // Establecemos la página por defecto en 1
        if (!auth.user) {
            setError('No estás autenticado para ver tus órdenes. Por favor, inicia sesión.');
            setLoading(false);
            return;
        }

        setLoading(true); // Siempre que se inicie una nueva búsqueda, se carga
        setError(null); // Limpiamos errores previos

        try {
            // Envía el parámetro 'page' en la URL
            const response = await axios.get(`/api/my-tickets?page=${page}`, {
                withCredentials: true,
            });

            // Actualizamos los estados con los datos paginados
            setOrders(response.data.data); // 'data' contiene los ítems reales
            setCurrentPage(response.data.current_page);
            setLastPage(response.data.last_page);
            // Filtramos los enlaces para solo mostrar 'next', 'prev' y los números de página
            const filteredLinks = response.data.links.filter(link =>
                link.url !== null && link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'
            ).map(link => ({
                ...link,
                label: link.label.replace(/&laquo; Previous|Next &raquo;/g, '') // Limpiar los labels si aún quedan
            }));
            setPaginationLinks(filteredLinks);


        } catch (err) {
            console.error("Error al cargar las órdenes:", err);
            if (err.response && err.response.status === 401) {
                setError('No tienes Ticket aun en tu cuenta.');
            } else {
                setError(err.response?.data?.message || 'Error al cargar las órdenes. Inténtelo de nuevo más tarde.');
            }
        } finally {
            setLoading(false);
        }
    }, [auth.user]); // El efecto se ejecuta cuando auth.user cambia

    // useEffect para cargar las órdenes iniciales o cuando cambia la página
    useEffect(() => {
        fetchOrders(currentPage);
    }, [currentPage, fetchOrders]); // Dependencias: currentPage y fetchOrders

    // Función para manejar el cambio de página
    const handlePageChange = (url) => {
        // Extraemos el número de página de la URL
        const pageNumber = new URL(url).searchParams.get('page');
        if (pageNumber) {
            setCurrentPage(parseInt(pageNumber));
        }
    };

    return (
        <Layout title="Mis Compras">
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
                            <Link
                                href={route("profile.my_orders")}
                                preserveScroll
                                className="block py-3 pl-4 border-l-4 font-medium border-blue-500 text-blue-500"
                            >
                                Mis Compras
                            </Link>
                        </div>
                    </div>
                    <div className="col-span-12 md:col-span-9">
                        <div>
                            <ValidationErrors errors={errors} />
                            {downloadErrorMessage && (
                                <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                                    <p className="font-bold">Error al descargar:</p>
                                    <p>{downloadErrorMessage}</p>
                                </div>
                            )}
                            <div className="border-b pb-4 mb-4">
                                <h2 className="text-2xl font-semibold">Mis Compras</h2>
                            </div>
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
                                                    {order.status.replace(/_/g, ' ').toUpperCase()}
                                                </span>
                                            </div>
                                            <p className="text-gray-600 mb-2"><strong>Fecha:</strong> {new Date(order.created_at).toLocaleDateString()}</p> {/* Formatear fecha */}
                                            <p className="text-gray-600 mb-2"><strong>Método de Pago:</strong> {order.payment_method === 'in-store' ? 'Pago en Caja' : 'Pago Móvil'}</p>
                                            <p className="text-gray-800 text-lg font-bold mb-4">
                                                <strong>Total:</strong> ${order.monto_total ? parseFloat(order.monto_total).toFixed(2) : '0.00'}
                                            </p>

                                            <h4 className="text-md font-semibold text-gray-700 mb-2">Items de la Orden:</h4>
                                            <ul className="list-disc list-inside mb-4 space-y-1 text-gray-700">
                                                {order.items.length > 0 ? (
                                                    order.items.map(item => (
                                                        <li key={item.product_id || item.id}>
                                                            {item.product_name} (x{item.quantity}) - ${item.price ? parseFloat(item.price).toFixed(2) : '0.00'} cada uno. Subtotal: ${item.subtotal ? parseFloat(item.subtotal).toFixed(2) : '0.00'}
                                                        </li>
                                                    ))
                                                ) : (
                                                    <li>No hay ítems para esta orden.</li>
                                                )}
                                            </ul>

                                            {(order.factura_id || order.numero_factura) && (
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

                                    {/* --- COMPONENTES DE PAGINACIÓN --- */}
                                    {paginationLinks.length > 1 && ( // Solo muestra la paginación si hay más de una página
                                        <div className="flex justify-center mt-8">
                                            <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                                {paginationLinks.map((link, index) => (
                                                    <button
                                                        key={index}
                                                        onClick={() => link.url && handlePageChange(link.url)}
                                                        disabled={link.url === null} // Deshabilita si no hay URL (p.ej., página actual)
                                                        aria-current={link.active ? 'page' : undefined}
                                                        className={`relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
                                                            link.active
                                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                                        } ${
                                                            index === 0 ? 'rounded-l-md' : ''
                                                        } ${
                                                            index === paginationLinks.length - 1 ? 'rounded-r-md' : ''
                                                        }`}
                                                    >
                                                        {/* Renderiza los símbolos y números de página */}
                                                        {link.label === '&laquo;' ? 'Anterior' : link.label === '&raquo;' ? 'Siguiente' : link.label}
                                                    </button>
                                                ))}
                                            </nav>
                                        </div>
                                    )}
                                    {/* --- FIN COMPONENTES DE PAGINACIÓN --- */}
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