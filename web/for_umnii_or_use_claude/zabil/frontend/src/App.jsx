import { Routes, Route, Link } from 'react-router-dom'
import NoteList from './components/NoteList'
import NoteEditor from './components/NoteEditor'
import NoteView from './components/NoteView'

export default function App() {
  return (
    <div className="app">
      <header>
        <div className="container header-inner">
          <Link to="/" className="logo">NoteKeeper</Link>
        </div>
      </header>
      <main className="container">
        <Routes>
          <Route path="/" element={<NoteList />} />
          <Route path="/new" element={<NoteEditor />} />
          <Route path="/note/:id" element={<NoteView />} />
        </Routes>
      </main>
    </div>
  )
}
