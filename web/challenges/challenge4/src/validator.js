const { URL } = require('url');
const net = require('net');

const BLOCKED_HOSTS = new Set([
  'localhost',
  'localhost.localdomain',
  'ip6-localhost',
  'ip6-loopback',
  'metadata.google.internal',
  'metadata.internal',
  'instance-data',
  'kubernetes.default',
  'kubernetes.default.svc',
  'kubernetes.default.svc.cluster.local',
]);

const BLOCKED_TLDS = ['.local', '.internal', '.localhost'];

function parseIPv4(hostname) {
  if (/^0x[0-9a-f]+$/i.test(hostname)) {
    const num = parseInt(hostname, 16);
    if (num >= 0 && num <= 0xFFFFFFFF) {
      return [(num >>> 24) & 0xFF, (num >>> 16) & 0xFF, (num >>> 8) & 0xFF, num & 0xFF];
    }
  }

  if (/^\d+$/.test(hostname)) {
    const num = parseInt(hostname, 10);
    if (num >= 0 && num <= 0xFFFFFFFF) {
      return [(num >>> 24) & 0xFF, (num >>> 16) & 0xFF, (num >>> 8) & 0xFF, num & 0xFF];
    }
  }

  if (/^0[0-7]+$/.test(hostname)) {
    const num = parseInt(hostname, 8);
    if (num >= 0 && num <= 0xFFFFFFFF) {
      return [(num >>> 24) & 0xFF, (num >>> 16) & 0xFF, (num >>> 8) & 0xFF, num & 0xFF];
    }
  }

  const parts = hostname.split('.');
  if (parts.length >= 2 && parts.length <= 4) {
    const octets = [];
    let allNumeric = true;
    for (const part of parts) {
      let val;
      if (/^0x[0-9a-f]+$/i.test(part)) {
        val = parseInt(part, 16);
      } else if (/^0[0-7]+$/.test(part) && part !== '0') {
        val = parseInt(part, 8);
      } else if (/^\d+$/.test(part)) {
        val = parseInt(part, 10);
      } else {
        allNumeric = false;
        break;
      }
      octets.push(val);
    }
    if (allNumeric && octets.length === parts.length) {
      if (octets.every(o => o >= 0 && o <= 255)) {
        while (octets.length < 4) octets.push(0);
        return octets;
      }
    }
  }

  return null;
}

function isPrivateIPv4(octets) {
  const [a, b, c, d] = octets;
  if (a === 0) return true;
  if (a === 127) return true;
  if (a === 10) return true;
  if (a === 172 && b >= 16 && b <= 31) return true;
  if (a === 192 && b === 168) return true;
  if (a === 169 && b === 254) return true;
  if (a === 255 && b === 255 && c === 255 && d === 255) return true;
  return false;
}

function isPrivateIPv6(hostname) {
  const addr = hostname.replace(/^\[|\]$/g, '');
  if (addr === '::1' || addr === '::') return true;
  if (/^fe80:/i.test(addr)) return true;
  if (/^f[cd]/i.test(addr)) return true;

  const v4mapped = addr.match(/::ffff:(.+)$/i);
  if (v4mapped) {
    const inner = v4mapped[1];
    if (net.isIPv4(inner)) {
      const octets = inner.split('.').map(Number);
      if (isPrivateIPv4(octets)) return true;
    }
    const hexMatch = inner.match(/^([0-9a-f]{1,4}):([0-9a-f]{1,4})$/i);
    if (hexMatch) {
      const high = parseInt(hexMatch[1], 16);
      const low = parseInt(hexMatch[2], 16);
      const octets = [(high >> 8) & 0xFF, high & 0xFF, (low >> 8) & 0xFF, low & 0xFF];
      if (isPrivateIPv4(octets)) return true;
    }
  }

  return false;
}

function validate(urlString) {
  let parsed;
  try {
    parsed = new URL(urlString);
  } catch {
    throw new Error('Invalid URL format');
  }

  if (!['http:', 'https:'].includes(parsed.protocol)) {
    throw new Error('Only HTTP and HTTPS protocols are allowed');
  }

  if (parsed.username || parsed.password) {
    throw new Error('Credentials in URL are not allowed');
  }

  const hostname = parsed.hostname.toLowerCase();

  if (!hostname || hostname.length === 0) {
    throw new Error('Empty hostname');
  }

  if (BLOCKED_HOSTS.has(hostname)) {
    throw new Error('This hostname is not allowed');
  }

  for (const tld of BLOCKED_TLDS) {
    if (hostname.endsWith(tld)) {
      throw new Error('This domain suffix is not allowed');
    }
  }

  const bracketless = hostname.replace(/^\[|\]$/g, '');
  if (net.isIPv6(bracketless)) {
    if (isPrivateIPv6(hostname)) {
      throw new Error('Private IPv6 addresses are not allowed');
    }
    return parsed;
  }

  if (net.isIPv4(hostname)) {
    const octets = hostname.split('.').map(Number);
    if (isPrivateIPv4(octets)) {
      throw new Error('Private IPv4 addresses are not allowed');
    }
    return parsed;
  }

  const ipv4 = parseIPv4(hostname);
  if (ipv4) {
    if (isPrivateIPv4(ipv4)) {
      throw new Error('Encoded private IP addresses are not allowed');
    }
    return parsed;
  }

  return parsed;
}

module.exports = { validate };
