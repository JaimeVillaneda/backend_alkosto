import React from 'react';
import { categories } from '../../data/categories';
import './Navigation.scss';

const Navigation = ({ isOpen, onClose }) => {
  return (
    <>
      <div className={`navigation ${isOpen ? 'open' : ''}`}>
        <div className="navigation-header">
          <h2>Categorías</h2>
          <button className="close-button" onClick={onClose}>
            ✕
          </button>
        </div>
        
        <div className="navigation-content">
          <a href="/" className="nav-item">Inicio</a>
          <a href="/ofertas" className="nav-item">Hiperofertas</a>
          
          <div className="nav-section">
            <h3 className="nav-section-title">Productos</h3>
            {categories.map(category => (
              <a 
                key={category.id} 
                href={`/category/${category.id}`}
                className="nav-item nav-category"
              >
                <img 
                  src={category.image} 
                  alt={category.name}
                  className="nav-icon"
                  onError={(e) => {
                    e.target.src = '/images/placeholder.png';
                  }}
                />
                {category.name}
              </a>
            ))}
          </div>
          
          <div className="nav-section">
            <h3 className="nav-section-title">Servicios</h3>
            <a href="/servicio-tecnico" className="nav-item">Servicio Técnico</a>
            <a href="/garantias" className="nav-item">Garantías</a>
            <a href="/financiacion" className="nav-item">Financiación</a>
          </div>
        </div>
      </div>
      
      <div 
        className={`navigation-overlay ${isOpen ? 'open' : ''}`} 
        onClick={onClose}
      />
    </>
  );
};

export default Navigation;