import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'

export default function NoteList() {
  const [notes, setNotes] = useState([])

  useEffect(() => {
    fetch('/api/notes')
      .then(r => r.json())
      .then(data => setNotes(Array.isArray(data) ? data : []))
  }, [])

  return (
    <div>
      <div className="list-header">
        <h2>My Notes</h2>
        <Link to="/new" className="btn">+ New Note</Link>
      </div>
      <div className="note-grid">
        {notes.map(note => (
          <Link key={note.id} to={`/note/${note.id}`} className="note-card">
            <h3>{note.title}</h3>
            <time>{new Date(note.created_at).toLocaleDateString()}</time>
          </Link>
        ))}
      </div>
      {notes.length === 0 && <p className="empty">No notes yet. Create your first one!</p>}
    </div>
  )
}
