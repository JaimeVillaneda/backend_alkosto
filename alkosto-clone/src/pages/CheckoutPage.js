// src/pages/CheckoutPage.js
import React, { useState } from "react";
import { useCart } from "../context/CartContext";
import { useNavigate } from "react-router-dom";
import "./CheckoutPage.scss";

const CheckoutPage = () => {
  const { cartItems, totalPrice, clearCart } = useCart();
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    name: "",
    email: "",
    address: "",
    city: "",
    paymentMethod: "credit-card",
  });

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    // Simulación de compra finalizada
    clearCart();
    navigate("/order-confirmation", { state: { ...formData, total: totalPrice } });
  };

  if (cartItems.length === 0) {
    return (
      <div className="checkout-page empty">
        <h2>No tienes productos en el carrito</h2>
      </div>
    );
  }

  return (
    <div className="checkout-page">
      <h1>Finalizar compra</h1>
      <div className="checkout-container">
        
        {/* Formulario */}
        <form className="checkout-form" onSubmit={handleSubmit}>
          <h2>Datos de envío</h2>
          <input
            type="text"
            name="name"
            placeholder="Nombre completo"
            value={formData.name}
            onChange={handleChange}
            required
          />
          <input
            type="email"
            name="email"
            placeholder="Correo electrónico"
            value={formData.email}
            onChange={handleChange}
            required
          />
          <input
            type="text"
            name="address"
            placeholder="Dirección"
            value={formData.address}
            onChange={handleChange}
            required
          />
          <input
            type="text"
            name="city"
            placeholder="Ciudad"
            value={formData.city}
            onChange={handleChange}
            required
          />

          <h2>Método de pago</h2>
          <select
            name="paymentMethod"
            value={formData.paymentMethod}
            onChange={handleChange}
          >
            <option value="credit-card">Tarjeta de crédito</option>
            <option value="debit-card">Tarjeta débito</option>
            <option value="cash">Pago en efectivo</option>
          </select>

          <button type="submit" className="confirm-btn">Confirmar compra</button>
        </form>

        {/* Resumen */}
        <div className="checkout-summary">
          <h2>Resumen del pedido</h2>
          <ul>
            {cartItems.map((item, index) => (
              <li key={index}>
                {item.name} x {item.quantity} = ${(
                  item.price * item.quantity
                ).toLocaleString()}
              </li>
            ))}
          </ul>
          <p className="total">Total: ${totalPrice.toLocaleString()}</p>
        </div>
      </div>
    </div>
  );
};

export default CheckoutPage;
