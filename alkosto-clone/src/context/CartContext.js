// src/context/CartContext.js
import React, { createContext, useState, useContext } from 'react';

// Crear el contexto
export const CartContext = createContext();

// Proveedor del carrito
export const CartProvider = ({ children }) => {
  const [cartItems, setCartItems] = useState([]);

  // Cantidad total de productos
  const totalQuantity = cartItems.reduce((acc, item) => acc + item.quantity, 0);

  // Precio total del carrito
  const totalPrice = cartItems.reduce((acc, item) => acc + item.price * item.quantity, 0);

  // Agregar al carrito
  const addToCart = (product, quantity = 1) => {
    setCartItems((prevItems) => {
      const existing = prevItems.find((item) => item.id === product.id);
      if (existing) {
        return prevItems.map((item) =>
          item.id === product.id
            ? { ...item, quantity: item.quantity + quantity }
            : item
        );
      } else {
        return [...prevItems, { ...product, quantity }];
      }
    });
  };

  // Eliminar un producto
  const removeFromCart = (productId) => {
    setCartItems((prevItems) => prevItems.filter((item) => item.id !== productId));
  };

  // Vaciar carrito
  const clearCart = () => {
    setCartItems([]);
  };

  return (
    <CartContext.Provider
      value={{
        cartItems,
        addToCart,
        removeFromCart,
        clearCart,
        totalQuantity,
        totalPrice,
      }}
    >
      {children}
    </CartContext.Provider>
  );
};

// ✅ Hook personalizado para usar en cualquier parte
export const useCart = () => useContext(CartContext);
