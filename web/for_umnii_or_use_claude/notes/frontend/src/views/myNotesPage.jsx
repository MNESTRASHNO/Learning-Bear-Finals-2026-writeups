import { useState, useEffect } from 'react'
import { useSearchParams } from 'react-router-dom'
import { useNavigate } from 'react-router-dom'
import Modal from 'react-modal'
import api from '../api'

const MyNotesPage = () => {
  const [notes, setNotes] = useState([])
  const [error, setError] = useState('')
  const [isModalOpen, setIsModalOpen] = useState(false)
  const navigate = useNavigate()
  const [searchParams, setSearchParams] = useSearchParams()
  const searchQuery = searchParams.get('q') || ''

  useEffect(() => {
    const fetchNotes = async () => {
      try {
        let notesData
        if (searchQuery == "") {
            notesData = await api.getAllNotes()
        } else {
            notesData = await api.searchNotes(searchQuery)
        }
        setNotes(notesData.data)
      } catch (err) {
        setError(err.message)
        setIsModalOpen(true)
      }
    }
    fetchNotes()
  }, [searchQuery])

  const handleAddNote = async () => {
    navigate('/notes/create')
  }

  return (
    <div className="container py-5">
      <Modal
        isOpen={isModalOpen}
        onRequestClose={() => setIsModalOpen(false)}
        className="modal-dialog modal-dialog-centered"
        overlayClassName="modal-backdrop"
        ariaHideApp={false}
        style={{
          overlay: {
            backgroundColor: 'white'
          },
          content: {
            padding: '20px'
          }
        }}
      >
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title">Error</h5>
            <button 
              type="button" 
              className="btn-close" 
              onClick={() => setIsModalOpen(false)}
            />
          </div>
          <div className="modal-body">
            {error}
          </div>
          <div className="modal-footer">
            <button 
              type="button" 
              className="btn btn-secondary" 
              onClick={() => setIsModalOpen(false)}
            >
              Close
            </button>
          </div>
        </div>
      </Modal>

      <div className="row mb-4">
        <div className="col">
          <h1 className="mb-4">My Notes</h1>
          <div className="d-flex gap-2 mb-4">
            <input
              type="search"
              className="form-control"
              placeholder="Search notes..."
              value={searchQuery}
              onChange={(e) => setSearchParams({ q: e.target.value })}
            />
            <button 
              className="btn btn-primary"
              onClick={handleAddNote}
            >
              Add New Note
            </button>
          </div>
        </div>
      </div>

      <div className="row g-4">
        {notes.map(note => (
          <div key={note.id} className="col-md-6 col-lg-4">
            <div className="card h-100 shadow-sm">
              <div className="card-body">
                <h5 className="card-title">{note.title}</h5>
                <p className="card-text">{note.content}</p>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}

export default MyNotesPage
