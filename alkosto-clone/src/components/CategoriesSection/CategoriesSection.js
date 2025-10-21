import React from "react";
import "./CategoriesSection.scss";

// Importamos todas las imágenes de categorías
import celulares from "../../assets/images/categorias/celulares.webp";
import electro from "../../assets/images/categorias/electro.webp";
import computo from "../../assets/images/categorias/computo.webp";
import tv from "../../assets/images/categorias/tv.webp";
import videojuegos from "../../assets/images/categorias/videojuegos.webp";
import gamer from "../../assets/images/categorias/gamer.webp";
import domotica from "../../assets/images/categorias/domotica.webp";
import pines from "../../assets/images/categorias/pines.webp";
import audio from "../../assets/images/categorias/audio.webp";
import electrohogar from "../../assets/images/categorias/electrohogar.webp";
import smartwatch from "../../assets/images/categorias/smartwatch.webp";
import audifonos from "../../assets/images/categorias/audifonos.webp";
import accesorios from "../../assets/images/categorias/accesorios.webp";
import camaras from "../../assets/images/categorias/camaras.webp";
import juguetes from "../../assets/images/categorias/juguetes.webp";
import deportes from "../../assets/images/categorias/deportes.webp";
import muebles from "../../assets/images/categorias/muebles.webp";
import colchones from "../../assets/images/categorias/colchones.webp";
import llantas from "../../assets/images/categorias/llantas.webp";
import hogar from "../../assets/images/categorias/hogar.webp";
import ropaHogar from "../../assets/images/categorias/ropa-hogar.webp";
import proyectores from "../../assets/images/categorias/proyectores.webp";
import ofertas from "../../assets/images/categorias/ofertas.webp";

const categories = [
  { name: "Celulares", image: celulares },
  { name: "Electro", image: electro },
  { name: "Computo e impresión", image: computo },
  { name: "Televisores", image: tv },
  { name: "Videojuegos", image: videojuegos },
  { name: "Zona Gamer", image: gamer },
  { name: "Domótica", image: domotica },
  { name: "Pines", image: pines },
  { name: "Audio", image: audio },
  { name: "Electrohogar", image: electrohogar },
  { name: "Smartwatch", image: smartwatch },
  { name: "Audífonos", image: audifonos },
  { name: "Accesorios", image: accesorios },
  { name: "Cámaras", image: camaras },
  { name: "Juguetes", image: juguetes },
  { name: "Deportes", image: deportes },
  { name: "Muebles", image: muebles },
  { name: "Colchones", image: colchones },
  { name: "Llantas", image: llantas },
  { name: "Hogar", image: hogar },
  { name: "Ropa hogar", image: ropaHogar },
  { name: "Proyectores", image: proyectores },
  { name: "Ofertas con bancos", image: ofertas },
];

const CategoriesSection = () => {
  return (
    <section className="categories-section">
      <div className="categories-container">
        {/* 🔹 Título */}
        <div className="categories-header">
          <h2>La Ñapa es lo mejor, ven y disfrútala</h2>
        </div>

        {/* 🔹 Grid de categorías */}
        <div className="categories-grid">
          {categories.map((cat, index) => (
            <div key={index} className="category-card">
              <img src={cat.image} alt={cat.name} />
              <span>{cat.name}</span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default CategoriesSection;
