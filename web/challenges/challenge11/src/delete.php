<?php
require_once 'includes/config.php';

$file = $_GET['file'] ?? '';

if (empty($file) || !preg_match('/^[a-f0-9]+\.\w+$/', $file)) {
    flash('Invalid image reference.', 'error');
    redirect('gallery.php');
}

$path = UPLOAD_DIR . $file;

if (file_exists($path)) {
    unlink($path);
    flash('Image deleted.');
} else {
    flash('Image not found.', 'error');
}

redirect('gallery.php');
