<?php

if (!isset($_GET['url'])) {
    echo '<h1>URL Fetcher</h1>';
    echo '<p>Fetch content from a URL.</p>';
    echo '<form method="GET">';
    echo '<input type="text" name="url" placeholder="Enter URL" size="60">';
    echo '<button type="submit">Fetch</button>';
    echo '</form>';
    exit;
}

$url = $_GET['url'];

$blocked = ['file://', 'php://', 'data://', 'glob://', 'phar://', 'zip://', 'zlib://', 'rar://'];
foreach ($blocked as $scheme) {
    if (stripos($url, $scheme) !== false) {
        die('Blocked scheme detected.');
    }
}

if (preg_match('#(^/|\.\./)#', $url)) {
    die('Local file access is not allowed.');
}

$content = @file_get_contents($url);

if ($content === false) {
    die('Failed to fetch URL.');
}

echo '<h2>Response:</h2>';
echo '<pre>' . htmlspecialchars($content) . '</pre>';
