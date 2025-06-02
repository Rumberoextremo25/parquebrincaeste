import React, { useState, useEffect } from 'react';
import Layout from "@/Layouts/Layout";
import './Checkout.css';
import BannerHero from '@/Components/Hero/BannerHero';

const Checkout = ({ cartItems, user }) => {
    const [formData, setFormData] = useState({
        nombre_completo: '',
        correo: '',
        telefono: '',
        direccion: '',
        ciudad: '',
        codigo_postal: '',
        promoCode: '',
        paymentMethod: '',
        nombre_banco: '',
        numero_telefono: '',
        cedula: '',
        clave_dinamica: '',
        monto: 0
    });
    const [loading, setLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [total, setTotal] = useState(0);
    const [showMobilePaymentInfo, setShowMobilePaymentInfo] = useState(false);

    useEffect(() => {
        const totalAmount = cartItems.reduce((acc, item) => acc + item.product.price * item.quantity, 0);
        setTotal(totalAmount);
        setFormData((prevData) => ({ ...prevData, monto: totalAmount })); // Establecer el monto automáticamente
    }, [cartItems]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({ ...formData, [name]: value });

        if (name === 'paymentMethod' && value === 'mobile-payment') {
            setShowMobilePaymentInfo(true);
        } else {
            setShowMobilePaymentInfo(false);
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
                body: JSON.stringify({ ...formData, total })
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

    const InputField = ({ type, name, label, value, onChange, required }) => (
        <div className="form-group">
            <label htmlFor={name}>{label}</label>
            <input type={type} name={name} id={name} required={required} value={value} onChange={onChange} />
        </div>
    );

    const handleQuantityChange = (productId, change) => {
        setCartItems((prevItems) => {
            return prevItems.map((item) => {
                if (item.product.id === productId) {
                    const newQuantity = item.quantity + change;
                    return {
                        ...item,
                        quantity: newQuantity > 0 ? newQuantity : 1,
                    };
                }
                return item;
            });
        });
    };

    return (
        <Layout>
            <BannerHero img="https://wallpaperbat.com/img/423222-eagle-mountain-sunset-minimalist-1366x768-resolution.jpg" title="Checkout" />
            <div className="checkout-container flex flex-wrap">

                <div className="checkout-form-container flex-1 p-4">
                    {errorMessage && <div className="error-message">{errorMessage}</div>}
                    <form className="checkout-form" onSubmit={handleSubmit}>
                        <h2 className="section-title">Detalles del Cliente</h2>
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

                        <h2 className="section-title">Dirección de Facturación</h2>
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

                        <h2 className="section-title">Código de Promoción</h2>
                        <InputField
                            type="text"
                            name="promoCode"
                            label="Introduce tu código"
                            value={formData.promoCode}
                            onChange={handleChange}
                        />

                        <h2 className="section-title">Pasarela de Pago</h2>
                        <div className="form-group">
                            <label htmlFor="payment-method">Selecciona tu método de pago</label>
                            <select name="paymentMethod" id="payment-method" required value={formData.paymentMethod} onChange={handleChange}>
                                <option value="">Seleccione...</option>
                                <option value="mobile-payment">Pago Móvil</option>
                                <option value="in-store">Pago en Caja</option>
                            </select>
                        </div>

                        {/* Formulario adicional para Pago Móvil */}
                        {showMobilePaymentInfo && (
                            <div className="mobile-payment-info">
                                <h3>Información de Pago Móvil</h3>
                                {['nombre_banco', 'numero_telefono', 'cedula', 'clave_dinamica'].map((field) => (
                                    <InputField
                                        key={field}
                                        type={field === 'numero_telefono' ? 'tel' : 'text'}
                                        name={field}
                                        label={
                                            field === 'nombre_banco' ? 'Nombre del Banco' :
                                                field === 'numero_telefono' ? 'Número de Teléfono' :
                                                    field === 'cedula' ? 'Cédula' :
                                                        'Clave Dinámica'
                                        }
                                        value={formData[field]}
                                        onChange={handleChange}
                                        required
                                    />
                                ))}
                                <InputField
                                    type="number"
                                    name="monto"
                                    label="Monto"
                                    value={formData.monto} // El monto se establece automáticamente
                                    readOnly // Campo de solo lectura
                                />
                            </div>
                        )}

                        <button type="submit" className="btn-submit" disabled={loading}>
                            {loading ? 'Procesando...' : 'Completar Compra'}
                        </button>
                    </form>
                </div>

                <div className="col-25 p-4" style={{ marginLeft: '20px' }}>
                    <div className="container">
                        <h4>Cart
                            <span className="price" style={{ color: 'black' }}>
                                <i className="fa fa-shopping-cart"></i>
                                <b>{cartItems.length}</b>
                            </span>
                        </h4>
                        {cartItems.map((item) => (
                            <div key={item.product.id} className="cart-item">
                                <p>
                                    <a href="#">{item.product.name}</a>
                                    <span className="price">${item.product.price.toFixed(2)}</span>
                                </p>
                                <div className="quantity-controls">
                                    <button
                                        onClick={() => handleQuantityChange(item.product.id, -1)}
                                        disabled={item.quantity <= 1}
                                    >
                                        -
                                    </button>
                                    <span className="quantity">{item.quantity}</span>
                                    <button
                                        onClick={() => handleQuantityChange(item.product.id, 1)}
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        ))}
                        <hr />
                        <p>Total <span className="price" style={{ color: 'black' }}><b>${total.toFixed(2)}</b></span></p>
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Checkout;
