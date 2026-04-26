import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import PasteComponent from '../components/paste';
import Header from '../components/header';
import api from '../api';

function PastePage() {
  const { pasteId } = useParams();
  const [pasteData, setPasteData] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchPaste = async () => {
      if (pasteId) {
        try {
          const response = await api.getNote(pasteId);
          if (response.status === "success") {
            setPasteData(response.data);
            console.log(response.data);
          } else {
            setError(response.message);
          }
        } catch (err) {
          setError("Failed to load paste");
        }
      }
    };

    fetchPaste();
  }, [pasteId]);

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

  if (!pasteData) {
    return (
      <>
        <Header />
        <div style={{ textAlign: 'center', padding: '2rem' }}>
          <p>Loading...</p>
        </div>
      </>
    );
  }

  return (
    <>
      <Header />
      <div style={{ 
        display: 'flex',
        justifyContent: 'center',
        padding: '2rem'
      }}>
        <PasteComponent
          title={`${pasteData.owner}'s paste`}
          code={pasteData.content}
          language={pasteData.language}
          author={pasteData.username}
        />
      </div>
    </>
  );
}

export default PastePage;
