import React from 'react';
import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { vscDarkPlus } from 'react-syntax-highlighter/dist/esm/styles/prism';

function PasteComponent({ code, language, title, author }) {
  return (
    <div style={{ 
      border: '1px solid #ccc',
      borderRadius: '4px',
      backgroundColor: '#f9f9f9',
      maxWidth: '800px',
      width: '100%'
    }}>
      <div style={{
        padding: '1rem 1rem .5rem 1rem',
        borderBottom: '1px solid #ccc'
      }}>
        <div style={{ 
          display: 'flex', 
          justifyContent: 'space-between', 
          alignItems: 'center' 
        }}>
          {title && <h3 style={{ margin: 0 }}>{title}</h3>}
          <span>{language}</span>
        </div>
        <div style={{ 
          marginTop: '0.5rem',
          fontSize: '0.9rem',
          color: '#666'
        }}>
          {author && (
            <span>
              Posted by <strong>{author}</strong>
            </span>
          )}
        </div>
      </div>
      <div>
        <SyntaxHighlighter 
          language={language}
          style={vscDarkPlus}
          showLineNumbers={true}
          wrapLines={true}
          customStyle={{
            margin: 0,
            borderRadius: '0 0 4px 4px',
          }}
        >
          {code}
        </SyntaxHighlighter>
      </div>
    </div>
  );
}

// Add prop types validation
PasteComponent.defaultProps = {
  title: '',
  code: '',
  language: 'javascript', // Default language
  author: ''
};

export default PasteComponent;
