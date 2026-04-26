import { useState, useEffect } from 'react'
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import LoginPage from './views/loginPage'
import PastePage from './views/pastePage'
import RegisterPage from './views/registerPage'
import CreatePastePage from './views/createPastePage'
import { checkAuth } from './helpers'
import MyPastePage from './views/myPastePage'
import UserPastePage from './views/userPastePage'

function App() {
  const isAuthenticated = checkAuth()

  return (
    <Router>
      <Routes>
        {/* <Route path="/" element={isAuthenticated ? <PastePage /> : <Navigate to="/login" />} /> */}
        <Route path="/paste/:pasteId" element={isAuthenticated ? <PastePage /> : <Navigate to="/login" />} />
        <Route path="/new" element={isAuthenticated ? <CreatePastePage /> : <Navigate to="/login" />} />
        <Route path="/login" element={!isAuthenticated ? <LoginPage /> : <Navigate to="/" />} />
        <Route path="/register" element={!isAuthenticated ? <RegisterPage /> : <Navigate to="/" />} />
        <Route path="/" element={isAuthenticated ? <MyPastePage /> : <Navigate to="/login" />} />
        <Route path="/user/:username" element={isAuthenticated ? <UserPastePage /> : <Navigate to="/login" />} />
      </Routes>
    </Router>
  )
}

export default App
