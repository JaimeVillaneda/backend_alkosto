// src/components/MiniCategories/MiniCategories.js
import React from "react";
import { Link } from "react-router-dom";

// Imágenes
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

// slug para URLs
const slugify = (s) =>
  s
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");

// catálogo por defecto
const categories = [
  { name: "Celulares", img: celulares },
  { name: "Electro", img: electro },
  { name: "Computo e impresión", img: computo },
  { name: "Televisores", img: tv },
  { name: "Videojuegos", img: videojuegos },
  { name: "Zona Gamer", img: gamer },
  { name: "Domótica", img: domotica },
  { name: "Pines", img: pines },
  { name: "Audio", img: audio },
  { name: "Electrohogar", img: electrohogar },
  { name: "Smartwatch", img: smartwatch },
  { name: "Audífonos", img: audifonos },
  { name: "Accesorios", img: accesorios },
  { name: "Cámaras", img: camaras },
  { name: "Juguetes", img: juguetes },
  { name: "Deportes", img: deportes },
  { name: "Muebles", img: muebles },
  { name: "Colchones", img: colchones },
  { name: "Llantas", img: llantas },
  { name: "Hogar", img: hogar },
  { name: "Ropa hogar", img: ropaHogar },
  { name: "Proyectores", img: proyectores },
  { name: "Ofertas con bancos", img: ofertas },
];

export default function MiniCategories({
  items = categories,
  variant = "grid",   // "grid" | "sidebar"
  limit = null,       // null = todas
  columns = 6,        // columnas en variante sidebar
}) {
  const list = typeof limit === "number" ? items.slice(0, limit) : items;

  const className =
    variant === "sidebar"
      ? "mini-categories mini-categories--sidebar"
      : "mini-categories mini-categories--grid";

  // Propiedad CSS personalizada para el nº de columnas (solo en sidebar)
  const style = variant === "sidebar" ? { "--mini-cols": columns } : undefined;

  return (
    <div className={className} style={style} aria-label="Listado de categorías">
      {list.map((it) => (
        // agrega `link-reset` para forzar que nunca se subraye
        <Link
          key={it.name}
          className="mini-cat link-reset"
          to={`/category/${slugify(it.name)}`}
          aria-label={it.name}
        >
          <img src={it.img} alt={it.name} loading="lazy" />
          <span>{it.name}</span>
        </Link>
      ))}
    </div>
  );
}
