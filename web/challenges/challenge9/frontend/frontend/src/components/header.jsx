import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { checkAuth, logout } from '../helpers';

function Header() {
  const isAuthenticated = checkAuth();
  const [username, setUsername] = useState('');
  const navigate = useNavigate();

  const handleSearch = (e) => {
    e.preventDefault();
    if (username.trim()) {
      navigate(`/user/${username}`);
      setUsername('');
    }
  };

  const handleLogout = () => {
    logout();
    window.location.href = '/login';
    setIsAuthenticated(false);
  };

  return (
    <header style={{
      backgroundColor: '#333',
      padding: '1rem',
      color: 'white',
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center'
    }}>
      <div className="logo">
        <Link to="/" style={{ textDecoration: 'none', color: 'white' }}>
          <h1 style={{ 
            margin: 0,
            fontSize: '1.5rem'
          }}>
            CodePaste
          </h1>
        </Link>
      </div>
      
      <nav style={{ display: 'flex', alignItems: 'center', gap: '1.5rem' }}>
        {isAuthenticated && (
          <form onSubmit={handleSearch} style={{ display: 'flex', gap: '0.5rem' }}>
            <input
              type="text"
              placeholder="Search by username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              style={{
                padding: '0.25rem 0.5rem',
                borderRadius: '2px',
                border: 'none'
              }}
            />
            <button 
              type="submit"
              style={{
                padding: '0.25rem 0.75rem',
                borderRadius: '2px',
                border: 'none`',
                backgroundColor: '#007bff',
                color: 'white',
                cursor: 'pointer',
                width: 'auto'
              }}
            >
              Search
            </button>
          </form>
        )}

        <ul style={{
          listStyle: 'none',
          margin: 0,
          padding: 0,
          display: 'flex',
          gap: '1.5rem'
        }}>
          <li>
            <Link to="/" style={{ color: 'white', textDecoration: 'none' }}>
              Home
            </Link>
          </li>
          {isAuthenticated ? (
            <>
              <li>
                <Link 
                  to="/new" 
                  style={{ 
                    color: 'white', 
                    textDecoration: 'none',
                    backgroundColor: '#007bff',
                    padding: '0.5rem 1rem',
                    borderRadius: '4px'
                  }}
                >
                  New Paste
                </Link>
              </li>
              <li>
                <button
                  onClick={handleLogout}
                  style={{
                    color: 'white',
                    textDecoration: 'none',
                    backgroundColor: 'transparent',
                    border: 'none',
                    cursor: 'pointer',
                    padding: 0,
                    fontSize: '1rem'
                  }}
                >
                  Logout
                </button>
              </li>
            </>
          ) : (
            <>
              <li>
                <Link to="/login" style={{ color: 'white', textDecoration: 'none' }}>
                  Login
                </Link>
              </li>
              <li>
                <Link to="/register" style={{ color: 'white', textDecoration: 'none' }}>
                  Register
                </Link>
              </li>
            </>
          )}
        </ul>
      </nav>
    </header>
  );
}

export default Header;
