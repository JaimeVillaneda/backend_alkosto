// Mensaje de carga
console.log("Frontend Alkosto Clon cargado correctamente ✅");

// Inicializar Swiper para el banner
const bannerSwiper = new Swiper(".banner-swiper", {
  loop: true,
  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
  },
  pagination: {
    el: ".banner .swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".banner .swiper-button-next",
    prevEl: ".banner .swiper-button-prev",
  },
});

// Inicializar Swiper para productos
const productosSwiper = new Swiper(".productos-swiper", {
  slidesPerView: 3,
  spaceBetween: 20,
  loop: true,
  navigation: {
    nextEl: ".productos .swiper-button-next",
    prevEl: ".productos .swiper-button-prev",
  },
  pagination: {
    el: ".productos .swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    320: { slidesPerView: 1 },
    768: { slidesPerView: 2 },
    1024: { slidesPerView: 3 },
  },
});

// Buscador
document.querySelector(".search-bar button").addEventListener("click", () => {
  let query = document.querySelector(".search-bar input").value;
  if (query) {
    alert("Buscando: " + query);
  } else {
    alert("Por favor ingresa un término de búsqueda");
  }
});

// Botón agregar al carrito
document.querySelectorAll(".btn-comprar").forEach(boton => {
  boton.addEventListener("click", () => {
    alert("Producto agregado al carrito 🛒");
  });
});
