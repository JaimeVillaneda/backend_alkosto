// src/pages/HomePage.js
import React from "react";
import HeroCarousel from "../components/HeroCarousel/HeroCarousel";
import MiniCategories from "../components/MiniCategories/MiniCategories";
import "./HomePage.scss";

export default function HomePage() {
  return (
    <main className="home">
      {/* ===== HERO: carrusel (izquierda) + mini-categorías (derecha) ===== */}
      <section className="home-hero" aria-label="Promociones y accesos rápidos">
        <div className="home-hero__container">
          {/* Carrusel a la izquierda */}
          <div className="home-hero__left">
            {/* altura controlada por CSS; bleed={false} para no desbordar */}
            <HeroCarousel bleed={false} />
          </div>

          {/* Mini-categorías a la derecha en 6 columnas */}
          <aside className="home-hero__right" aria-label="Mini categorías">
            <MiniCategories variant="sidebar" columns={6} limit={null} />
          </aside>
        </div>
      </section>

      {/* (Opcional) Otras secciones debajo
      <section className="featured-products">…</section>
      <section className="categories-section">…</section>
      */}
    </main>
  );
}
