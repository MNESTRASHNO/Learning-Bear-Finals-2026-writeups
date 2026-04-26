package handlers

import (
	"encoding/json"
	"html"
	"net/http"

	"notekeeper/sanitizer"
	"notekeeper/store"
)

type Handler struct {
	store *store.Store
}

func New(s *store.Store) *Handler {
	return &Handler{store: s}
}

type createRequest struct {
	Title   string `json:"title"`
	Content string `json:"content"`
}

type errorResponse struct {
	Error string `json:"error"`
}

func (h *Handler) CreateNote(w http.ResponseWriter, r *http.Request) {
	var req createRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		writeJSON(w, http.StatusBadRequest, errorResponse{Error: "invalid JSON body"})
		return
	}

	if req.Title == "" || req.Content == "" {
		writeJSON(w, http.StatusBadRequest, errorResponse{Error: "title and content are required"})
		return
	}

	if len(req.Title) > 200 {
		writeJSON(w, http.StatusBadRequest, errorResponse{Error: "title too long"})
		return
	}

	cleanContent, err := sanitizer.Sanitize(req.Content)
	if err != nil {
		writeJSON(w, http.StatusBadRequest, errorResponse{Error: "Content rejected: " + err.Error()})
		return
	}

	safeTitle := html.EscapeString(req.Title)

	note := h.store.Create(safeTitle, cleanContent)
	writeJSON(w, http.StatusCreated, note)
}

func (h *Handler) ListNotes(w http.ResponseWriter, r *http.Request) {
	notes := h.store.List()
	writeJSON(w, http.StatusOK, notes)
}

func (h *Handler) GetNote(w http.ResponseWriter, r *http.Request) {
	id := r.PathValue("id")
	note := h.store.Get(id)
	if note == nil {
		writeJSON(w, http.StatusNotFound, errorResponse{Error: "note not found"})
		return
	}
	writeJSON(w, http.StatusOK, note)
}

func (h *Handler) ReportNote(w http.ResponseWriter, r *http.Request) {
	id := r.PathValue("id")
	note := h.store.Get(id)
	if note == nil {
		writeJSON(w, http.StatusNotFound, errorResponse{Error: "note not found"})
		return
	}

	writeJSON(w, http.StatusOK, map[string]string{
		"status":  "reported",
		"message": "An admin will review this note shortly.",
	})
}

func writeJSON(w http.ResponseWriter, status int, v any) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	json.NewEncoder(w).Encode(v)
}
