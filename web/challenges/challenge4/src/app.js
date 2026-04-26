const express = require('express');
const path = require('path');
const { validate } = require('./validator');
const { fetchUrl } = require('./fetcher');
const { translate } = require('./translator');
const emojiTable = require('./emojiTable');

const app = express();
const FLAG = process.env.FLAG || 'flag{redacted}';

app.use(express.json());
app.use(express.urlencoded({ extended: false }));

app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'views', 'index.html'));
});

app.get('/table', (req, res) => {
  res.json(emojiTable);
});

app.post('/translate', async (req, res) => {
  try {
    const { url } = req.body;
    if (!url || typeof url !== 'string') {
      return res.status(400).json({ error: 'URL is required' });
    }

    const parsed = validate(url.trim());
    const content = await fetchUrl(parsed);
    const result = translate(content);

    res.json({ result, bytes: content.length });
  } catch (err) {
    res.status(400).json({ error: err.message });
  }
});

app.get('/flag', (req, res) => {
  const ip = req.socket.remoteAddress;
  if (ip === '127.0.0.1' || ip === '::ffff:127.0.0.1' || ip === '::1') {
    return res.send(FLAG);
  }
  res.status(403).send('Forbidden');
});

app.listen(11004, '0.0.0.0');
