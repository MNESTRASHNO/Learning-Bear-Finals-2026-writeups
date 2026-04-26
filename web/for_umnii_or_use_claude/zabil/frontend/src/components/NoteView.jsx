import { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'

export default function NoteView() {
  const { id } = useParams()
  const [note, setNote] = useState(null)
  const [reported, setReported] = useState(false)
  const [error, setError] = useState('')

  useEffect(() => {
    fetch(`/api/notes/${id}`)
      .then(r => {
        if (!r.ok) throw new Error('Not found')
        return r.json()
      })
      .then(setNote)
      .catch(() => setError('Note not found'))
  }, [id])

  async function handleReport() {
    await fetch(`/api/report/${id}`, { method: 'POST' })
    setReported(true)
  }

  if (error) {
    return (
      <div>
        <Link to="/" className="back-link">&larr; Back to notes</Link>
        <p className="empty">{error}</p>
      </div>
    )
  }

  if (!note) return <p className="loading">Loading...</p>

  return (
    <div>
      <Link to="/" className="back-link">&larr; Back to notes</Link>
      <div className="note-header">
        <h2>{note.title}</h2>
        <button
          onClick={handleReport}
          disabled={reported}
          className="report-btn"
        >
          {reported ? 'Reported' : 'Report to Admin'}
        </button>
      </div>
      <div
        className="note-content"
        dangerouslySetInnerHTML={{ __html: note.content }}
      />
      <div className="note-share">
        Share link: {window.location.href}
      </div>
    </div>
  )
}
