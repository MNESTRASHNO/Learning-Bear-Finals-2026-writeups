import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import Modal from 'react-modal'
import api from '../api'
import { checkAuth } from '../helpers'

const RegisterPage = () => {
  const navigate = useNavigate()
  const [username, setUsername] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [isModalOpen, setIsModalOpen] = useState(false)

  useEffect(() => {
    if (checkAuth()) {
      navigate('/')
    }
  }, [navigate])

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    
    try {
      await api.register(username, password)
      navigate('/login')
    } catch (err) {
      setError(err.message)
      setIsModalOpen(true)
    }
  }

  return (
    <div className="container d-flex flex-column align-items-center justify-content-center min-vh-100">
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

      <div className="card shadow p-4" style={{ maxWidth: '400px', width: '100%' }}>
        <h2 className="text-center mb-4">
          Create your account
        </h2>
        <form onSubmit={handleSubmit}>
          <div className="mb-3">
            <label htmlFor="username" className="form-label">
              Username
            </label>
            <input
              id="username"
              name="username"
              type="text"
              required
              className="form-control"
              placeholder="Choose username"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
            />
          </div>
          <div className="mb-4">
            <label htmlFor="password" className="form-label">
              Password
            </label>
            <input
              id="password"
              name="password"
              type="password"
              required
              className="form-control"
              placeholder="Choose password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
          </div>

          <button
            type="submit"
            className="btn btn-primary w-100"
          >
            Sign up
          </button>
        </form>
      </div>
    </div>
  )
}

export default RegisterPage