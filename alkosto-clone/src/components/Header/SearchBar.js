import React, { useState } from 'react';
import { products } from '../../data/products';
import './SearchBar.scss';

const SearchBar = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [suggestions, setSuggestions] = useState([]);
  const [isFocused, setIsFocused] = useState(false);

  const handleSearch = (term) => {
    setSearchTerm(term);
    if (term.length > 2) {
      const filtered = products.filter(product =>
        product.name.toLowerCase().includes(term.toLowerCase()) ||
        product.brand.toLowerCase().includes(term.toLowerCase())
      );
      setSuggestions(filtered.slice(0, 5));
    } else {
      setSuggestions([]);
    }
  };

  const handleSuggestionClick = (product) => {
    setSearchTerm(product.name);
    setSuggestions([]);
    window.location.href = `/product/${product.id}`;
  };

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    if (searchTerm.trim()) {
      window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
    }
  };

  return (
    <form className="search-bar" onSubmit={handleSearchSubmit}>
      <div className="search-container">
        <input 
          type="text" 
          placeholder="¿Qué buscas hoy?" 
          className="search-input"
          value={searchTerm}
          onChange={(e) => handleSearch(e.target.value)}
          onFocus={() => setIsFocused(true)}
          onBlur={() => setTimeout(() => setIsFocused(false), 200)}
        />
        <button type="submit" className="search-button">
          <span className="search-icon">🔍</span>
        </button>
      </div>

      {suggestions.length > 0 && isFocused && (
        <div className="search-suggestions">
          {suggestions.map(product => (
            <div
              key={product.id}
              className="suggestion-item"
              onMouseDown={() => handleSuggestionClick(product)}
            >
              <img 
                src={product.image} 
                alt={product.name} 
                className="suggestion-image"
              />
              <div className="suggestion-info">
                <div className="suggestion-name">{product.name}</div>
                <div className="suggestion-brand">{product.brand}</div>
                <div className="suggestion-price">${product.price.toLocaleString()}</div>
              </div>
            </div>
          ))}
        </div>
      )}
    </form>
  );
};

export default SearchBar;