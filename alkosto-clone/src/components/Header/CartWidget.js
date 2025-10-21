// src/components/Header/CartWidget.js
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useCart } from '../../context/CartContext'; // ✅ corregido
import './CartWidget.scss';

const CartWidget = () => {
  const { cartItems, totalQuantity } = useCart(); // ✅ usamos useCart
  const [isOpen, setIsOpen] = useState(false);

  return (
    <div 
      className="cart-widget" 
      onMouseEnter={() => setIsOpen(true)} 
      onMouseLeave={() => setIsOpen(false)}
    >
      {/* Icono y texto */}
      <Link to="/cart" className="cart-link">
        <i className="alk-icon-cart"></i>
        <span>Mi carrito</span>
        {totalQuantity > 0 && <span className="cart-count">{totalQuantity}</span>}
      </Link>

      {/* Dropdown con productos */}
      {isOpen && cartItems.length > 0 && (
        <div className="cart-dropdown">
          <ul>
            {cartItems.map((item, index) => (
              <li key={index} className="cart-item">
                <img src={item.image} alt={item.name} />
                <div>
                  <span className="cart-item-name">{item.name}</span>
                  <span className="cart-item-price">
                    ${item.price.toLocaleString()} x {item.quantity}
                  </span>
                </div>
              </li>
            ))}
          </ul>
          <Link to="/cart" className="go-to-cart">Ver carrito</Link>
        </div>
      )}
    </div>
  );
};

export default CartWidget;
