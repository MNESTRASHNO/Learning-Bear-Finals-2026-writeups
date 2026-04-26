import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import CodeEditor from '../components/codeEditor';
import Header from '../components/header';
import api from '../api';

function CreatePastePage() {
  const [code, setCode] = useState('');
  const [language, setLanguage] = useState('javascript');
  const [isPrivate, setIsPrivate] = useState(false);
  const [error, setError] = useState(null);
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      const response = await api.createNote(language, code, isPrivate);
      if (response.status === "success") {
        navigate(`/paste/${response.data.id}`);
      } else {
        setError(response.message);
      }
    } catch (err) {
      setError("Failed to create paste");
    }
  };

  const languages = [
    { value: 'javascript', label: 'JavaScript' },
    { value: 'python', label: 'Python' },
    { value: 'java', label: 'Java' },
    { value: 'c', label: 'C' },
    { value: 'cpp', label: 'C++' },
    { value: 'jsx', label: 'JSX' }
  ];

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
        <h2>Create New Paste</h2>
        
        <form onSubmit={handleSubmit}>
          <div style={{ 
            border: '1px solid #ccc',
            borderRadius: '4px',
            padding: '1rem',
            backgroundColor: '#f9f9f9',
            marginBottom: '1rem'
          }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <div>
                <label htmlFor="language" style={{ marginRight: '1rem' }}>
                  Language:
                </label>
                <select
                  id="language"
                  value={language}
                  onChange={(e) => setLanguage(e.target.value)}
                  style={{
                    padding: '0.5rem',
                    borderRadius: '4px',
                    border: '1px solid #ccc'
                  }}
                >
                  {languages.map(lang => (
                    <option key={lang.value} value={lang.value}>
                      {lang.label}
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label htmlFor="private" style={{ marginRight: '1rem' }}>
                  Private:
                </label>
                <input
                  type="checkbox"
                  id="private"
                  checked={isPrivate}
                  onChange={(e) => setIsPrivate(e.target.checked)}
                  style={{ cursor: 'pointer' }}
                />
              </div>
            </div>
          </div>

          <div style={{ 
            border: '1px solid #ccc',
            borderRadius: '4px',
            backgroundColor: '#f9f9f9',
            marginBottom: '1rem',
          }}>
            <CodeEditor
              initialValue={code}
              language={language}
              onCodeChange={setCode}
            />
          </div>
          
          <button 
            type="submit"
            style={{
              backgroundColor: '#333',
              color: 'white',
              padding: '0.5rem 1rem',
              border: 'none',
              borderRadius: '4px',
              cursor: 'pointer'
            }}
          >
            Create Paste
          </button>
        </form>
      </div>
    </>
  );
}

export default CreatePastePage;
