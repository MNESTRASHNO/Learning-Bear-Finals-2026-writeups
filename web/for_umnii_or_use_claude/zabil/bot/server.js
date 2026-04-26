const net = require('net');
const crypto = require('crypto');
const { verify } = require('./lib/pow');
const { visit } = require('./lib/browser');

const PORT = 1337;
const COMPLEXITY = parseInt(process.env.POW_COMPLEXITY || '26');
const TIMEOUT = 60_000;

const server = net.createServer(socket => {
  socket.setTimeout(TIMEOUT);
  socket.on('timeout', () => {
    socket.write('Timeout.\n');
    socket.destroy();
  });

  const resource = crypto.randomBytes(12).toString('hex');

  socket.write(`=== NoteKeeper Bot ===\n`);
  socket.write(`Solve proof-of-work first.\n`);
  socket.write(`hashcash -mb${COMPLEXITY} ${resource}\n`);
  socket.write(`stamp> `);

  let buffer = '';
  let stage = 'pow';

  socket.on('data', async chunk => {
    buffer += chunk.toString();

    while (buffer.includes('\n')) {
      const idx = buffer.indexOf('\n');
      const line = buffer.slice(0, idx).trim();
      buffer = buffer.slice(idx + 1);

      if (stage === 'pow') {
        if (!verify(line, resource, COMPLEXITY)) {
          socket.write('Invalid stamp.\n');
          socket.destroy();
          return;
        }
        socket.write('OK. Send note ID to visit:\nid> ');
        stage = 'id';
      } else if (stage === 'id') {
        if (!/^[0-9a-f]{16}$/.test(line)) {
          socket.write('Invalid note ID format.\n');
          socket.destroy();
          return;
        }
        socket.write('Visiting note...\n');
        stage = 'done';
        try {
          await visit(line);
          socket.write('Done.\n');
        } catch (err) {
          console.error('Bot error:', err.message);
          socket.write('Error visiting note.\n');
        }
        socket.destroy();
      }
    }
  });

  socket.on('error', () => {});
});

server.listen(PORT, () => {
  console.log(`Bot service listening on port ${PORT}`);
});
