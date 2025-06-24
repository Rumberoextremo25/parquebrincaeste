import React, { useState, useEffect, Fragment } from 'react';
import Layout from '@/Layouts/Layout';
import BannerHero from '@/Components/Hero/BannerHero';
import Modal from '@/Components/Modal';
import { router } from '@inertiajs/react';

const Checkout = ({ cartItems: initialCartItems, user, errors, availableBanks: initialAvailableBanks }) => {
    const [localCartItems, setLocalCartItems] = useState(initialCartItems);
    const [formData, setFormData] = useState({
        nombre_completo: user?.name || '',
        correo: user?.email || '',
        telefono: user?.phone || '',
        direccion: user?.address || '',
        ciudad: user?.city || '',
        codigo_postal: user?.postal_code || '',
        promoCode: '',
        paymentMethod: '',
        banco_remitente: '',
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

    const merchantMobilePaymentDetails = {
        banco: 'Bancaribe C.A.',
        cedula: 'J-505728440',
        Nombre: 'Brinca Este 2024 C.A',
        telefono: '(0412) 350 88 26'
    };

    useEffect(() => {
        const totalAmount = localCartItems.reduce((acc, item) => acc + item.price * item.quantity, 0);
        setTotal(totalAmount);
        setFormData((prevData) => ({ ...prevData, monto: totalAmount }));
    }, [localCartItems]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });

        if (name === 'paymentMethod') {
            if (value === 'mobile-payment') {
                setShowMobilePaymentInfoModal(true);
                setShowMobilePaymentForm(true);
            } else {
                // Cuando se cambia a "Pago en Caja", limpiamos los campos de pago móvil
                setFormData(prevData => ({
                    ...prevData,
                    banco_remitente: '',
                    numero_telefono_remitente: '',
                    cedula_remitente: '',
                    numero_referencia_pago: ''
                }));
                setShowMobilePaymentInfoModal(false);
                setShowMobilePaymentForm(false);
            }
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrorMessage('');

        if (localCartItems.length === 0) {
            setErrorMessage('Tu carrito está vacío. Añade productos para continuar.');
            setLoading(false);
            return;
        }

        let dataToSend = {
            ...formData,
            monto: total, // Asegúrate de enviar el total calculado en el frontend
            items: localCartItems.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                price: item.price, // Si el backend valida y recalcula, este 'price' es informativo
            }))
        };

        // --- INICIO DE LA SOLUCIÓN: Limpiar campos de pago móvil si no son necesarios ---
        if (dataToSend.paymentMethod !== 'mobile-payment') {
            dataToSend = {
                ...dataToSend,
                banco_remitente: null,
                numero_telefono_remitente: null,
                cedula_remitente: null,
                numero_referencia_pago: null,
            };
        }
        // --- FIN DE LA SOLUCIÓN ---

        router.post('/checkout', dataToSend, {
            onStart: () => setLoading(true),
            onFinish: () => setLoading(false),
            onSuccess: () => {
                setLocalCartItems([]); // Limpia el carrito cuando la operación fue exitosa
            },
            onError: (inertiaErrors) => {
                if (typeof inertiaErrors === 'string') {
                    setErrorMessage(inertiaErrors);
                } else if (inertiaErrors.checkout) {
                    setErrorMessage(inertiaErrors.checkout);
                } else if (Object.keys(inertiaErrors).length > 0) {
                    setErrorMessage('Por favor, revise los campos del formulario para corregir los errores.');
                    console.error('Inertia Validation Errors:', inertiaErrors);
                } else {
                    setErrorMessage('Hubo un problema inesperado al procesar su pedido. Por favor, inténtelo de nuevo más tarde.');
                }
            },
        });
    };

    const InputField = ({ type, name, label, value, onChange, required, readOnly, placeholder, error }) => (
        <div className="mb-4">
            <label htmlFor={name} className="block text-gray-700 text-sm font-bold mb-2">{label}</label>
            <input
                type={type}
                name={name}
                id={name}
                required={required}
                value={value === null ? '' : value} // Asegúrate de que los valores null se muestren como cadenas vacías
                onChange={onChange}
                readOnly={readOnly}
                placeholder={placeholder}
                className={`shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ${error ? 'border-red-500' : ''}`}
            />
            {error && <p className="text-red-500 text-xs italic mt-1">{error}</p>}
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

                        {/* --- INICIO DE LA SOLUCIÓN DEL ERROR 'key' --- */}
                        {Object.keys(errors).length > 0 && (
                            <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong className="font-bold">¡Por favor corrige los siguientes errores!</strong>
                                <ul className="mt-2 list-disc list-inside">
                                    {Object.keys(errors).map((key) => {
                                        const errorMessages = Array.isArray(errors[key]) ? errors[key] : [errors[key]];
                                        return errorMessages.map((message, index) => (
                                            <li key={`${key}-${index}`}>{message}</li>
                                        ));
                                    })}
                                </ul>
                            </div>
                        )}
                        {/* --- FIN DE LA SOLUCIÓN DEL ERROR 'key' --- */}

                        <form className="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" onSubmit={handleSubmit}>
                            <h2 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Detalles del Cliente</h2>
                            {['nombre_completo', 'correo', 'telefono'].map((field) => (
                                <InputField
                                    key={field}
                                    type={field === 'correo' ? 'email' : 'text'}
                                    name={field}
                                    label={field === 'nombre_completo' ? 'Nombre Completo' : field === 'correo' ? 'Correo Electrónico' : 'Número de Teléfono'}
                                    value={formData[field]}
                                    onChange={handleChange}
                                    required={field !== 'telefono'}
                                    error={errors[field]}
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
                                    required={field !== 'codigo_postal'}
                                    error={errors[field]}
                                />
                            ))}

                            <h2 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2 mt-8">Código de Promoción</h2>
                            <InputField
                                type="text"
                                name="promoCode"
                                label="Introduce tu código"
                                value={formData.promoCode}
                                onChange={handleChange}
                                error={errors.promoCode}
                            />

                            <h2 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2 mt-8">Pasarela de Pago</h2>
                            <div className="mb-4">
                                <label htmlFor="payment-method" className="block text-gray-700 text-sm font-bold mb-2">Selecciona tu método de pago</label>
                                <select
                                    name="paymentMethod"
                                    id="payment-method"
                                    required
                                    value={formData.paymentMethod}
                                    onChange={handleChange}
                                    className={`shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ${errors.paymentMethod ? 'border-red-500' : ''}`}
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="mobile-payment">Pago Móvil</option>
                                    <option value="in-store">Pago en Caja</option>
                                </select>
                                {errors.paymentMethod && <p className="text-red-500 text-xs italic mt-1">{errors.paymentMethod}</p>}
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
                                    <InputField
                                        type="text"
                                        name="banco_remitente"
                                        label="Banco del Remitente"
                                        value={formData.banco_remitente}
                                        onChange={handleChange}
                                        required={formData.paymentMethod === 'mobile-payment'}
                                        placeholder="Ej: Banco Mercantil"
                                        error={errors.banco_remitente}
                                    />

                                    <InputField
                                        type="tel"
                                        name="numero_telefono_remitente"
                                        label="Número de Teléfono del Remitente"
                                        value={formData.numero_telefono_remitente}
                                        onChange={handleChange}
                                        required={formData.paymentMethod === 'mobile-payment'}
                                        placeholder="Ej: 04XX-XXXXXXX"
                                        error={errors.numero_telefono_remitente}
                                    />
                                    <InputField
                                        type="text"
                                        name="cedula_remitente"
                                        label="Cédula/RIF del Remitente"
                                        value={formData.cedula_remitente}
                                        onChange={handleChange}
                                        required={formData.paymentMethod === 'mobile-payment'}
                                        placeholder="Ej: V-12345678"
                                        error={errors.cedula_remitente}
                                    />
                                    <InputField
                                        type="text"
                                        name="numero_referencia_pago"
                                        label="Número de Referencia"
                                        value={formData.numero_referencia_pago}
                                        onChange={handleChange}
                                        required={formData.paymentMethod === 'mobile-payment'}
                                        placeholder="Ej: 1234567890"
                                        error={errors.numero_referencia_pago}
                                    />
                                    <InputField
                                        type="number"
                                        name="monto"
                                        label="Monto"
                                        value={total.toFixed(2)}
                                        readOnly
                                        error={errors.monto}
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
                            {localCartItems.length === 0 ? (
                                <p className="text-gray-600">El carrito está vacío.</p>
                            ) : (
                                localCartItems.map((item) => (
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
                                ))
                            )}
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