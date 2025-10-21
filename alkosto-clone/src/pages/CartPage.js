// src/pages/CartPage.js
import React from "react";
import { Link } from "react-router-dom";
import { useCart } from "../context/CartContext";
import "./CartPage.scss";

const CartPage = () => {
  const { cartItems, removeFromCart, clearCart, totalPrice } = useCart();

  return (
    <div className="cart-page">
      <h1>Carrito de compras</h1>
      <h3>Productos en el carrito: ({cartItems.length})</h3>

      {cartItems.length === 0 ? (
        <div className="cart-empty">
          <div className="empty-alert">
            <span className="icon">⚠️</span>
            <p>Tu carrito de compras está vacío.</p>
          </div>
          <p>
            Puedes seleccionar un producto para seguir comprando <br />
            o volver al inicio para ver las últimas ofertas.
          </p>
          <Link to="/" className="back-home-btn">
            Volver al inicio
          </Link>
        </div>
      ) : (
        <div className="cart-container">
          {/* Lista de productos */}
          <div className="cart-items">
            {cartItems.map((item) => (
              <div className="cart-item" key={item.id}>
                <img src={item.image} alt={item.name} />
                <div className="cart-item-details">
                  <h3>{item.name}</h3>
                  <p className="price">
                    Precio: ${item.price.toLocaleString()}
                  </p>
                  <p className="quantity">Cantidad: {item.quantity}</p>
                  <p className="subtotal">
                    Subtotal: ${(item.price * item.quantity).toLocaleString()}
                  </p>
                  <button
                    onClick={() => removeFromCart(item.id)}
                    className="remove-btn"
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            ))}
          </div>

          {/* Resumen */}
          <div className="cart-summary">
            <h2>Resumen de compra</h2>
            <p>Total: <span>${totalPrice.toLocaleString()}</span></p>
            <Link to="/checkout" className="checkout-btn">
              Finalizar compra
            </Link>
            <button onClick={clearCart} className="clear-btn">
              Vaciar carrito
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default CartPage;
