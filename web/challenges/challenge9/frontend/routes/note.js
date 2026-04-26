const express = require("express")
const router = express.Router()

router.use("*", (req, res, next) => {
  if (!req.session.username) {
    res.json({ status: "error", message: "you need auth first" })
    return
  }

  next()
})


router.get("/my", async (req, res) => {
  const r = await req.api.myNotes(req.session.username)
    .catch(err => ({ status: "error", message: "internal api error" }))

  res.json(r)
})


router.post("/create", async (req, res) => {
  const username = req.session.username
  const language = req.body.language
  const content = req.body.content
  const isPrivate = req.body.private

  const r = await req.api.createNote(username, language, content, isPrivate)
    .catch(err => ({ status: "success", message: "internal api error" }))
  res.json(r)
})


router.get("/get/:noteId", async(req, res) => {
  const username = req.session.username
  const noteId = req.params.noteId

  const r = await req.api.getNote(username, noteId)
    .catch(err => ({ status: "success", message: "internal api error" }))
  res.json(r)
})


router.get("/delete/:noteId", async(req, res) => {
  const username = req.session.username
  const noteId = req.params.noteId

  const r = await req.api.deleteNote(username, noteId)
    .catch(err => ({ status: "success", message: "internal api error" }))
  res.json(r)
})

module.exports = router
