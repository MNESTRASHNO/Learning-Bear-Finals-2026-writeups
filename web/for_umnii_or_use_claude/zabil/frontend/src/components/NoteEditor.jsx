import { useState } from 'react'
import { useNavigate } from 'react-router-dom'

export default function NoteEditor() {
  const [title, setTitle] = useState('')
  const [content, setContent] = useState('')
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)
  const navigate = useNavigate()

  async function handleSubmit(e) {
    e.preventDefault()
    setError('')
    setSubmitting(true)

    try {
      const res = await fetch('/api/notes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title, content }),
      })

      const data = await res.json()

      if (!res.ok) {
        setError(data.error || 'Failed to create note')
        return
      }

      navigate(`/note/${data.id}`)
    } catch {
      setError('Network error')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <h2>New Note</h2>
      {error && <div className="error">{error}</div>}
      <input
        type="text"
        placeholder="Note title"
        value={title}
        onChange={e => setTitle(e.target.value)}
        required
      />
      <textarea
        placeholder="Write your note content here..."
        value={content}
        onChange={e => setContent(e.target.value)}
        required
      />
      <p className="hint">HTML formatting is supported.</p>
      <button type="submit" className="btn" disabled={submitting}>
        {submitting ? 'Saving...' : 'Save Note'}
      </button>
    </form>
  )
}
