import React, { useState } from "react";
import CartWidget from "./CartWidget";
import SearchBar from "./SearchBar";
import LoginModal from "../common/Modal/LoginModal";
import "./Header.scss";

// Logo desde /src/assets/images/logo
import alkostoLogo from "../../assets/images/logo/alkosto-logo-header.svg";

const Header = () => {
  const [isLoginOpen, setIsLoginOpen] = useState(false);

  return (
    <header className="header">
      {/* 🔹 Header principal */}
      <div className="header-main">
        <div className="container header-main-container">

          {/* ===== Fila superior ===== */}
          <div className="header-top-row">
            {/* Logo */}
            <div className="header-logo">
              <a href="/" aria-label="Ir al inicio">
                <img src={alkostoLogo} alt="Alkosto" />
              </a>
            </div>

            {/* Teléfonos + Links (tipografía más grande) */}
            <div className="header-info-links">
              <span className="strong">Venta: (601) 746 8001</span>
              <span className="strong">Servicio: (601) 407 3033</span>
              <a href="/sigue-tu-pedido">Sigue tu pedido</a>
              <a href="/tiendas">Nuestras tiendas</a>
              <a href="/catalogos">Catálogo</a>
              <a href="/ayuda">Ayuda</a>
            </div>
          </div>

          {/* ===== Fila inferior ===== */}
          <div className="header-bottom-row">
            <div className="header-right-group">
              {/* Buscador (tipografía más grande) */}
              <div className="header-search">
                <SearchBar />
              </div>

              {/* Cuenta + Carrito (tipografía más grande) */}
              <div className="header-actions">
                <button
                  type="button"
                  className="account-link"
                  onClick={() => setIsLoginOpen(true)}
                  aria-label="Abrir Mi cuenta"
                >
                  <i className="alk-icon-user" aria-hidden="true"></i>
                  <span>Mi cuenta</span>
                </button>

                <div className="cart-link">
                  <CartWidget />
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      {/* 🔸 Categorías (sin cambios) */}
      <div className="header-categories">
        <div className="container header-categories-container">
          <a href="/category/celulares">Celulares</a>
          <a href="/category/computadores">Computadores</a>
          <a href="/category/electrodomesticos">Electrodomésticos</a>
          <a href="/category/tv">TV</a>
          <a href="/category/accesorios">Accesorios</a>
          <a href="/category/videojuegos">Videojuegos</a>
          <a href="/category/audio">Audio</a>
          <a href="/category/camaras">Cámaras</a>
          <a href="/category/pines">Pines</a>
          <a href="/category/hogar">Hogar</a>
          <a href="/category/casa-inteligente">Casa Inteligente</a>
          <a href="/category/deportes">Deportes</a>
          <a href="/category/llantas-motos">Llantas y Motos</a>
          <a href="/category/juguetes">Juguetes</a>
          <a href="/category/otros">Otros</a>
        </div>
      </div>

      {/* 🔹 Modal de login */}
      {isLoginOpen && <LoginModal onClose={() => setIsLoginOpen(false)} />}
    </header>
  );
};

export default Header;
