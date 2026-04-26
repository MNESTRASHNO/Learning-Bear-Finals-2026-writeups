const express = require("express");
const session = require("express-session");
const bcrypt = require("bcryptjs");
const { Pool } = require("pg");
const path = require("path");

const app = express();
const TOKEN = process.env.TOKEN;
const INTERNAL_URL = process.env.INTERNAL_URL;

const pool = new Pool({
  host: process.env.DB_HOST,
  port: 5432,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME,
});

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(
  session({
    secret: process.env.SESSION_SECRET,
    resave: false,
    saveUninitialized: false,
    cookie: { maxAge: 3600000 },
  })
);
app.use(express.static(path.join(__dirname, "public")));

function auth(req, res, next) {
  if (!req.session.user) return res.status(401).json({ error: "unauthorized" });
  next();
}

app.get("/", (req, res) => {
  res.redirect("/dashboard.html");
});

app.post("/api/register", async (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) {
    return res.status(400).json({ error: "username and password are required" });
  }
  try {
    const hash = await bcrypt.hash(password, 10);
    await pool.query(
      "INSERT INTO users (username, password) VALUES ($1, $2)",
      [username, hash]
    );
    res.json({ ok: true });
  } catch (err) {
    if (err.code === "23505") {
      return res.status(409).json({ error: "username already taken" });
    }
    res.status(500).json({ error: "registration failed" });
  }
});

app.post("/api/login", async (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) {
    return res.status(400).json({ error: "username and password are required" });
  }
  try {
    const result = await pool.query(
      "SELECT * FROM users WHERE username = $1",
      [username]
    );
    if (result.rows.length === 0) {
      return res.status(401).json({ error: "invalid credentials" });
    }
    const user = result.rows[0];
    const valid = await bcrypt.compare(password, user.password);
    if (!valid) {
      return res.status(401).json({ error: "invalid credentials" });
    }
    req.session.user = { id: user.id, username: user.username };
    res.json({ ok: true, username: user.username });
  } catch {
    res.status(500).json({ error: "login failed" });
  }
});

app.post("/api/logout", (req, res) => {
  req.session.destroy();
  res.json({ ok: true });
});

app.get("/api/me", auth, (req, res) => {
  res.json(req.session.user);
});

app.get("/api/profile", auth, async (req, res) => {
  const { username } = req.query;
  if (!username) {
    return res.status(400).json({ error: "username is required" });
  }
  try {
    const response = await fetch(`${INTERNAL_URL}/profile`, {
      headers: {
        Cookie: `token=${TOKEN}; username=${username}`,
      },
    });
    const data = await response.json();
    res.json(data);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

app.get("/api/fetch", auth, async (req, res) => {
  const { url } = req.query;
  if (!url) {
    return res.status(400).json({ error: "url is required" });
  }
  try {
    const response = await fetch(url);
    const data = await response.text();
    res.send(data);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

async function start() {
  for (let i = 0; i < 30; i++) {
    try {
      await pool.query("SELECT 1");
      break;
    } catch {
      await new Promise((r) => setTimeout(r, 1000));
    }
  }
  app.listen(3000);
}

start();
