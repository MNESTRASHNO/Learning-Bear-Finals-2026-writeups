import { BrowserRouter as Router, Routes, Route } from 'react-router-dom'
import LoginPage from './views/loginPage'
import RegisterPage from './views/registerPage'
import MyNotesPage from './views/myNotesPage'
import CreateNotePage from './views/createNotePage'
import Header from './components/header'
import { checkAuth } from './helpers'
import './App.css'

function App() {
  const authorized = checkAuth()
  return (
    <Router>
      <div className="min-vh-100 bg-light">
        <Header />

        <main>
          <Routes>
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />
            {authorized ? (
              <>
                <Route path="/" element={<MyNotesPage />} />
                <Route path="/notes/create" element={<CreateNotePage />} />
              </>
            ) : (
              <Route path="/" element={
                <div className="container d-flex flex-column align-items-center justify-content-center min-vh-100">
                  <h1 className="display-4 fw-bold">Welcome</h1>
                  <p className="mt-4 text-muted">Please login or register to continue</p>
                </div>
              } />
            )}
          </Routes>
        </main>
      </div>
    </Router>
  )
}

export default App
