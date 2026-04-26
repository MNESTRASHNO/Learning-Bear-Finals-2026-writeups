<?php
require_once 'includes/config.php';
require_once 'includes/ImageProcessor.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        flash('Upload failed. Please select a valid file.', 'error');
        redirect('index.php');
    }

    $file = $_FILES['image'];

    if ($file['size'] > MAX_FILE_SIZE) {
        flash('File exceeds the maximum size of 5 MB.', 'error');
        redirect('index.php');
    }

    $processor = new ImageProcessor($file['tmp_name']);
    $mime = $processor->getMimeType();

    if (!in_array($mime, ALLOWED_TYPES)) {
        flash('Invalid file type. Allowed: JPG, PNG, GIF, WebP.', 'error');
        redirect('index.php');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        flash('Invalid file extension.', 'error');
        redirect('index.php');
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        flash('Failed to save the uploaded file.', 'error');
        redirect('index.php');
    }

    flash('Image uploaded successfully!');
    redirect('view.php?file=' . urlencode($filename));

} elseif ($action === 'import') {
    $url = trim($_POST['url'] ?? '');

    if (empty($url)) {
        flash('Please provide a URL.', 'error');
        redirect('index.php');
    }

    $data = @file_get_contents($url);

    if ($data === false) {
        flash('Could not fetch the image from the provided URL.', 'error');
        redirect('index.php');
    }

    $tmp = tempnam(sys_get_temp_dir(), 'pv_import_');
    file_put_contents($tmp, $data);

    $processor = new ImageProcessor($tmp);
    $mime = $processor->getMimeType();

    if (!in_array($mime, ALLOWED_TYPES)) {
        @unlink($tmp);
        flash('The URL does not point to a valid image.', 'error');
        redirect('index.php');
    }

    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    $ext = $extMap[$mime] ?? 'jpg';

    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    rename($tmp, UPLOAD_DIR . $filename);

    flash('Image imported successfully!');
    redirect('view.php?file=' . urlencode($filename));
}

redirect('index.php');
