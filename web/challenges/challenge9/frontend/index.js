const express = require("express")
const session = require('express-session')
const bodyParser = require('body-parser') 
const path = require('path')
const helpers = require("./helpers")

const API = require("./api")

const authRoutes = require("./routes/auth") 
const noteRoutes = require("./routes/note")
const userRoutes = require("./routes/user")

const TOKEN = process.env.TOKEN || "TOKEN"
const BACKEND_URL = process.env.BACKEND_URL || "http://127.0.0.1:5001"
const FLAG = process.env.FLAG || "flag{redacted}"

const api = new API(BACKEND_URL, TOKEN)
api.register("admin", helpers.randStr(32))
api.createNote("admin", "plain", FLAG, true)

const app = express()
app.set('trust proxy', 1)

app.use(bodyParser.urlencoded({ extended: false }))
app.use(session({
  secret: helpers.randStr(10),
  saveUninitialized: false,
  cookie: { httpOnly: false }
}))

app.use("*", (req, res, next) => {
  req.api = api
  next()
})

app.use(express.static(path.join(__dirname, 'frontend/dist')))
app.use("/api/auth", authRoutes)
app.use("/api/note", noteRoutes)
app.use("/api/user", userRoutes)

app.get("*", (req, res) => {
  res.sendFile(path.join(__dirname, 'frontend/dist', 'index.html'))
})

app.listen(5002)
