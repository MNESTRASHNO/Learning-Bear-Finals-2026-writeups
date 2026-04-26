async function fetchUrl(parsed) {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 5000);

  try {
    const response = await fetch(parsed.toString(), {
      redirect: 'error',
      signal: controller.signal,
      headers: { 'User-Agent': 'FunnyTranslator/1.0' },
    });

    if (!response.ok) {
      throw new Error('Remote server returned ' + response.status);
    }

    const arrayBuffer = await response.arrayBuffer();
    const buffer = Buffer.from(arrayBuffer);

    if (buffer.length > 1024 * 512) {
      throw new Error('Response too large (max 512KB)');
    }

    return buffer;
  } finally {
    clearTimeout(timeout);
  }
}

module.exports = { fetchUrl };
