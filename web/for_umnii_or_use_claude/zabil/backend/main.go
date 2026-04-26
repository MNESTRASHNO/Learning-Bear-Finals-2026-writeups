package main

import (
	"log"
	"net/http"

	"notekeeper/handlers"
	"notekeeper/store"
)

func main() {
	s := store.New()
	h := handlers.New(s)

	mux := http.NewServeMux()
	mux.HandleFunc("POST /api/notes", h.CreateNote)
	mux.HandleFunc("GET /api/notes", h.ListNotes)
	mux.HandleFunc("GET /api/notes/{id}", h.GetNote)
	mux.HandleFunc("POST /api/report/{id}", h.ReportNote)

	log.Println("NoteKeeper server listening on :8080")
	log.Fatal(http.ListenAndServe(":8080", mux))
}
