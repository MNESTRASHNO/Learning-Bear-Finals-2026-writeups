import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import Header from '../components/header';
import PasteComponent from '../components/paste';
import api from '../api';

function UserPastePage() {
  const [pastes, setPastes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const { username } = useParams();

  useEffect(() => {
    const fetchPastes = async () => {
      try {
        const response = await api.getUserPastes(username);
        if (response.status === 'error') {
          setError(response.message);
        } else {
          setPastes(response.data.notes);
        }
      } catch (err) {
        setError('Failed to fetch pastes. Please try again later.');
      } finally {
        setLoading(false);
      }
    };

    fetchPastes();
  }, [username]);

  if (loading) {
    return (
      <>
        <Header />
        <div style={{ textAlign: 'center', padding: '2rem' }}>
          <p>Loading...</p>
        </div>
      </>
    );
  }

  if (error) {
    return (
      <>
        <Header />
        <div style={{ textAlign: 'center', padding: '2rem' }}>
          <p>Error: {error}</p>
        </div>
      </>
    );
  }

  return (
    <>
      <Header />
      <div style={{ maxWidth: '1200px', margin: '2rem auto', padding: '0 1rem' }}>
        <h2>{username}'s Pastes</h2>
        {pastes.length === 0 ? (
          <p>No pastes found for this user.</p>
        ) : (
          <div style={{ display: 'grid', gap: '1rem' }}>
            {pastes.map(paste => (
              <div 
                key={paste.id}
                style={{
                  border: '1px solid #ccc',
                  borderRadius: '4px',
                  padding: '1rem',
                  backgroundColor: '#f9f9f9'
                }}
              >
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                  <h3 style={{ margin: 0 }}>
                    <Link 
                      to={`/paste/${paste.id}`}
                      style={{ textDecoration: 'none', color: '#333' }}
                    >
                      Paste {paste.id}
                    </Link>
                  </h3>
                  <span></span>
                </div>
                <div style={{ marginTop: '0.5rem', fontSize: '0.9rem', color: '#666' }}>
                  {paste.private && <span>🔒 Private</span>}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </>
  );
}

export default UserPastePage;
