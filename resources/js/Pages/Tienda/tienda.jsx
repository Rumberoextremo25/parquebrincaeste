import React, { useState, useEffect } from 'react';
import { Inertia } from '@inertiajs/inertia';
import Layout from "@/Layouts/Layout";
import BannerHero from "@/Components/Hero/BannerHero";

const Tienda = ({ precioInicial }) => {
    const [cantidad, setCantidad] = useState(1);
    const [fecha, setFecha] = useState('');
    const [hora, setHora] = useState('');
    const [tipoTicket, setTipoTicket] = useState('');
    const [talla, setTalla] = useState('');
    const [precio, setPrecio] = useState(precioInicial);
    const [cantidadMedias, setCantidadMedias] = useState('');
    const [tallaMedias, setTallaMedias] = useState('');
    const [precioMedias] = useState(2);
    const [mensaje, setMensaje] = useState('');

    useEffect(() => {
        if (fecha) {
            const day = new Date(fecha).getDay();
            const nuevoPrecio = (day === 5 || day === 6 || day === 0) ? 6 : 5;
            setPrecio(nuevoPrecio);
        }
    }, [fecha]);

    const handleSubmit = (e) => {
        e.preventDefault();
        const data = {
            cantidad,
            fecha,
            hora,
            tipoTicket,
            talla,
            cantidadMedias: cantidadMedias || 0,
            tallaMedias: tallaMedias || '',
            precioMedias,
        };

        // Calcular el total
        const totalMedias = (precioMedias * (cantidadMedias || 0));
        const total = (precio * cantidad) + totalMedias;

        // Reinicia los estados
        setCantidad(1);
        setFecha('');
        setHora('');
        setTipoTicket('');
        setTalla('');
        setCantidadMedias('');
        setTallaMedias('');

        // Redirigir a la página de checkout
        Inertia.visit('/checkout', {
            method: 'POST',
            data: data,
        });
    };

    return (
        <Layout>
            <BannerHero img="/img/home/BrincaEste.jpg" title="COMPRA TUS ENTRADAS AQUÍ!" />
            <div className="py-8 container mx-auto">
                <div className="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
                    <form onSubmit={handleSubmit} className="p-6">
                        <h2 className="text-xl font-bold text-gray-800 mb-4">Detalles del Producto de Ticket</h2>

                        <div className="form-group mb-4">
                            <label htmlFor="cantidad" className="block text-gray-700">Cantidad:</label>
                            <input
                                type="number"
                                id="cantidad"
                                className="mt-1 block w-full p-2 border rounded"
                                value={cantidad}
                                min="1"
                                onChange={(e) => setCantidad(Number(e.target.value))}
                                required
                            />
                        </div>

                        <div className="form-group mb-4">
                            <label htmlFor="cantidadMedias" className="block text-gray-700">Cantidad de Medias (opcional):</label>
                            <input
                                type="number"
                                id="cantidadMedias"
                                className="mt-1 block w-full p-2 border rounded"
                                value={cantidadMedias}
                                min="0"
                                onChange={(e) => setCantidadMedias(Number(e.target.value))}
                            />
                        </div>

                        <div className="form-group mb-4">
                            <label htmlFor="tallaMedias" className="block text-gray-700">Talla de Medias:</label>
                            <select
                                id="tallaMedias"
                                className="mt-1 block w-full p-2 border rounded"
                                value={tallaMedias}
                                onChange={(e) => setTallaMedias(e.target.value)}
                            >
                                <option value="">Seleccione la talla de medias</option>
                                <option value="S">S (34)</option>
                                <option value="M">M (36)</option>
                                <option value="L">L (38)</option>
                                <option value="XL">XL (40)</option>
                                <option value="2XL">2XL (42)</option>
                                <option value="3XL">3XL (44)</option>
                            </select>
                        </div>

                        <div className="form-group mb-4">
                            <label htmlFor="fecha" className="block text-gray-700">Fecha:</label>
                            <input
                                type="date"
                                id="fecha"
                                className="mt-1 block w-full p-2 border rounded"
                                value={fecha}
                                onChange={(e) => setFecha(e.target.value)}
                                required
                            />
                        </div>

                        <div className="form-group mb-4">
                            <label htmlFor="hora" className="block text-gray-700">Hora:</label>
                            <input
                                type="time"
                                id="hora"
                                className="mt-1 block w-full p-2 border rounded"
                                value={hora}
                                onChange={(e) => setHora(e.target.value)}
                                required
                            />
                        </div>

                        <div className="form-group mb-4">
                            <label htmlFor="tipoTicket" className="block text-gray-700">Tipo de Ticket:</label>
                            <select
                                id="tipoTicket"
                                className="mt-1 block w-full p-2 border rounded"
                                value={tipoTicket}
                                onChange={(e) => setTipoTicket(e.target.value)}
                                required
                            >
                                <option value="">Seleccione el tipo de ticket</option>
                                <option value="plataforma">Plataforma</option>
                                <option value="baby_park">Baby Park</option>
                            </select>
                        </div>

                        <div className="mb-4">
                            <p className="text-gray-700">Precio por ticket: <span className="font-bold">${precio}</span></p>
                            <p className="text-gray-700">Precio por medias: <span className="font-bold">${precioMedias}</span></p>
                            <p className="text-gray-700">Precio total: <span className="font-bold">${(precio * cantidad) + (precioMedias * (cantidadMedias || 0))}</span></p>
                        </div>

                        <button type="submit" className="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                            Comprar
                        </button>
                    </form>
                    {mensaje && <p className="p-4 text-green-600">{mensaje}</p>}
                </div>
            </div>
        </Layout>
    );
};

export default Tienda;
