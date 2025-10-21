import React from 'react';
import './Footer.scss';

const Footer = () => {
  return (
    <footer className="footer">
      {/* Main Footer */}
      <div className="footer-main">
        <div className="container">
          <div className="footer-content">
            {/* Company Info */}
            <div className="footer-section">
              <h3>Alkosto</h3>
              <div className="footer-links">
                <a href="/nosotros">Nosotros</a>
                <a href="/tiendas">Nuestras Tiendas</a>
                <a href="/trabaja-con-nosotros">Trabaja con Nosotros</a>
                <a href="/contacto">Contáctenos</a>
              </div>
            </div>

            {/* Services */}
            <div className="footer-section">
              <h3>Servicio al Cliente</h3>
              <div className="footer-links">
                <a href="/garantias">Garantías</a>
                <a href="/devoluciones">Devoluciones</a>
                <a href="/preguntas-frecuentes">Preguntas Frecuentes</a>
                <a href="/terminos-condiciones">Términos y Condiciones</a>
              </div>
            </div>

            {/* Payment Methods */}
            <div className="footer-section">
              <h3>Medios de Pago</h3>
              <div className="payment-methods">
                <div className="payment-icon">💳</div>
                <div className="payment-icon">🏦</div>
                <div className="payment-icon">📱</div>
                <div className="payment-icon">💰</div>
              </div>
              <div className="footer-links">
                <a href="/medios-pago">Ver todos los medios de pago</a>
              </div>
            </div>

            {/* Contact */}
            <div className="footer-section">
              <h3>Contáctanos</h3>
              <div className="contact-info">
                <div className="contact-item">
                  <span className="contact-icon">📞</span>
                  <span>Venta: (601) 746 8001</span>
                </div>
                <div className="contact-item">
                  <span className="contact-icon">🛠️</span>
                  <span>Servicio: (601) 407 3033</span>
                </div>
                <div className="contact-item">
                  <span className="contact-icon">✉️</span>
                  <span>servicio@alkosto.com</span>
                </div>
              </div>
            </div>

            {/* Social Media */}
            <div className="footer-section">
              <h3>Síguenos</h3>
              <div className="social-media">
                <a 
  href="https://www.facebook.com/alkostohiperahorro/" 
  target="_blank" 
  rel="noopener noreferrer" 
  className="social-icon"
>
  📘
</a>
<a 
  href="https://www.instagram.com/alkostooficial/" 
  target="_blank" 
  rel="noopener noreferrer" 
  className="social-icon"
>
  📸
</a>
<a 
  href="https://x.com/alkosto" 
  target="_blank" 
  rel="noopener noreferrer" 
  className="social-icon"
>
  🐦
</a>
<a 
  href="https://www.youtube.com/results?search_query=alkosto" 
  target="_blank" 
  rel="noopener noreferrer" 
  className="social-icon"
>
  ▶️
</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Bottom Footer */}
      <div className="footer-bottom">
        <div className="container">
          <div className="footer-bottom-content">
            <div className="copyright">
              © 2024 Alkosto. Todos los derechos reservados.
            </div>
            <div className="security-badges">
              <span className="security-badge">🔒</span>
              <span className="security-badge">🛡️</span>
              <span className="security-badge">⭐</span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;