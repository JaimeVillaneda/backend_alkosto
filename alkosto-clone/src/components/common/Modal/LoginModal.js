import React, { useState } from 'react';
import './LoginModal.scss';

const LoginModal = ({ onClose }) => {
  const [email, setEmail] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    alert(`Iniciaste sesión con: ${email}`);
    onClose();
  };

  return (
    <div className="login-modal-overlay" onClick={onClose}>
      <div className="login-modal" onClick={(e) => e.stopPropagation()}>
        <div className="login-header">
          <h2>Ingresar o crear cuenta</h2>
          <p>
            Accede a tus datos personales, tus pedidos y solicita devoluciones:
          </p>
        </div>

        <form onSubmit={handleSubmit}>
          <input
            type="email"
            placeholder="Correo electrónico"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
          <button type="submit">Continuar</button>
        </form>

        <div className="login-extra">
          <div>
            <h4>📦 Sigue tu pedido</h4>
            <p>Revisa el estado actual de tu pedido.</p>
          </div>
          <div>
            <h4>📄 Descarga tu factura</h4>
            <p>Consulta y descarga tu factura fácilmente.</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoginModal;
