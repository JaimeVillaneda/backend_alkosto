// src/pages/OrderConfirmation.js
import React from "react";
import { useLocation, Link } from "react-router-dom";
import "./OrderConfirmation.scss";

const OrderConfirmation = () => {
  const location = useLocation();
  const { name, email, total } = location.state || {};

  return (
    <div className="order-confirmation">
      <h1>¡Gracias por tu compra, {name}!</h1>
      <p>Se enviará la confirmación a: <strong>{email}</strong></p>
      <p>Monto total pagado: <strong>${total?.toLocaleString()}</strong></p>

      <Link to="/" className="back-home">Volver al inicio</Link>
    </div>
  );
};

export default OrderConfirmation;
