package store

import (
	"crypto/rand"
	"encoding/hex"
	"sort"
	"sync"
	"time"
)

type Note struct {
	ID        string    `json:"id"`
	Title     string    `json:"title"`
	Content   string    `json:"content"`
	CreatedAt time.Time `json:"created_at"`
}

type Store struct {
	mu    sync.RWMutex
	notes map[string]*Note
}

func New() *Store {
	return &Store{notes: make(map[string]*Note)}
}

func (s *Store) Create(title, content string) *Note {
	b := make([]byte, 8)
	rand.Read(b)
	id := hex.EncodeToString(b)

	note := &Note{
		ID:        id,
		Title:     title,
		Content:   content,
		CreatedAt: time.Now(),
	}

	s.mu.Lock()
	s.notes[id] = note
	s.mu.Unlock()

	return note
}

func (s *Store) Get(id string) *Note {
	s.mu.RLock()
	defer s.mu.RUnlock()
	return s.notes[id]
}

func (s *Store) List() []*Note {
	s.mu.RLock()
	defer s.mu.RUnlock()

	notes := make([]*Note, 0, len(s.notes))
	for _, n := range s.notes {
		notes = append(notes, n)
	}
	sort.Slice(notes, func(i, j int) bool {
		return notes[i].CreatedAt.After(notes[j].CreatedAt)
	})
	return notes
}
