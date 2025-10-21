import React from 'react';
import './ProductCard.scss';

const ProductCard = ({ product }) => {
  const hasDiscount = product.originalPrice > product.price;
  const discountPercentage = hasDiscount 
    ? Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100)
    : 0;

  return (
    <div className="product-card">
      <div className="product-image">
        <img src={product.image} alt={product.name} />
        {hasDiscount && (
          <span className="discount-badge">-{discountPercentage}%</span>
        )}
      </div>
      
      <div className="product-info">
        <h3 className="product-name">{product.name}</h3>
        <p className="product-brand">{product.brand}</p>
        
        <div className="product-price">
          {hasDiscount && (
            <span className="original-price">${product.originalPrice.toLocaleString()}</span>
          )}
          <span className="current-price">${product.price.toLocaleString()}</span>
        </div>
        
        <div className="product-features">
          {product.features.slice(0, 2).map((feature, index) => (
            <span key={index} className="feature-tag">{feature}</span>
          ))}
        </div>
        
        <button className="add-to-cart-btn">Agregar al carrito</button>
      </div>
    </div>
  );
};

export default ProductCard;