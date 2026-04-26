import React, { useState, useRef, useEffect } from 'react';
import Prism from 'prismjs';
import 'prismjs/themes/prism.css';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-jsx';
import 'prismjs/components/prism-python';
import 'prismjs/components/prism-java';
import 'prismjs/components/prism-c';
import 'prismjs/components/prism-cpp';

function CodeEditor({ initialValue = '', language = 'javascript', onCodeChange }) {
  const [code, setCode] = useState(initialValue);
  const preRef = useRef(null);
  const textareaRef = useRef(null);

  useEffect(() => {
    Prism.highlightAll();
  }, [code]);

  useEffect(() => {
    if (preRef.current && textareaRef.current) {
      textareaRef.current.style.width = `${preRef.current.offsetWidth}px`;
      textareaRef.current.style.height = `${preRef.current.offsetHeight}px`;
    }
  }, [code]);

  const handleChange = (e) => {
    const newCode = e.target.value;
    setCode(newCode);
    if (onCodeChange) {
      onCodeChange(newCode);
    }
  };

  return (
    <div className="code-editor">
      <div className="editor-container" style={{ position: 'relative' }}>
        <textarea
          ref={textareaRef}
          value={code}
          onChange={handleChange}
          className="code-input"
          spellCheck="false"
          placeholder="Enter your code here..."
          style={{
            minHeight: '300px',
            padding: '1rem',
            fontFamily: 'monospace',
            fontSize: '14px',
            lineHeight: '1.5',
            border: '1px solid #ccc',
            borderRadius: '4px',
            backgroundColor: 'transparent',
            resize: 'vertical',
            position: 'absolute',
            top: 0,
            left: 0,
            zIndex: 1,
            color: 'transparent',
            caretColor: 'black',
            boxSizing: 'border-box',
          }}
        />
        <pre 
          ref={preRef}
          style={{
            minHeight: '300px',
            padding: '1rem',
            fontFamily: 'monospace',
            fontSize: '14px',
            lineHeight: '1.5',
            border: '1px solid #ccc',
            borderRadius: '4px',
            backgroundColor: '#f8f8f8',
            margin: 0,
            pointerEvents: 'none',
          }}
        >
          <code className={`language-${language}`}>
            {code || ' '}
          </code>
        </pre>
      </div>
    </div>
  );
}

export default CodeEditor;
