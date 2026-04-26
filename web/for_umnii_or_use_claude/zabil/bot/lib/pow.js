const crypto = require('crypto');

function verify(stamp, expectedResource, requiredBits) {
  const parts = stamp.split(':');
  if (parts.length !== 7) return false;

  const [ver, bits, , resource] = parts;

  if (ver !== '1') return false;
  if (parseInt(bits) < requiredBits) return false;
  if (resource !== expectedResource) return false;

  const hash = crypto.createHash('sha1').update(stamp).digest();
  const leadingZeros = countLeadingZeroBits(hash);

  return leadingZeros >= requiredBits;
}

function countLeadingZeroBits(buf) {
  let count = 0;
  for (let i = 0; i < buf.length; i++) {
    if (buf[i] === 0) {
      count += 8;
    } else {
      let byte = buf[i];
      while ((byte & 0x80) === 0) {
        count++;
        byte <<= 1;
      }
      break;
    }
  }
  return count;
}

module.exports = { verify };
