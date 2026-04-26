import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import Header from '../components/header';
import api from '../api';

function MyPastePage() {
  const [pastes, setPastes] = useState([]);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchPastes = async () => {
      try {
        const response = await api.getMyNotes();
        console.log(response);
        if (response.status === "success") {
          setPastes(response.data);
        } else {
          setError(response.message);
        }
      } catch (err) {
        console.log(err);
        setError("Failed to load pastes");
      } finally {
        setLoading(false);
      }
    };

    fetchPastes();
  }, []);

  const handleDelete = async (id) => {
    try {
      const response = await api.deleteNote(id);
      if (response.status === "success") {
        setPastes(pastes.filter(paste => paste.id !== id));
      } else {
        setError(response.message);
      }
    } catch (err) {
      setError("Failed to delete paste");
    }
  };

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
        <h2>My Pastes</h2>
        {pastes.length === 0 ? (
          <p>You haven't created any pastes yet. <Link to="/new">Create one now!</Link></p>
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
                  <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                    <span>Language: {paste.language}</span>
                    <button
                      onClick={() => handleDelete(paste.id)}
                      style={{
                        width: '32px',
                        height: '32px',
                        padding: '4px',
                        border: '1px solid #dc3545',
                        borderRadius: '4px',
                        backgroundColor: '#dc3545',
                        color: 'white',
                        cursor: 'pointer',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                      }}
                      title="Delete paste"
                    >
                      <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fillRule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118z"/>
                      </svg>
                    </button>
                  </div>
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

export default MyPastePage;
