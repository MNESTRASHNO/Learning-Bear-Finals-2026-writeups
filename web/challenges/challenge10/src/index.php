<?php

$flag = trim(file_get_contents('/flag.txt'));

if (!isset($_GET['file'])) {
    echo '<h1>File Viewer</h1>';
    echo '<form method="GET">';
    echo '<input type="text" name="file" placeholder="Enter filename">';
    echo '<button type="submit">Read</button>';
    echo '</form>';
    exit;
}

$file = $_GET['file'];
$content = file_get_contents($file);

if ($content === false) {
    die('Cannot read file.');
}

if (trim($content) === $flag) {
    die('Access denied.');
}

echo '<pre>' . htmlspecialchars($content) . '</pre>';
