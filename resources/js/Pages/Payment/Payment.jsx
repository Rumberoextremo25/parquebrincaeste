import React, { useState } from 'react';
import Layout from "@/Layouts/Layout";
import './Payment.css'; // Asegúrate de tener este archivo CSS
import BannerHero from '@/Components/Hero/BannerHero';

const Payment = () => {
    const [paymentMethod, setPaymentMethod] = useState('');
    const [dynamicKey, setDynamicKey] = useState('');
    const [paymentDate, setPaymentDate] = useState('');
    const [amount, setAmount] = useState('');
    const [loading, setLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrorMessage('');

        // Aquí va la lógica de envío del formulario
        // ...

        setLoading(false);
    };

    return (
        <Layout>
            <BannerHero img="https://wallpaperbat.com/img/423222-eagle-mountain-sunset-minimalist-1366x768-resolution.jpg" title="Procesar Compra" />
            <div className="payment-container">
                <h1 className="title">Pasarela de Pago</h1>
                {errorMessage && <div className="error-message">{errorMessage}</div>}
                <form className="payment-form" onSubmit={handleSubmit}>
                    <h2 className="section-title">Selecciona tu Método de Pago</h2>
                    <div className="form-group">
                        <label>
                            <input
                                type="radio"
                                value="mobile-payment"
                                checked={paymentMethod === 'mobile-payment'}
                                onChange={(e) => setPaymentMethod(e.target.value)}
                                required
                            />
                            Pago Móvil
                        </label>
                    </div>

                    {paymentMethod && (
                        <>
                            <h2 className="section-title">Detalles del Pago</h2>
                            {paymentMethod === 'mobile-payment' && (
                                <>
                                    <div className="form-group">
                                        <label htmlFor="dynamic-key">Clave Dinámica / Token</label>
                                        <input
                                            type="text"
                                            id="dynamic-key"
                                            value={dynamicKey}
                                            onChange={(e) => setDynamicKey(e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="payment-date">Fecha de Pago</label>
                                        <input
                                            type="date"
                                            id="payment-date"
                                            value={paymentDate}
                                            onChange={(e) => setPaymentDate(e.target.value)}
                                            required
                                        />
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="amount">Monto</label>
                                        <input
                                            type="number"
                                            id="amount"
                                            value={amount}
                                            onChange={(e) => setAmount(e.target.value)}
                                            required
                                        />
                                    </div>
                                </>
                            )}
                        </>
                    )}

                    <button type="submit" className="btn-submit" disabled={loading}>
                        {loading ? 'Procesando...' : 'Completar Pago'}
                    </button>
                </form>
            </div>
        </Layout>
    );
};

export default Payment;