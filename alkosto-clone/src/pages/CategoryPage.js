import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { categories } from '../data/categories';
import { products } from '../data/products';
import ProductCard from '../components/Product/ProductCard';
import './CategoryPage.scss';

const CategoryPage = () => {
  const { categoryId } = useParams();
  const [category, setCategory] = useState(null);
  const [categoryProducts, setCategoryProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [sortBy, setSortBy] = useState('name');
  const [priceRange, setPriceRange] = useState([0, 10000000]);
  const [selectedBrands, setSelectedBrands] = useState([]);

  useEffect(() => {
    // Encontrar la categoría
    const foundCategory = categories.find(cat => cat.id === categoryId);
    setCategory(foundCategory);

    // Filtrar productos por categoría
    const catProducts = products.filter(product => 
      product.category === categoryId || 
      (foundCategory?.subcategories?.some(sub => sub.id === product.category))
    );
    
    setCategoryProducts(catProducts);
    setFilteredProducts(catProducts);
  }, [categoryId]);

  useEffect(() => {
    let filtered = [...categoryProducts];

    // Filtrar por marca
    if (selectedBrands.length > 0) {
      filtered = filtered.filter(product => 
        selectedBrands.includes(product.brand.toLowerCase())
      );
    }

    // Filtrar por precio
    filtered = filtered.filter(product => 
      product.price >= priceRange[0] && product.price <= priceRange[1]
    );

    // Ordenar
    filtered.sort((a, b) => {
      switch (sortBy) {
        case 'price-asc':
          return a.price - b.price;
        case 'price-desc':
          return b.price - a.price;
        case 'name':
          return a.name.localeCompare(b.name);
        case 'rating':
          return b.rating - a.rating;
        default:
          return 0;
      }
    });

    setFilteredProducts(filtered);
  }, [categoryProducts, sortBy, priceRange, selectedBrands]);

  const brands = [...new Set(categoryProducts.map(p => p.brand))];
  const maxPrice = Math.max(...categoryProducts.map(p => p.price), 0);

  const handleBrandFilter = (brand) => {
    const brandLower = brand.toLowerCase();
    setSelectedBrands(prev =>
      prev.includes(brandLower)
        ? prev.filter(b => b !== brandLower)
        : [...prev, brandLower]
    );
  };

  if (!category) {
    return (
      <div className="category-page">
        <div className="container">
          <div className="loading">Cargando categoría...</div>
        </div>
      </div>
    );
  }

  return (
    <div className="category-page">
      <div className="container">
        {/* Header de la categoría */}
        <div className="category-header">
          <div className="breadcrumb">
            <a href="/">Inicio</a> / <span>{category.name}</span>
          </div>
          <h1>{category.name}</h1>
          <p className="category-description">
            Descubre los mejores {category.name} al mejor precio
          </p>
        </div>

        <div className="category-content">
          {/* Filtros */}
          <aside className="filters-sidebar">
            <div className="filter-section">
              <h3>Filtrar por</h3>
              
              {/* Filtro de Marcas */}
              <div className="filter-group">
                <h4>Marca</h4>
                {brands.map(brand => (
                  <label key={brand} className="filter-checkbox">
                    <input
                      type="checkbox"
                      checked={selectedBrands.includes(brand.toLowerCase())}
                      onChange={() => handleBrandFilter(brand)}
                    />
                    <span className="checkmark"></span>
                    {brand}
                  </label>
                ))}
              </div>

              {/* Filtro de Precio */}
              <div className="filter-group">
                <h4>Precio</h4>
                <div className="price-range">
                  <input
                    type="range"
                    min="0"
                    max={maxPrice}
                    value={priceRange[1]}
                    onChange={(e) => setPriceRange([0, parseInt(e.target.value)])}
                    className="price-slider"
                  />
                  <div className="price-labels">
                    <span>$0</span>
                    <span>${priceRange[1].toLocaleString()}</span>
                  </div>
                </div>
              </div>

              {/* Filtro de Ofertas */}
              <div className="filter-group">
                <h4>Ofertas</h4>
                <label className="filter-checkbox">
                  <input
                    type="checkbox"
                    onChange={() => {
                      // Lógica para filtrar ofertas
                    }}
                  />
                  <span className="checkmark"></span>
                  Solo ofertas
                </label>
              </div>
            </div>
          </aside>

          {/* Lista de productos */}
          <main className="products-main">
            <div className="products-header">
              <div className="products-count">
                {filteredProducts.length} productos encontrados
              </div>
              <div className="sort-filter">
                <label>Ordenar por:</label>
                <select
                  value={sortBy}
                  onChange={(e) => setSortBy(e.target.value)}
                  className="sort-select"
                >
                  <option value="name">Nombre</option>
                  <option value="price-asc">Precio: Menor a Mayor</option>
                  <option value="price-desc">Precio: Mayor a Menor</option>
                  <option value="rating">Mejor Valorados</option>
                </select>
              </div>
            </div>

            <div className="products-grid">
              {filteredProducts.map(product => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>

            {filteredProducts.length === 0 && (
              <div className="no-products">
                <h3>No se encontraron productos</h3>
                <p>Intenta ajustar los filtros para ver más resultados</p>
              </div>
            )}
          </main>
        </div>
      </div>
    </div>
  );
};

export default CategoryPage;