const express = require("express")

const router = express.Router()

router.get("/", async (req, res) => {
  const r = await req.api.getUsernames()
    .catch(err => ({ status: "error", message: "api error" }))

  res.json(r)
})


router.get("/:username", async (req, res) => {
  const username = req.params.username
  console.log(username)
  const r = await req.api.getUserInfo(username)
    .catch(err => ({ status: "error", message: "api error" }))

  res.json(r)
})

module.exports = router
