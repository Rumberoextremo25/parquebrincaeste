import React, { useState, useEffect } from "react";
import { Inertia } from "@inertiajs/inertia";
import Layout from "@/Layouts/Layout";
import BannerHero from "@/Components/Hero/BannerHero";
// --- Datos de PRODUCTOS (Simulado desde tu backend) ---
const PRODUCTS = [
    // // Brazaletes por hora (categoría: 'Brazalete')
    // { id: 1, name: 'Brazalete Azul', description: '11:00 AM a 12:00 M', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 2, name: 'Brazalete Amarillo', description: '12:00 M a 1:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 3, name: 'Brazalete Rojo', description: '1:00 PM a 2:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 4, name: 'Brazalete Verde Manzana', description: '2:00 PM a 3:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 5, name: 'Brazalete Naranja', description: '3:00 PM a 4:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 6, name: 'Brazalete Morado', description: '4:00 PM a 5:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 7, name: 'Brazalete Negro', description: '5:00 PM a 6:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 8, name: 'Brazalete Vinotinto', description: '6:00 PM a 7:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 9, name: 'Brazalete Azul Rey', description: '7:00 PM a 8:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // { id: 10, name: 'Brazalete Azul Marino', description: '8:00 PM a 9:00 PM', price: 15.00, category: 'Brazalete', onlyChildren: false, isRequiredOnce: false },
    // // Brazalete Baby Park (categoría: 'Baby Park')
    // { id: 11, name: 'Brazalete Baby Park', description: 'Todas las Horas', price: 12.00, category: 'Baby Park', onlyChildren: true, isRequiredOnce: false },
    // // Calcetines (categoría: 'Calcetines')
    // // Añadimos una propiedad `applicableTo` para definir a qué grupo de cliente aplica
    // { id: 12, name: 'Calcetín Talla 23-26', description: 'Medias para niños, talla 23-26.', price: 5.00, category: 'Calcetines', applicableTo: 'under6', isRequiredOnce: true },
    // { id: 13, name: 'Calcetín Talla 27-30', description: 'Medias para niños, talla 27-30.', price: 5.00, category: 'Calcetines', applicableTo: 'under6', isRequiredOnce: true },
    // { id: 14, name: 'Calcetín Talla 31-32', description: 'Medias para niños, talla 31-32.', price: 5.00, category: 'Calcetines', applicableTo: 'under6', isRequiredOnce: true },
    // { id: 15, name: 'Calcetín Talla S', description: 'Medias para mayores, talla S.', price: 5.00, category: 'Calcetines', applicableTo: 'adultOrOver6', isRequiredOnce: true },
    // { id: 16, name: 'Calcetín Talla M', description: 'Medias para mayores, talla M.', price: 5.00, category: 'Calcetines', applicableTo: 'adultOrOver6', isRequiredOnce: true },
    // { id: 17, name: 'Calcetín Talla L', description: 'Medias para mayores, talla L.', price: 5.00, category: 'Calcetines', applicableTo: 'adultOrOver6', isRequiredOnce: true },
    // { id: 18, name: 'Calcetín Talla XL', description: 'Medias para mayores, talla XL.', price: 5.00, category: 'Calcetines', applicableTo: 'adultOrOver6', isRequiredOnce: true },
];

const Tienda = (props) => {

    const [PRODUCTS,setPRODUCTS] = useState(props?.products.map((item)=>({...item,price:parseFloat(item.price)})) ?? [])

    
    // --- Estados para el formulario de selección ---
    const getDefaultValues = () => {
        const today = new Date();
        return {
            fecha: today.toLocaleDateString("en-CA", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            }),
        };
    };

    const defaultValue = getDefaultValues();

    const [fecha, setFecha] = useState(defaultValue.fecha);
    const [mensaje, setMensaje] = useState("");

    const [clientType, setClientType] = useState(null); // 'adultOrOver6' o 'under6'
    const [selectedBraceletId, setSelectedBraceletId] = useState('');
    const [braceletQuantity, setBraceletQuantity] = useState(1);

    const [needsSocks, setNeedsSocks] = useState(false);
    const [selectedSockTallaId, setSelectedSockTallaId] = useState('');
    const [sockQuantity, setSockQuantity] = useState(1);

    // --- Estado para el carrito de compras ---
    const [cartItems, setCartItems] = useState([]);

    // --- Productos derivados para facilitar el acceso ---
    const babyParkBraceletProduct = PRODUCTS.find(p => p.category === 'Baby Park');
    const hourlyBraceletsProducts = PRODUCTS.filter(p => p.category === 'Brazalete');
    const socksProducts = PRODUCTS.filter(p => p.category === 'Calcetines');

    // Filtra las medias según el tipo de cliente seleccionado
    const filteredSocks = socksProducts.filter(sock => {
        return sock.applicableTo === clientType;
    });

    const selectedBraceletProduct = PRODUCTS.find(p => p.id === selectedBraceletId);
    const selectedSockProduct = PRODUCTS.find(p => p.id === selectedSockTallaId);

    // --- Handlers de selección de formulario ---
    const handleClientTypeChange = (type) => {
        setClientType(type);
        setSelectedBraceletId('');
        setBraceletQuantity(1);
        setNeedsSocks(false);
        setSelectedSockTallaId(''); // Reiniciar talla de medias al cambiar tipo de cliente
        setSockQuantity(1);

        if (type === 'under6' && babyParkBraceletProduct) {
            setSelectedBraceletId(babyParkBraceletProduct.id);
            setBraceletQuantity(1);
        }
    };

    // --- Lógica para añadir ítems al carrito ---
    const handleAddToCart = () => {
        setMensaje('');

        if (!selectedBraceletProduct) {
            setMensaje("Por favor, selecciona un brazalete antes de añadir.");
            return;
        }

        if (needsSocks && !selectedSockProduct) {
            setMensaje("Por favor, selecciona la talla de las medias.");
            return;
        }

        const itemsToAdd = [];

        const braceletCartItem = {
            uniqueId: Date.now() + '-bracelet-' + selectedBraceletProduct.id,
            product: { ...selectedBraceletProduct },
            quantity: braceletQuantity,
            selectedDate: fecha,
            clientType: clientType
        };
        itemsToAdd.push(braceletCartItem);

        if (needsSocks && selectedSockProduct) {
            const sockCartItem = {
                uniqueId: Date.now() + '-sock-' + selectedSockProduct.id,
                product: { ...selectedSockProduct },
                quantity: sockQuantity,
            };
            itemsToAdd.push(sockCartItem);
        }

        setCartItems(prevCartItems => [...prevCartItems, ...itemsToAdd]);
        setMensaje("¡Productos añadidos al carrito!");

        // Reiniciar el formulario de selección después de añadir al carrito
        setClientType(null);
        setSelectedBraceletId('');
        setBraceletQuantity(1);
        setNeedsSocks(false);
        setSelectedSockTallaId('');
        setSockQuantity(1);
    };

    // --- Lógica para eliminar un ítem del carrito ---
    const handleRemoveFromCart = (uniqueIdToRemove) => {
        setCartItems(prevCartItems => prevCartItems.filter(item => item.uniqueId !== uniqueIdToRemove));
    };

    // --- Lógica para ajustar la cantidad de un ítem en el carrito ---
    const handleUpdateCartItemQuantity = (uniqueIdToUpdate, newQuantity) => {
        setCartItems(prevCartItems =>
            prevCartItems.map(item =>
                item.uniqueId === uniqueIdToUpdate
                    ? { ...item, quantity: Math.max(1, newQuantity) }
                    : item
            )
        );
    };

    // --- Lógica para el submit final del carrito a Laravel ---
    const handleSubmitCheckout = () => {
        if (cartItems.length === 0) {
            setMensaje("Tu carrito está vacío. Por favor, añade productos.");
            return;
        }

        const dataToSend = {
            fecha: fecha,
            items: cartItems.map(item => ({
                product_id: item.product.id,
                quantity: item.quantity,
                // client_type: item.clientType, // Puedes enviar esto si tu backend lo necesita
            })),
        };

        Inertia.visit('/tienda', {
            method: 'POST',
            data: dataToSend,
            onSuccess: () => {
                setMensaje("¡Tu pedido ha sido enviado con éxito!");
                setCartItems([]);
            },
            onError: (errors) => {
                console.error("Error al procesar la compra:", errors);
                setMensaje("Hubo un error al procesar tu compra. Revisa los datos e inténtalo de nuevo.");
            }
        });
    };

    // Calcular el total del carrito para mostrar en el frontend
    const totalCartPrice = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);

    return (
        <Layout>
            <BannerHero
                img="/img/home/BrincaEste.jpg"
                title="COMPRA TUS ENTRADAS AQUÍ!"
            />
            <div className="py-8 container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Columna de Selección de Productos */}
                    <div className="bg-white shadow-lg rounded-lg overflow-hidden p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-6">Selecciona tus Productos</h2>

                        {/* Paso 1: Selección de Fecha */}
                        <div className="form-group mb-6 border-b pb-4">
                            <label htmlFor="fecha" className="block text-gray-700 text-lg font-semibold mb-3">
                                Fecha de Visita:
                            </label>
                            <input
                                type="date"
                                id="fecha"
                                className="mt-1 block w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                                value={fecha}
                                onChange={(e) => setFecha(e.target.value)}
                                required
                            />
                        </div>

                        {/* Paso 2: Selección de Tipo de Cliente */}
                        <div className="mb-6 border-b pb-4">
                            <p className="block text-gray-700 text-lg font-semibold mb-3">¿Para quién es el brazalete?</p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <button
                                    type="button"
                                    onClick={() => handleClientTypeChange('under6')}
                                    className={`py-3 px-6 rounded-lg font-medium transition duration-300
                                        ${clientType === 'under6' ? 'bg-indigo-600 text-white shadow-md' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'}
                                    `}
                                >
                                    Niño/a &lt; 6 años (Área Baby Park)
                                </button>
                                <button
                                    type="button"
                                    onClick={() => handleClientTypeChange('adultOrOver6')}
                                    className={`py-3 px-6 rounded-lg font-medium transition duration-300
                                        ${clientType === 'adultOrOver6' ? 'bg-emerald-600 text-white shadow-md' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'}
                                    `}
                                >
                                    Mayor de 6 años o Adulto (Trampolines)
                                </button>
                            </div>
                        </div>

                        {/* Paso 3: Selección de Brazalete y Medias (Condicional) */}
                        {clientType && (
                            <div className="mt-6">
                                {/* Sección de Brazalete Baby Park */}
                                {clientType === 'under6' && babyParkBraceletProduct && (
                                    <div className="bg-blue-50 p-4 rounded-md mb-6 border border-blue-200">
                                        <h3 className="text-xl font-semibold text-blue-800 mb-3">Brazalete Baby Park</h3>
                                        <p className="text-blue-700 mb-2">
                                            **{babyParkBraceletProduct.name}**: {babyParkBraceletProduct.description}
                                            <span className="font-bold ml-2">${babyParkBraceletProduct.price.toFixed(2)}</span>
                                        </p>
                                        <div className="flex items-center gap-2 mt-2">
                                            <label htmlFor="babyParkQty" className="text-gray-700">Cantidad:</label>
                                            <input
                                                type="number"
                                                id="babyParkQty"
                                                min="1"
                                                value={braceletQuantity}
                                                onChange={(e) => setBraceletQuantity(parseInt(e.target.value) || 1)}
                                                className="w-20 p-2 border rounded-md text-center"
                                            />
                                        </div>
                                    </div>
                                )}

                                {/* Sección de Brazaletes por Hora */}
                                {clientType === 'adultOrOver6' && (
                                    <div className="bg-green-50 p-4 rounded-md mb-6 border border-green-200">
                                        <h3 className="text-xl font-semibold text-green-800 mb-3">Selecciona tu Franja Horaria (Brazalete)</h3>
                                        <p className="text-green-700 mb-3">
                                            Precio por brazalete: ${hourlyBraceletsProducts[0]?.price.toFixed(2)}
                                        </p>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                            {hourlyBraceletsProducts.map(bracelet => (
                                                <button
                                                    key={bracelet.id}
                                                    type="button"
                                                    onClick={() => setSelectedBraceletId(bracelet.id)}
                                                    className={`p-3 border rounded-md text-sm font-medium text-center transition duration-200
                                                        ${selectedBraceletId === bracelet.id ? 'bg-green-600 text-white shadow-md' : 'bg-white text-green-700 hover:bg-green-100 border-green-300'}
                                                    `}
                                                >
                                                    {bracelet.description} <br /> ({bracelet.name.replace('Brazalete ', '')})
                                                </button>
                                            ))}
                                        </div>
                                        {selectedBraceletId && (
                                            <div className="flex items-center gap-2 mt-3">
                                                <label htmlFor="hourlyQty" className="text-gray-700">Cantidad:</label>
                                                <input
                                                    type="number"
                                                    id="hourlyQty"
                                                    min="1"
                                                    value={braceletQuantity}
                                                    onChange={(e) => setBraceletQuantity(parseInt(e.target.value) || 1)}
                                                    className="w-20 p-2 border rounded-md text-center"
                                                />
                                            </div>
                                        )}
                                        {!selectedBraceletId && (
                                            <p className="text-red-500 text-sm mt-2">Por favor, selecciona una franja horaria.</p>
                                        )}
                                    </div>
                                )}

                                {/* Sección de Medias Especiales */}
                                <div className="bg-yellow-50 p-4 rounded-md border border-yellow-200 mb-6">
                                    <h3 className="text-xl font-semibold text-yellow-800 mb-3">Medias Especiales</h3>
                                    <p className="text-yellow-700 mb-2">Obligatorias para usar los trampolines (compra única).</p>
                                    <div className="flex items-center gap-4 mb-3">
                                        <label className="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                className="form-checkbox h-5 w-5 text-yellow-600"
                                                checked={needsSocks}
                                                onChange={(e) => setNeedsSocks(e.target.checked)}
                                            />
                                            <span className="ml-2 text-gray-700">Sí, necesito medias</span>
                                        </label>
                                        <label className="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                className="form-checkbox h-5 w-5 text-gray-600"
                                                checked={!needsSocks}
                                                onChange={(e) => setNeedsSocks(!e.target.checked)}
                                            />
                                            <span className="ml-2 text-gray-700">No, ya tengo medias</span>
                                        </label>
                                    </div>

                                    {needsSocks && (
                                        <>
                                            <div className="mb-3">
                                                <label htmlFor="sockTalla" className="block text-gray-700 mb-1">Selecciona Talla:</label>
                                                <select
                                                    id="sockTalla"
                                                    value={selectedSockTallaId}
                                                    onChange={(e) => setSelectedSockTallaId(parseInt(e.target.value))}
                                                    className="w-full p-2 border rounded-md bg-white"
                                                >
                                                    <option value="">Selecciona la talla de medias</option>
                                                    {filteredSocks.map(sock => ( // Usamos filteredSocks aquí
                                                        <option key={sock.id} value={sock.id}>
                                                            {sock.name} - ${sock.price.toFixed(2)}
                                                        </option>
                                                    ))}
                                                </select>
                                                {!selectedSockTallaId && (
                                                    <p className="text-red-500 text-sm mt-1">Por favor, selecciona una talla de medias.</p>
                                                )}
                                            </div>
                                            {selectedSockTallaId && (
                                                <div className="flex items-center gap-2">
                                                    <label htmlFor="sockQty" className="text-gray-700">Cantidad:</label>
                                                    <input
                                                        type="number"
                                                        id="sockQty"
                                                        min="1"
                                                        value={sockQuantity}
                                                        onChange={(e) => setSockQuantity(parseInt(e.target.value) || 1)}
                                                        className="w-20 p-2 border rounded-md text-center"
                                                    />
                                                </div>
                                            )}
                                        </>
                                    )}
                                </div>

                                {/* Botón Añadir al Carrito (para el ítem seleccionado actualmente) */}
                                <div className="mt-6 text-center">
                                    <button
                                        type="button"
                                        onClick={handleAddToCart}
                                        disabled={!selectedBraceletId || (needsSocks && !selectedSockTallaId)}
                                        className={`py-3 px-8 rounded-lg text-lg font-bold transition duration-300
                                            ${(!selectedBraceletId || (needsSocks && !selectedSockTallaId)) ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-lg'}
                                        `}
                                    >
                                        Añadir al Carrito
                                    </button>
                                </div>
                            </div>
                        )}
                        {mensaje && <p className="p-4 text-center text-red-600">{mensaje}</p>}
                    </div>

                    {/* Columna del Carrito de Compras */}
                    <div className="bg-white shadow-lg rounded-lg overflow-hidden p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-6">Tu Carrito ({cartItems.length} ítems)</h2>

                        {cartItems.length === 0 ? (
                            <p className="text-gray-500 text-center py-8">El carrito está vacío. ¡Empieza a añadir productos!</p>
                        ) : (
                            <>
                                <ul className="divide-y divide-gray-200">
                                    {cartItems.map(item => (
                                        <li key={item.uniqueId} className="py-4 flex flex-col sm:flex-row justify-between items-center">
                                            <div className="flex-1 text-center sm:text-left mb-2 sm:mb-0">
                                                <p className="font-semibold text-gray-700">{item.product.name}</p>
                                                <p className="text-sm text-gray-500">{item.product.description}</p>
                                                {item.selectedDate && (
                                                    <p className="text-xs text-gray-500">Fecha: {item.selectedDate}</p>
                                                )}
                                                {item.clientType && (
                                                    <p className="text-xs text-gray-500">Tipo: {item.clientType === 'under6' ? 'Niño < 6' : 'Adulto/Niño > 6'}</p>
                                                )}
                                            </div>
                                            <div className="flex items-center gap-3">
                                                <button
                                                    type="button"
                                                    onClick={() => handleUpdateCartItemQuantity(item.uniqueId, item.quantity - 1)}
                                                    className="p-1.5 bg-gray-200 rounded-full text-gray-700 hover:bg-gray-300 transition"
                                                    disabled={item.quantity <= 1}
                                                >
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 12H4"></path></svg>
                                                </button>
                                                <span className="font-medium text-gray-800">{item.quantity}</span>
                                                <button
                                                    type="button"
                                                    onClick={() => handleUpdateCartItemQuantity(item.uniqueId, item.quantity + 1)}
                                                    className="p-1.5 bg-gray-200 rounded-full text-gray-700 hover:bg-gray-300 transition"
                                                >
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                                <span className="font-semibold text-gray-800 w-20 text-right">${(item.product.price * item.quantity).toFixed(2)}</span>
                                                <button
                                                    type="button"
                                                    onClick={() => handleRemoveFromCart(item.uniqueId)}
                                                    className="ml-3 text-red-500 hover:text-red-700 transition"
                                                    title="Eliminar"
                                                >
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </li>
                                    ))}
                                </ul>

                                <div className="text-right mt-6 pt-4 border-t border-gray-200">
                                    <div className="text-2xl font-bold text-gray-900">
                                        Total: ${totalCartPrice.toFixed(2)}
                                    </div>
                                    <button
                                        type="button"
                                        onClick={handleSubmitCheckout}
                                        className="mt-4 w-full bg-green-600 text-white py-3 rounded-lg text-lg font-bold hover:bg-green-700 transition shadow-lg"
                                    >
                                        Proceder al Pago
                                    </button>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Tienda;
