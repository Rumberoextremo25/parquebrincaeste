import React, { useState, useEffect, Fragment } from 'react';
import Layout from '@/Layouts/Layout';
import BannerHero from '@/Components/Hero/BannerHero';
import Modal from '@/Components/Modal';
import { router } from '@inertiajs/react';

// --- Componente InputField envuelto en React.memo (ideal para optimización) ---
const InputField = React.memo(({ type, name, label, value, onChange, required, readOnly, placeholder, error, maxLength, pattern, inputMode, autoComplete }) => {
    return (
        <div className="mb-4">
            <label htmlFor={name} className="block text-gray-700 text-sm font-bold mb-2">{label}</label>
            <input
                type={type}
                name={name}
                id={name}
                required={required}
                value={value === null ? '' : value}
                onChange={onChange}
                readOnly={readOnly}
                placeholder={placeholder}
                maxLength={maxLength}
                pattern={pattern}
                inputMode={inputMode}
                autoComplete={autoComplete}
                className={`shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline ${error ? 'border-red-500' : ''}`}
            />
            {error && <p className="text-red-500 text-xs italic mt-1">{error}</p>}
        </div>
    );
});

// --- Componente principal Checkout ---
const Checkout = ({ cartItems: initialCartItems, user, errors, bcvRate: initialBcvRate }) => {
    // Initialize localCartItems directly from initialCartItems, as they are already flattened
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
        // Campos específicos para pago móvil
        banco_remitente: '',
        numero_telefono_remitente: '',
        cedula_remitente: '',
        // Campos específicos para tarjeta de crédito/débito
        card_number: '',
        card_holder_name: '',
        card_expiry: '', // Unificado: MM/YY o MM/YYYY
        card_cvv: '',
        // Campo de referencia de pago (para comprobante físico, si aplica, o para pago móvil)
        numero_referencia_pago: '',
        monto: 0
    });
    const [loading, setLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [totalUSD, setTotalUSD] = useState(0);

    const [currentBcvRate, setCurrentBcvRate] = useState(initialBcvRate);
    const [totalBs, setTotalBs] = useState(0);
    const [showMobilePaymentInfoModal, setShowMobilePaymentInfoModal] = useState(false);
    const [showPaymentDetailsForm, setShowPaymentDetailsForm] = useState(false);

    const [cardType, setCardType] = useState(''); // Estado para almacenar el tipo de tarjeta

    // Detalles del comercio para pago móvil
    const merchantMobilePaymentDetails = {
        banco: 'Bancaribe C.A.',
        cedula: 'J-505728440',
        Nombre: 'Brinca Este 2024 C.A',
        telefono: '(0412) 350 88 26'
    };

    // Función para detectar el tipo de tarjeta
    const detectCardType = (cardNumber) => {
        if (!cardNumber) return '';
        cardNumber = cardNumber.replace(/\s/g, ''); // Eliminar espacios

        // Patrones regex para tipos de tarjeta
        const visaPattern = /^4/;
        const mastercardPattern = /^5[1-5]/;
        const maestroPattern = /^(50|56|57|58|6[0-9])/; // Maestro tiene un rango más amplio

        if (visaPattern.test(cardNumber)) {
            return 'Visa';
        } else if (mastercardPattern.test(cardNumber)) {
            return 'Mastercard';
        } else if (maestroPattern.test(cardNumber)) {
            return 'Maestro';
        }
        return '';
    };

    useEffect(() => {
        const calculatedTotalUSD = localCartItems.reduce((acc, item) => acc + (item.price || 0) * item.quantity, 0);
        setTotalUSD(calculatedTotalUSD);
        setFormData((prevData) => ({ ...prevData, monto: calculatedTotalUSD }));

        if (currentBcvRate > 0) {
            setTotalBs(calculatedTotalUSD * currentBcvRate);
        } else {
            setTotalBs(0);
        }
    }, [localCartItems, currentBcvRate]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prevData => {
            const newState = { ...prevData, [name]: value };

            if (name === 'paymentMethod') {
                if (value === 'credit-debit-card') {
                    setShowPaymentDetailsForm(true);
                    setShowMobilePaymentInfoModal(false);
                } else if (value === 'mobile-payment') {
                    setShowMobilePaymentInfoModal(true);
                    setShowPaymentDetailsForm(true);
                } else {
                    // Limpia todos los campos de pago si se cambia el método a algo que no los requiera
                    newState.banco_remitente = '';
                    newState.numero_telefono_remitente = '';
                    newState.cedula_remitente = '';
                    newState.numero_referencia_pago = '';
                    newState.card_number = '';
                    newState.card_holder_name = '';
                    newState.card_expiry = ''; // Limpiar el campo unificado
                    newState.card_cvv = '';
                    setCardType(''); // Limpia el tipo de tarjeta
                    setShowMobilePaymentInfoModal(false);
                    setShowPaymentDetailsForm(false);
                }
            } else if (name === 'card_number') {
                // Formatear número de tarjeta para espacios cada 4 dígitos
                const formattedValue = value.replace(/\s?/g, '').replace(/(\d{4})/g, '$1 ').trim();
                newState[name] = formattedValue;
                setCardType(detectCardType(formattedValue)); // Detecta el tipo de tarjeta
            } else if (name === 'card_expiry') {
                // Formatear la fecha de vencimiento a MM/YY o MM/YYYY
                let formattedExpiry = value.replace(/\D/g, ''); // Eliminar todo lo que no sea dígito
                if (formattedExpiry.length > 2) {
                    formattedExpiry = formattedExpiry.substring(0, 2) + '/' + formattedExpiry.substring(2, 6);
                }
                newState[name] = formattedExpiry.substring(0, 7); // Limitar a MM/YYYY (7 caracteres)
            } else if (name === 'card_cvv') {
                // Permitir solo números y limitar longitud para CVV
                const numericValue = value.replace(/\D/g, ''); // Elimina todo lo que no sea dígito
                newState[name] = numericValue.substring(0, 4); // CVV es 3 o 4 dígitos
            }

            return newState;
        });
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

        // Validación específica para Tarjeta de Crédito/Débito
        if (formData.paymentMethod === 'credit-debit-card') {
            if (!formData.card_number.replace(/\s/g, '').match(/^\d{13,19}$/)) { // 13-19 dígitos sin espacios
                setErrorMessage('Por favor, ingresa un número de tarjeta válido (13 a 19 dígitos).');
                setLoading(false);
                return;
            }
            if (!formData.card_holder_name.trim()) {
                setErrorMessage('Por favor, ingresa el nombre del tarjetahabiente.');
                setLoading(false);
                return;
            }

            // Validación del campo unificado card_expiry
            const expiryParts = formData.card_expiry.split('/');
            if (expiryParts.length !== 2) {
                setErrorMessage('El formato de la fecha de vencimiento debe ser MM/AA o MM/AAAA.');
                setLoading(false);
                return;
            }

            const expiryMonth = parseInt(expiryParts[0], 10);
            let expiryYear = parseInt(expiryParts[1], 10);

            // Ajustar el año a formato de 4 dígitos si se ingresó en 2 dígitos
            if (expiryYear < 100) {
                const currentYearPrefix = Math.floor(new Date().getFullYear() / 100); // Ej: 20
                expiryYear = currentYearPrefix * 100 + expiryYear;
            }

            const currentYearFull = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1; // Mes actual (1-12)

            if (!expiryMonth || expiryMonth < 1 || expiryMonth > 12) {
                setErrorMessage('El mes de vencimiento de la tarjeta no es válido.');
                setLoading(false);
                return;
            }
            if (!expiryYear || expiryYear < currentYearFull || (expiryYear === currentYearFull && expiryMonth < currentMonth)) {
                setErrorMessage('La fecha de vencimiento de la tarjeta no es válida o ya ha expirado.');
                setLoading(false);
                return;
            }

            if (!formData.card_cvv.match(/^\d{3,4}$/)) { // 3 o 4 dígitos para CVV
                setErrorMessage('Por favor, ingresa un CVV válido (3 o 4 dígitos).');
                setLoading(false);
                return;
            }
        }

        // Validación para Pago Móvil (solo el número de referencia)
        if (formData.paymentMethod === 'mobile-payment') {
            if (!formData.numero_referencia_pago.trim()) {
                setErrorMessage('Por favor, ingresa el número de referencia del pago móvil.');
                setLoading(false);
                return;
            }
        }

        let dataToSend = {
            ...formData,
            monto: totalUSD,
            monto_bs: totalBs.toFixed(2),
            bcv_rate_used: currentBcvRate,
            // MODIFICATION: Map localCartItems to include all the flattened properties
            items: localCartItems.map(item => ({
                product_id: item.product_id, // Use product_id from the item
                quantity: item.quantity,
                price: item.price, // Use the adjusted price from the item
                selected_date: item.selectedDate,
                selected_time: item.selectedTime,
                product_name: item.product_name,
                product_description: item.product_description,
                client_type: item.clientType,
                uniqueId: item.uniqueId,
            }))
        };

        // Separar mes y año para enviar al backend si se unificó en el frontend
        if (dataToSend.paymentMethod === 'credit-debit-card' && dataToSend.card_expiry) {
            const [month, year] = dataToSend.card_expiry.split('/');
            dataToSend.card_expiry_month = month;
            dataToSend.card_expiry_year = year.length === 2 ? `20${year}` : year; // Asegurar año de 4 dígitos para backend
        } else {
            dataToSend.card_expiry_month = null;
            dataToSend.card_expiry_year = null;
        }
        delete dataToSend.card_expiry; // Eliminar el campo unificado para el envío

        // Limpiar datos no relevantes según el método de pago seleccionado
        if (dataToSend.paymentMethod !== 'mobile-payment') {
            dataToSend.banco_remitente = null;
            dataToSend.numero_telefono_remitente = null;
            dataToSend.cedula_remitente = null;
        }
        if (dataToSend.paymentMethod !== 'credit-debit-card') {
            dataToSend.card_number = null;
            dataToSend.card_holder_name = null;
            dataToSend.card_cvv = null;
        }
        // Si el pago no es ni móvil ni tarjeta, limpiar la referencia también
        if (dataToSend.paymentMethod !== 'mobile-payment' && dataToSend.paymentMethod !== 'credit-debit-card') {
            dataToSend.numero_referencia_pago = null;
        }

        router.post('/checkout', dataToSend, {
            onStart: () => setLoading(true),
            onFinish: () => setLoading(false),
            onSuccess: () => {
                setLocalCartItems([]);
                // Opcional: Redirigir a una página de confirmación
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

    const handleQuantityChange = (uniqueId, change) => {
        setLocalCartItems((prevItems) => {
            return prevItems.map((item) => {
                if (item.uniqueId === uniqueId) {
                    const newQuantity = item.quantity + change;
                    return {
                        ...item,
                        quantity: newQuantity > 0 ? newQuantity : 1,
                        subtotal: (newQuantity > 0 ? newQuantity : 1) * (item.price || 0)
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
                                    <option value="credit-debit-card">Tarjeta de Crédito o Débito</option>
                                    <option value="mobile-payment">Pago Móvil</option>
                                </select>
                                {errors.paymentMethod && <p className="text-red-500 text-xs italic mt-1">{errors.paymentMethod}</p>}
                            </div>

                            {/* Modal de información para Pago Móvil */}
                            <Modal show={showMobilePaymentInfoModal} onClose={() => setShowMobilePaymentInfoModal(false)}>
                                <div className="p-6">
                                    <h3 className="text-2xl font-bold text-center text-blue-600 mb-4">¡Realiza tu Pago Móvil!</h3>
                                    <p className="text-gray-700 mb-2 text-center text-sm font-semibold">
                                        El monto en Bolívares se calcula con la tasa actual del BCV.
                                    </p>
                                    {currentBcvRate > 0 && (
                                        <p className="text-gray-600 mb-4 text-center text-xs">
                                            Tasa BCV actual: **1 USD = {currentBcvRate.toFixed(2)} Bs**
                                        </p>
                                    )}

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
                                            <strong>Monto a pagar:</strong>
                                            <span className="text-green-800 ml-2">${totalUSD.toFixed(2)}</span>
                                            {currentBcvRate > 0 && (
                                                <span className="text-green-800 ml-2">({totalBs.toFixed(2)} Bs)</span>
                                            )}
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

                            {/* Formulario de detalles de pago */}
                            {showPaymentDetailsForm && (
                                <div className="mt-8">
                                    <h3 className="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Confirma tu Pago</h3>

                                    {/* Campos para Pago Móvil */}
                                    {formData.paymentMethod === 'mobile-payment' && (
                                        <Fragment>
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
                                                label="Número de Referencia de Pago Móvil"
                                                value={formData.numero_referencia_pago}
                                                onChange={handleChange}
                                                required={formData.paymentMethod === 'mobile-payment'}
                                                placeholder="Ingrese todos los números del comprobante"
                                                error={errors.numero_referencia_pago}
                                            />
                                        </Fragment>
                                    )}

                                    {/* Campos para Tarjeta de Crédito o Débito */}
                                    {formData.paymentMethod === 'credit-debit-card' && (
                                        <Fragment>
                                            <InputField
                                                type="text"
                                                name="card_number"
                                                label="Número de Tarjeta"
                                                value={formData.card_number}
                                                onChange={handleChange}
                                                required={formData.paymentMethod === 'credit-debit-card'}
                                                placeholder="XXXX XXXX XXXX XXXX"
                                                maxLength={19}
                                                inputMode="numeric"
                                                pattern="[\d\s]{13,19}"
                                                autoComplete="cc-number"
                                                error={errors.card_number}
                                            />
                                            {cardType && (
                                                <p className="text-gray-600 text-sm mb-4">Tipo de tarjeta: <strong className="text-blue-700">{cardType}</strong></p>
                                            )}

                                            <InputField
                                                type="text"
                                                name="card_holder_name"
                                                label="Nombre del Tarjetahabiente"
                                                value={formData.card_holder_name}
                                                onChange={handleChange}
                                                required={formData.paymentMethod === 'credit-debit-card'}
                                                placeholder="Como aparece en la tarjeta"
                                                autoComplete="cc-name"
                                                error={errors.card_holder_name}
                                            />

                                            {/* Campo unificado para Mes y Año de Vencimiento */}
                                            <InputField
                                                type="text"
                                                name="card_expiry"
                                                label="Fecha de Vencimiento (MM/AA o MM/AAAA)"
                                                value={formData.card_expiry}
                                                onChange={handleChange}
                                                required={formData.paymentMethod === 'credit-debit-card'}
                                                placeholder="MM/AA"
                                                maxLength={7}
                                                inputMode="numeric"
                                                pattern="(0[1-9]|1[0-2])\/?([0-9]{2}|[0-9]{4})"
                                                autoComplete="cc-exp"
                                                error={errors.card_expiry}
                                            />

                                            <InputField
                                                type="password"
                                                name="card_cvv"
                                                label="CVV/CVC"
                                                value={formData.card_cvv}
                                                onChange={handleChange}
                                                required={formData.paymentMethod === 'credit-debit-card'}
                                                placeholder="XXX o XXXX"
                                                maxLength={4}
                                                inputMode="numeric"
                                                pattern="\d{3,4}"
                                                autoComplete="cc-csc"
                                                error={errors.card_cvv}
                                            />
                                        </Fragment>
                                    )}

                                    {/* Campo de referencia de pago, si aplica */}
                                    {formData.paymentMethod !== 'mobile-payment' && formData.paymentMethod !== '' && (
                                        <InputField
                                            type="text"
                                            name="numero_referencia_pago"
                                            label="Número de Referencia de Pago (Opcional)"
                                            value={formData.numero_referencia_pago}
                                            onChange={handleChange}
                                            required={false}
                                            placeholder="Referencia de tu pago (si aplica)"
                                            error={errors.numero_referencia_pago}
                                        />
                                    )}
                                </div>
                            )}

                            {/* Botón de envío */}
                            <div className="mt-8">
                                <button
                                    type="submit"
                                    className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled={loading}
                                >
                                    {loading ? 'Procesando...' : 'Confirmar Pedido'}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Resumen del pedido (siempre visible) */}
                    <div className="w-full md:w-1/4 px-4 mt-8 md:mt-0">
                        <div className="bg-white shadow-md rounded p-6">
                            <h2 className="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">Resumen del Pedido</h2>
                            {localCartItems.length === 0 ? (
                                <p className="text-gray-600">Tu carrito está vacío.</p>
                            ) : (
                                <ul>
                                    {localCartItems.map((item) => (
                                        <li key={item.uniqueId} className="flex justify-between items-center mb-2 pb-2 border-b border-gray-200 last:border-b-0">
                                            <div>
                                                <span className="font-semibold text-gray-800">{item.product_name}</span>
                                                <div className="text-sm text-gray-600">
                                                    {item.client_type && <p>Tipo: {item.client_type}</p>}
                                                    {item.selectedDate && <p>Fecha: {item.selectedDate}</p>}
                                                    {item.selectedTime && <p>Hora: {item.selectedTime}</p>}
                                                </div>
                                                <div className="flex items-center mt-1">
                                                    <button
                                                        type="button"
                                                        onClick={() => handleQuantityChange(item.uniqueId, -1)}
                                                        className="bg-gray-200 text-gray-700 px-2 py-1 rounded-l hover:bg-gray-300"
                                                    >
                                                        -
                                                    </button>
                                                    <span className="bg-gray-100 text-gray-800 px-3 py-1">{item.quantity}</span>
                                                    <button
                                                        type="button"
                                                        onClick={() => handleQuantityChange(item.uniqueId, 1)}
                                                        className="bg-gray-200 text-gray-700 px-2 py-1 rounded-r hover:bg-gray-300"
                                                    >
                                                        +
                                                    </button>
                                                </div>
                                            </div>
                                            <span className="font-bold text-gray-800">${((item.price || 0) * item.quantity).toFixed(2)}</span>
                                        </li>
                                    ))}
                                </ul>
                            )}
                            <div className="mt-4 pt-4 border-t border-gray-300">
                                <div className="flex justify-between items-center mb-2">
                                    <span className="font-semibold text-lg text-gray-700">Subtotal USD:</span>
                                    <span className="font-bold text-lg text-gray-800">${totalUSD.toFixed(2)}</span>
                                </div>
                                {currentBcvRate > 0 && (
                                    <div className="flex justify-between items-center mb-2">
                                        <span className="font-semibold text-lg text-gray-700">Total en Bs (BCV):</span>
                                        <span className="font-bold text-lg text-green-700">{totalBs.toFixed(2)} Bs</span>
                                    </div>
                                )}
                                {currentBcvRate > 0 && (
                                    <p className="text-gray-600 text-xs text-right mt-1">Tasa BCV: 1 USD = {currentBcvRate.toFixed(2)} Bs</p>
                                )}
                                {/* Aquí podrías añadir descuentos o costos de envío si aplicaran */}
                                <div className="flex justify-between items-center mt-4 pt-4 border-t border-gray-300">
                                    <span className="font-bold text-xl text-gray-800">Total a Pagar:</span>
                                    <span className="font-bold text-xl text-blue-600">${totalUSD.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Checkout;