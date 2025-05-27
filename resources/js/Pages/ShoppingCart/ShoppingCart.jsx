import React, { useState } from 'react';
import './ShoppingCart.css';
import Layout from '@/Layouts/Layout';
import BannerHero from '@/Components/Hero/BannerHero';

const ShoppingCart = ({ items = [], onRemoveItem, onCheckout }) => {
    const totalAmount = items.reduce((total, item) => {
        const price = item.price || 0;
        const quantity = item.quantity || 0;
        return total + price * quantity;
    }, 0);

    return (
        <Layout>
            <BannerHero img="https://wallpaperbat.com/img/423222-eagle-mountain-sunset-minimalist-1366x768-resolution.jpg" title="Carrito" />
            <div className="cart-container">
                    <h1 className="cart-title">Carrito de Compras</h1>
                    {items.length === 0 ? (
                        <div className="empty-cart">
                            <p>Tu carrito está vacío.</p>
                            <button className="checkout-btn">Seguir Comprando</button>
                        </div>
                    ) : (
                        <div>
                            <table className="cart-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.map((item) => (
                                        <tr key={item.id} className="cart-item">
                                            <td className="item-details">
                                                <h3 className="item-name">{item.name}</h3>
                                            </td>
                                            <td className="quantity-control">
                                                <button onClick={() => onDecreaseQuantity(item.id)}>-</button>
                                                <input
                                                    type="number"
                                                    value={item.quantity}
                                                    onChange={(e) => onUpdateQuantity(item.id, parseInt(e.target.value))}
                                                />
                                                <button onClick={() => onIncreaseQuantity(item.id)}>+</button>
                                            </td>
                                            <td className="item-price">${item.price.toFixed(2)}</td>
                                            <td className="item-subtotal">${(item.price * item.quantity).toFixed(2)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                            <div className="total">
                                Total: <span>${totalAmount.toFixed(2)}</span>
                            </div>
                            <div className="actions">
                                <button className="checkout-btn">Seguir Comprando</button>
                                <button className="checkout-btn" onClick={onCheckout}>Proceder al Pago</button>
                            </div>
                        </div>
                    )}
                </div>
        </Layout>

    );
};

export default ShoppingCart;