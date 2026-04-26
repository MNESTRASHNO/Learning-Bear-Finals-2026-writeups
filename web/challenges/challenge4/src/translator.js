const emojiTable = require('./emojiTable');

function translate(buffer) {
  let result = '';
  for (const byte of buffer) {
    result += emojiTable[byte];
  }
  return result;
}

module.exports = { translate };
