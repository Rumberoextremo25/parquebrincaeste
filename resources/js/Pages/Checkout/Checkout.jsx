import React, { useState, useEffect, Fragment } from 'react'; // Import Fragment
import Layout from '@/Layouts/Layout';
import BannerHero from '@/Components/Hero/BannerHero';
import Modal from '@/Components/Modal'; // <--- VERIFICA ESTA RUTA DE NUEVO, DEBE SER LA CORRECTA A TU MODAL EXISTENTE

const Checkout = ({ cartItems: initialCartItems, user }) => {
    const [localCartItems, setLocalCartItems] = useState(initialCartItems);
    const [formData, setFormData] = useState({
        nombre_completo: '',
        correo: '',
        telefono: '',
        direccion: '',
        ciudad: '',
        codigo_postal: '',
        promoCode: '',
        paymentMethod: '',
        banco_remitente: '', // Este será ahora el valor seleccionado del dropdown
        numero_telefono_remitente: '',
        cedula_remitente: '',
        numero_referencia_pago: '',
        monto: 0
    });
    const [loading, setLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [total, setTotal] = useState(0);
    const [showMobilePaymentInfoModal, setShowMobilePaymentInfoModal] = useState(false);
    const [showMobilePaymentForm, setShowMobilePaymentForm] = useState(false);

    // --- ESTADOS PARA LA LISTA DE BANCOS ---
    const [availableBanks, setAvailableBanks] = useState([]); // Almacena la lista de bancos
    const [banksLoading, setBanksLoading] = useState(true);   // Indica si los bancos están cargando
    const [banksError, setBanksError] = useState('');        // Almacena errores al cargar bancos

    // Datos del comercio para Pago Móvil (ejemplo, deberían venir de tu backend o configuración)
    const merchantMobilePaymentDetails = {
        banco: 'Bancaribe C.A.',
        cedula: 'J-505728440',
        Nombre: 'Brinca Este 2024 C.A',
        telefono: '(0412) 350 88 26'
    };

    // Efecto para recalcular el total
    useEffect(() => {
        const totalAmount = localCartItems.reduce((acc, item) => acc + item.price * item.quantity, 0);
        setTotal(totalAmount);
        setFormData((prevData) => ({ ...prevData, monto: totalAmount }));
    }, [localCartItems]);

    // --- EFECTO ACTUALIZADO PARA CARGAR LA LISTA DE BANCOS DESDE EL BACKEND ---
    useEffect(() => {
        const fetchBanks = async () => {
            setBanksLoading(true);
            setBanksError('');
            try {
                // CAMBIO AQUÍ: Llamada real al endpoint de tu Laravel
                const response = await fetch('/api/bancos/listar');

                if (!response.ok) {
                    // Si la respuesta no es 2xx, lanza un error
                    const errorText = await response.text(); // Intenta leer el texto del error
                    throw new Error(`Error al cargar bancos: ${response.status} - ${errorText}`);
                }
                const data = await response.json();
                setAvailableBanks(data);
            } catch (error) {
                setBanksError(error.message);
                console.error("Error al cargar bancos:", error);
            } finally {
                setBanksLoading(false);
            }
        };

        fetchBanks();
    }, []); // El array vacío asegura que se ejecute solo una vez al montar

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });

        if (name === 'paymentMethod') {
            if (value === 'mobile-payment') {
                setShowMobilePaymentInfoModal(true);
                setShowMobilePaymentForm(true);
            } else {
                setShowMobilePaymentInfoModal(false);
                setShowMobilePaymentForm(false);
            }
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrorMessage('');

        try {
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            const response = await fetch('/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({
                    ...formData,
                    total,
                    items: localCartItems.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity,
                    }))
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al procesar la compra.');
            }

            const data = await response.json();
            if (data.success) {
                window.location.href = '/Checkout/Success';
            } else {
                throw new Error('Error en la respuesta del servidor.');
            }
        } catch (error) {
            setErrorMessage(error.message);
        } finally {
            setLoading(false);
        }
    };

    const InputField = ({ type, name, label, value, onChange, required, readOnly, placeholder }) => (
        <div className="mb-4">
            <label htmlFor={name} className="block text-gray-700 text-sm font-bold mb-2">{label}</label>
            <input
                type={type}
                name={name}
                id={name}
                required={required}
                value={value}
                onChange={onChange}
                readOnly={readOnly}
                placeholder={placeholder}
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            />
        </div>
    );

    const handleQuantityChange = (productId, change) => {
        setLocalCartItems((prevItems) => {
            return prevItems.map((item) => {
                if (item.id === productId) {
                    const newQuantity = item.quantity + change;
                    return {
                        ...item,
                        quantity: newQuantity > 0 ? newQuantity : 1,
                        subtotal: (newQuantity > 0 ? newQuantity : 1) * item.price
                    };
                }
                return item;
            });
        });
    };

    return (
        <Layout>
            <BannerHero img="https://wallpaperbat.com/img/423222-eagle-mountain-sunset-minimalist-1366x768-resolution.jpg" title="Checkout" />
            <div className="container mx-auto p-4 md:p-8">

                <div className="flex flex-wrap -mx-4">

                    <div className="w-full md:w-3/4 px-4">
                        {errorMessage && <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong className="font-bold">Error:</strong>
                            <span className="block sm:inline">{errorMessage}</span>
                        </div>}
                        <form className="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" onSubmit={handleSubmit}>
                            <h2 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Detalles del Cliente</h2>
                            {['nombre_completo', 'correo', 'telefono'].map((field) => (
                                <InputField
                                    key={field}
                                    type={field === 'correo' ? 'email' : 'tel'}
                                    name={field}
                                    label={field === 'nombre_completo' ? 'Nombre Completo' : field === 'correo' ? 'Correo Electrónico' : 'Número de Teléfono'}
                                    value={formData[field]}
                                    onChange={handleChange}
                                    required
                                />
                            ))}

                            <h2 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2 mt-8">Dirección de Facturación</h2>
                            {['direccion', 'ciudad', 'codigo_postal'].map((field) => (
                                <InputField
                                    key={field}
                                    type="text"
                                    name={field}
                                    label={field === 'direccion' ? 'Dirección' : field === 'ciudad' ? 'Ciudad' : 'Código Postal'}
                                    value={formData[field]}
                                    onChange={handleChange}
                                    required
                                />
                            ))}

                            <h2 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2 mt-8">Código de Promoción</h2>
                            <InputField
                                type="text"
                                name="promoCode"
                                label="Introduce tu código"
                                value={formData.promoCode}
                                onChange={handleChange}
                            />

                            <h2 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2 mt-8">Pasarela de Pago</h2>
                            <div className="mb-4">
                                <label htmlFor="payment-method" className="block text-gray-700 text-sm font-bold mb-2">Selecciona tu método de pago</label>
                                <select name="paymentMethod" id="payment-method" required value={formData.paymentMethod} onChange={handleChange} className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Seleccione...</option>
                                    <option value="mobile-payment">Pago Móvil</option>
                                    <option value="in-store">Pago en Caja</option>
                                </select>
                            </div>

                            <Modal show={showMobilePaymentInfoModal} onClose={() => setShowMobilePaymentInfoModal(false)}>
                                <div className="p-6">
                                    <h3 className="text-2xl font-bold text-center text-blue-600 mb-4">¡Realiza tu Pago Móvil!</h3>
                                    <p className="text-gray-700 mb-4 text-center">Por favor, realiza la transacción a los siguientes datos:</p>
                                    <ul className="list-disc list-inside bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                                        <li className="mb-2 text-lg text-blue-800">
                                            <strong>Banco:</strong> <span className="font-semibold">{merchantMobilePaymentDetails.banco}</span>
                                        </li>
                                        <li className="mb-2 text-lg text-blue-800">
                                            <strong>Cédula/RIF:</strong> <span className="font-semibold">{merchantMobilePaymentDetails.cedula}</span>
                                        </li>
                                        <li className="mb-2 text-lg text-blue-800">
                                            <strong>Nombre de Empresa:</strong> <span className="font-semibold">{merchantMobilePaymentDetails.Nombre}</span>
                                        </li>
                                        <li className="mb-2 text-lg text-blue-800">
                                            <strong>Número de Teléfono:</strong> <span className="font-semibold">{merchantMobilePaymentDetails.telefono}</span>
                                        </li>
                                        <li className="text-xl font-bold text-green-700 mt-4">
                                            <strong>Monto a pagar:</strong> <span className="text-green-800">${total.toFixed(2)}</span>
                                        </li>
                                    </ul>
                                    <p className="text-gray-700 mb-6 text-center">Una vez realizada la transacción, completa el formulario de abajo para confirmar tu pago.</p>
                                    <div className="flex justify-center">
                                        <button
                                            className="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300"
                                            onClick={() => setShowMobilePaymentInfoModal(false)}
                                        >
                                            Entendido
                                        </button>
                                    </div>
                                </div>
                            </Modal>

                            {showMobilePaymentForm && (
                                <div className="mt-8">
                                    <h3 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Confirma tu Pago Móvil</h3>
                                    <div className="mb-4">
                                        <label htmlFor="banco_remitente" className="block text-gray-700 text-sm font-bold mb-2">Banco del Remitente</label>
                                        {banksLoading ? (
                                            <p className="text-gray-500">Cargando bancos...</p>
                                        ) : banksError ? (
                                            <p className="text-red-500">Error: {banksError}</p>
                                        ) : (
                                            <select
                                                name="banco_remitente"
                                                id="banco_remitente"
                                                value={formData.banco_remitente}
                                                onChange={handleChange}
                                                required
                                                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                            >
                                                <option value="">Seleccione el Banco</option>
                                                {availableBanks.map((bank) => (
                                                    <option key={bank.id} value={bank.name}>
                                                        {bank.name}
                                                    </option>
                                                ))}
                                            </select>
                                        )}
                                    </div>

                                    <InputField
                                        type="tel"
                                        name="numero_telefono_remitente"
                                        label="Número de Teléfono del Remitente"
                                        value={formData.numero_telefono_remitente}
                                        onChange={handleChange}
                                        required
                                        placeholder="Ej: 04XX-XXXXXXX"
                                    />
                                    <InputField
                                        type="text"
                                        name="cedula_remitente"
                                        label="Cédula/RIF del Remitente"
                                        value={formData.cedula_remitente}
                                        onChange={handleChange}
                                        required
                                        placeholder="Ej: V-12345678"
                                    />
                                    <InputField
                                        type="text"
                                        name="numero_referencia_pago"
                                        label="Número de Referencia"
                                        value={formData.numero_referencia_pago}
                                        onChange={handleChange}
                                        required
                                        placeholder="Ej: 1234567890"
                                    />
                                    <InputField
                                        type="number"
                                        name="monto"
                                        label="Monto"
                                        value={formData.monto.toFixed(2)}
                                        readOnly
                                    />
                                </div>
                            )}

                            <button type="submit" className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" disabled={loading}>
                                {loading ? 'Procesando...' : 'Completar Compra'}
                            </button>
                        </form>
                    </div>

                    <div className="w-full md:w-1/4 px-4">
                        <div className="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                            <h4 className="text-xl font-bold mb-4">
                                Carrito
                                <span className="text-black ml-2">
                                    <i className="fa fa-shopping-cart"></i>
                                    <b className="ml-1">{localCartItems.length}</b>
                                </span>
                            </h4>
                            {localCartItems.map((item) => (
                                <div key={item.id} className="flex justify-between items-center py-2 border-b border-gray-200">
                                    <p>
                                        <a href="#" className="text-blue-600 hover:underline">{item.name}</a>
                                        <span className="text-gray-700 ml-2">${item.price}</span>
                                    </p>
                                    <div className="flex items-center">
                                        <button
                                            onClick={() => handleQuantityChange(item.id, -1)}
                                            disabled={item.quantity <= 1}
                                            className="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-2 rounded-l"
                                        >
                                            -
                                        </button>
                                        <span className="mx-2">{item.quantity}</span>
                                        <button
                                            onClick={() => handleQuantityChange(item.id, 1)}
                                            className="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-2 rounded-r"
                                        >
                                            +
                                        </button>
                                    </div>
                                </div>
                            ))}
                            <hr className="my-4" />
                            <p className="text-right">Total: <span className="font-bold text-gray-800">${total.toFixed(2)}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Checkout;