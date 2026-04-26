import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api';
import Header from '../components/header';

function LoginPage() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    try {
      const response = await api.login(username, password);
      if (response.status === 'error') {
        setError(response.message);
      } else {
        // Successful login - could add redirect logic here
        console.log('Login successful');
        window.location.href = '/';
      }
    } catch (err) {
      setError('Failed to login. Please try again.');
    }
  };

  return (
    <>
      <Header />
      <div className="login-container">
        <form onSubmit={handleSubmit} className="login-form">
          <h2>Login</h2>
          {error && <div className="error-message">{error}</div>}
          <div className="form-group">
            <label htmlFor="username">Username:</label>
            <input
              type="text"
              id="username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="password">Password:</label>
            <input
              type="password"
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>
          <button type="submit">Login</button>
          <p style={{ marginTop: '1rem', textAlign: 'center' }}>
            Don't have an account? <Link to="/register">Register here</Link>
          </p>
        </form>
      </div>
    </>
  );
}

export default LoginPage;
