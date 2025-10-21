import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { products } from '../data/products';
import { useCart } from '../context/CartContext';
import './ProductPage.scss';

const ProductPage = () => {
  const { productId } = useParams();
  const { addToCart } = useCart();

  const [product, setProduct] = useState(null);
  const [selectedImage, setSelectedImage] = useState(0);
  const [quantity, setQuantity] = useState(1);
  const [relatedProducts, setRelatedProducts] = useState([]);

  useEffect(() => {
    // 🔹 Asegúrate de parsear productId si tus IDs son numéricos
    const foundProduct = products.find((p) => p.id === parseInt(productId));
    setProduct(foundProduct);

    if (foundProduct) {
      const related = products
        .filter((p) => p.id !== foundProduct.id && p.category === foundProduct.category)
        .slice(0, 4);
      setRelatedProducts(related);
    }
  }, [productId]);

  const handleAddToCart = () => {
    if (product) {
      addToCart(product, quantity);
      alert(`${quantity} ${product.name} agregado(s) al carrito`);
    }
  };

  if (!product) {
    return (
      <div className="product-page">
        <div className="container">
          <div className="loading">Producto no encontrado</div>
        </div>
      </div>
    );
  }

  // Simular múltiples imágenes
  const productImages = [
    product.image,
    product.image,
    product.image,
    product.image
  ];

  const hasDiscount = product.originalPrice > product.price;
  const discountPercentage = hasDiscount
    ? Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100)
    : 0;

  return (
    <div className="product-page">
      <div className="container">
        {/* Breadcrumb */}
        <div className="breadcrumb">
          <a href="/">Inicio</a> / 
          <a href={`/category/${product.category}`}>{product.category}</a> / 
          <span>{product.name}</span>
        </div>

        <div className="product-content">
          {/* Galería de imágenes */}
          <div className="product-gallery">
            <div className="main-image">
              <img 
                src={productImages[selectedImage]} 
                alt={product.name}
                className="product-image"
              />
              {hasDiscount && (
                <div className="discount-badge">-{discountPercentage}%</div>
              )}
            </div>
            
            <div className="image-thumbnails">
              {productImages.map((image, index) => (
                <button
                  key={index}
                  className={`thumbnail ${selectedImage === index ? 'active' : ''}`}
                  onClick={() => setSelectedImage(index)}
                >
                  <img src={image} alt={`Vista ${index + 1}`} />
                </button>
              ))}
            </div>
          </div>

          {/* Información del producto */}
          <div className="product-info">
            <h1 className="product-title">{product.name}</h1>
            
            <div className="product-brand">
              Marca: <span>{product.brand}</span>
            </div>

            <div className="product-rating">
              <div className="stars">
                {'★'.repeat(Math.floor(product.rating))}
                {'☆'.repeat(5 - Math.floor(product.rating))}
              </div>
              <span className="rating-value">({product.rating})</span>
            </div>

            <div className="product-price">
              {hasDiscount && (
                <div className="original-price">
                  <span className="price-label">Precio regular:</span>
                  <span className="price-value">${product.originalPrice.toLocaleString()}</span>
                </div>
              )}
              
              <div className="current-price">
                <span className="price-label">{hasDiscount ? 'Precio con descuento:' : 'Precio:'}</span>
                <span className="price-value">${product.price.toLocaleString()}</span>
              </div>

              {hasDiscount && (
                <div className="savings">
                  Ahorras: ${(product.originalPrice - product.price).toLocaleString()}
                </div>
              )}
            </div>

            <div className="product-features">
              <h3>Características principales</h3>
              <ul>
                {product.features.map((feature, index) => (
                  <li key={index}>{feature}</li>
                ))}
              </ul>
            </div>

            <div className="purchase-options">
              <div className="quantity-selector">
                <label>Cantidad:</label>
                <div className="quantity-controls">
                  <button 
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="quantity-btn"
                  >
                    -
                  </button>
                  <span className="quantity">{quantity}</span>
                  <button 
                    onClick={() => setQuantity(quantity + 1)}
                    className="quantity-btn"
                  >
                    +
                  </button>
                </div>
              </div>

              <div className="action-buttons">
                <button 
                  onClick={handleAddToCart}
                  className="add-to-cart-btn primary"
                >
                  Agregar al carrito
                </button>
                
                <button className="buy-now-btn">
                  Comprar ahora
                </button>
              </div>
            </div>

            <div className="product-specs">
              <h3>Especificaciones técnicas</h3>
              <div className="specs-grid">
                <div className="spec-item">
                  <span className="spec-label">Marca:</span>
                  <span className="spec-value">{product.brand}</span>
                </div>
                <div className="spec-item">
                  <span className="spec-label">Categoría:</span>
                  <span className="spec-value">{product.category}</span>
                </div>
                <div className="spec-item">
                  <span className="spec-label">Garantía:</span>
                  <span className="spec-value">12 meses</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Productos relacionados */}
        {relatedProducts.length > 0 && (
          <section className="related-products">
            <h2>Productos relacionados</h2>
            <div className="related-grid">
              {relatedProducts.map((relatedProduct) => (
                <div key={relatedProduct.id} className="related-item">
                  <img src={relatedProduct.image} alt={relatedProduct.name} />
                  <h4>{relatedProduct.name}</h4>
                  <p>${relatedProduct.price.toLocaleString()}</p>
                  <a 
                    href={`/product/${relatedProduct.id}`}
                    className="view-product-btn"
                  >
                    Ver producto
                  </a>
                </div>
              ))}
            </div>
          </section>
        )}
      </div>
    </div>
  );
};

export default ProductPage;
