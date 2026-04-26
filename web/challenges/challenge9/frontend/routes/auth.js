const express = require("express")

const router = express.Router()

router.post("/register", async (req, res) => {
  const username = req.body.username
  const password = req.body.password
  if (!username || !password) {
    res.json({ status: "error", message: "invalid username or password" })
    return
  }
  const r = await req.api.register(username, password)
    .catch(err => ({ status: "error", message: "api error" }))

  if (r.status == "error") {
    res.json(r)
    return
  }

  req.session.username = username
  res.json(r)
})

router.post("/login", async (req, res) => {
  const username = req.body.username
  const password = req.body.password
  if (!username || !password) {
    res.json({ status: "error", message: "invalid username or password" })
    return
  }
  const r = await req.api.login(username, password)
    .catch(err => ({ status: "error", message: "api error" }))

  if (r.status == "error") {
    res.json(r)
    return
  }

  req.session.username = username
  res.json(r)
})



module.exports = router
