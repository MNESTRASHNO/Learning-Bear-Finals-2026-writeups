const express = require("express");

const app = express();
const TOKEN = process.env.TOKEN;
const FLAG = process.env.FLAG;

function parseCookies(header) {
  const result = {};
  if (!header) return result;
  header.split(";").forEach((pair) => {
    const [name, ...rest] = pair.split("=");
    result[name.trim()] = rest.join("=").trim();
  });
  return result;
}

app.get("/profile", (req, res) => {
  const cookies = parseCookies(req.headers.cookie);
  if (cookies.token !== TOKEN) {
    return res.status(403).json({ error: "forbidden" });
  }
  res.json({ username: cookies.username, role: "user" });
});

app.get("/flag", (req, res) => {
  const cookies = parseCookies(req.headers.cookie);
  const token = cookies.token || req.query.token;
  if (token !== TOKEN) {
    return res.status(403).json({ error: "forbidden" });
  }
  res.json({ flag: FLAG });
});

app.listen(5000);
