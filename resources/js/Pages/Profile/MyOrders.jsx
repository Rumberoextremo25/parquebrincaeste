import React, { useEffect, useState } from 'react';  
import axios from 'axios';  
import BannerHero from "@/Components/Hero/BannerHero";  
import ValidationErrors from "@/Components/ValidationErrors";  
import Layout from "@/Layouts/Layout";  
import { Link, usePage } from "@inertiajs/react";  

const MyOrders = () => {  
    const { errors, auth, app } = usePage().props; // Importante: Extrae 'app' de las props  
    const [orders, setOrders] = useState([]);  
    const [loading, setLoading] = useState(true);  
    const [error, setError] = useState(null);  

    useEffect(() => {  
        const fetchOrders = async () => {  
            try {  
                const response = await axios.get('/api/my-orders'); // Cambia la URL según tu API  
                setOrders(response.data);  
            } catch (err) {  
                setError('Error al cargar las órdenes');  
            } finally {  
                setLoading(false);  
            }  
        };  

        fetchOrders();  
    }, []);  

    return (  
        <Layout title="Mis Compras" app={app}> {/* Pasa 'app' al componente Layout */}  
            <BannerHero title={`Bienvenido, ${auth.user.name}`} />  
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
                        </div>  
                    </div>  
                    <div className="col-span-12 md:col-span-9">  
                        <div>  
                            <ValidationErrors errors={errors} />  
                            <div className="border-b pb-4 mb-4">  
                                <h2 className="text-2xl font-semibold">Mis Compras</h2>  
                            </div>  
                            {loading ? (  
                                <div>Cargando...</div>  
                            ) : error ? (  
                                <div>{error}</div>  
                            ) : (  
                                <ul>  
                                    {orders.map(order => (  
                                        <li key={order.id} className="mb-4">  
                                            <h2>Orden #{order.id}</h2>  
                                            <p>Fecha: {new Date(order.date).toLocaleDateString()}</p>  
                                            <p>Total: ${order.total}</p>  
                                        </li>  
                                    ))}  
                                </ul>  
                            )}  
                        </div>  
                    </div>  
                </div>  
            </div>  
        </Layout>  
    );  
};  

export default MyOrders;
