// src/components/HomeBanner/HomeBanner.js
import React from "react";
import "./HomeBanner.scss";

const categories = [
  { name: "Celulares", image: "/images/categoria/celulares.webp", link: "/category/celulares" },
  { name: "Electro", image: "/images/categoria/electro.webp", link: "/category/electro" },
  { name: "Computo e impresión", image: "/images/categoria/computo.webp", link: "/category/computo" },
  { name: "Televisores", image: "/images/categoria/tv.webp", link: "/category/tv" },
  { name: "Videojuegos", image: "/images/categoria/videojuegos.webp", link: "/category/videojuegos" },
  { name: "Zona Gamer", image: "/images/categoria/gamer.webp", link: "/category/gamer" },
  { name: "Domótica", image: "/images/categoria/domotica.webp", link: "/category/domotica" },
  { name: "Pines", image: "/images/categoria/pines.webp", link: "/category/pines" },
  { name: "Audio", image: "/images/categoria/audio.webp", link: "/category/audio" },
  { name: "Electrohogar", image: "/images/categoria/electrohogar.webp", link: "/category/electrohogar" },
  { name: "Smartwatch", image: "/images/categoria/smartwatch.webp", link: "/category/smartwatch" },
  { name: "Audífonos", image: "/images/categoria/audifonos.webp", link: "/category/audifonos" },
  { name: "Accesorios", image: "/images/categoria/accesorios.webp", link: "/category/accesorios" },
  { name: "Cámaras", image: "/images/categoria/camaras.webp", link: "/category/camaras" },
  { name: "Juguetes", image: "/images/categoria/juguetes.webp", link: "/category/juguetes" },
  { name: "Deportes", image: "/images/categoria/deportes.webp", link: "/category/deportes" },
  { name: "Muebles", image: "/images/categoria/muebles.webp", link: "/category/muebles" },
  { name: "Colchones", image: "/images/categoria/colchones.webp", link: "/category/colchones" },
  { name: "Llantas", image: "/images/categoria/llantas.webp", link: "/category/llantas" },
  { name: "Hogar", image: "/images/categoria/hogar.webp", link: "/category/hogar" },
  { name: "Ropa hogar", image: "/images/categoria/ropa-hogar.webp", link: "/category/ropa-hogar" },
  { name: "Proyectores", image: "/images/categoria/proyectores.webp", link: "/category/proyectores" },
  { name: "Ofertas con bancos", image: "/images/categoria/ofertas.webp", link: "/category/ofertas" },
  { name: "Hiperofertas", image: "/images/categoria/Hiperofertas.webp", link: "/category/ofertas" },
];

const HomeBanner = () => {
  return (
    <section className="home-banner">
      <div className="hero-content">
        <h1>La Ñapa es lo mejor, ven y disfrútala</h1>
      </div>

      <div className="categories-grid">
        {categories.map((cat, index) => (
          <a key={index} href={cat.link} className="category-card">
            <img src={cat.image} alt={cat.name} />
            <span>{cat.name}</span>
          </a>
        ))}
      </div>
    </section>
  );
};

export default HomeBanner;
