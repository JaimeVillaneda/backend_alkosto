import React, {
  useEffect,
  useMemo,
  useRef,
  useState,
  useCallback,
} from "react";
import "./HeroCarousel.scss";

const DEFAULT_SLIDES = [
  { title: "Ofertas irresistibles", subtitle: "Tecnología al mejor precio", bg: "g1", cta: "Ver ofertas" },
  { title: "Electrohogar", subtitle: "Renueva tu casa hoy", bg: "g2", cta: "Descubrir" },
  { title: "Zona Gamer", subtitle: "Setup soñado", bg: "g3", cta: "Armar mi setup" },
];

export default function HeroCarousel({
  slides = DEFAULT_SLIDES,
  interval = 5000,
  bleed = false,
  flushLeft = false,
  ariaLabel = "Promociones destacadas",
}) {
  const [idx, setIdx] = useState(0);
  const timer = useRef(null);
  const count = slides.length;

  const go = useCallback((next) => {
    setIdx((p) => (p + next + count) % count);
  }, [count]);

  const goTo = useCallback(
    (i) => setIdx(((i % count) + count) % count),
    [count]
  );

  const stop = useCallback(() => {
    if (timer.current) {
      clearInterval(timer.current);
      timer.current = null;
    }
  }, []);

  const start = useCallback(() => {
    stop();
    if (interval > 0 && count > 1) {
      timer.current = setInterval(() => go(1), interval);
    }
  }, [interval, count, go, stop]);

  useEffect(() => {
    start();
    const onVis = () => (document.hidden ? stop() : start());
    document.addEventListener("visibilitychange", onVis);
    return () => {
      stop();
      document.removeEventListener("visibilitychange", onVis);
    };
  }, [start, stop]);

  useEffect(() => {
    const onKey = (e) => {
      if (e.key === "ArrowLeft") go(-1);
      if (e.key === "ArrowRight") go(1);
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [go]);

  const touchStartX = useRef(0);
  const touchDx = useRef(0);

  const onTouchStart = () => {
    touchDx.current = 0;
    touchStartX.current = 0;
    stop();
  };
  const onTouchMove = (e) => {
    if (!touchStartX.current) touchStartX.current = e.touches[0].clientX;
    touchDx.current = e.touches[0].clientX - touchStartX.current;
  };
  const onTouchEnd = () => {
    const dx = touchDx.current;
    const TH = 40;
    if (dx > TH) go(-1);
    else if (dx < -TH) go(1);
    start();
  };

  const current = useMemo(() => slides[idx], [slides, idx]);

  const rootClass = [
    "hero-carousel",
    bleed ? "hero-carousel--bleed" : "",
    flushLeft ? "hero-carousel--flush-left" : "",
  ].join(" ");

  return (
    <div
      className={rootClass}
      role="region"
      aria-roledescription="carousel"
      aria-label={ariaLabel}
      onMouseEnter={stop}
      onMouseLeave={start}
      onTouchStart={onTouchStart}
      onTouchMove={onTouchMove}
      onTouchEnd={onTouchEnd}
    >
      <div className="hero-carousel__inner">
        <div
          className={`hero-slide ${current.bg ? `bg-${current.bg}` : ""}`}
          style={current.img ? { backgroundImage: `url(${current.img})` } : undefined}
          aria-label={current.title || "Slide"}
        >
          <div className="hero-slide__content">
            {current.title && <h3 className="hero-slide__title">{current.title}</h3>}
            {current.subtitle && <p className="hero-slide__subtitle">{current.subtitle}</p>}
            {current.cta && (
              current.href ? (
                <a className="btn" href={current.href}>{current.cta}</a>
              ) : (
                <button className="btn" type="button">{current.cta}</button>
              )
            )}
          </div>
        </div>

        {count > 1 && (
          <>
            <button className="nav prev" onClick={() => go(-1)} aria-label="Anterior">‹</button>
            <button className="nav next" onClick={() => go(1)} aria-label="Siguiente">›</button>
          </>
        )}

        {count > 1 && (
          <div className="dots" role="tablist" aria-label="Paginación del carrusel">
            {slides.map((_, i) => (
              <button
                key={i}
                role="tab"
                aria-selected={i === idx}
                className={`dot ${i === idx ? "active" : ""}`}
                onClick={() => goTo(i)}
                aria-label={`Ir al slide ${i + 1}`}
              />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
