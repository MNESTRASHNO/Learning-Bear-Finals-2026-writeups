<?php
require_once 'includes/config.php';
require_once 'includes/ImageProcessor.php';

$file = $_GET['file'] ?? '';

if (empty($file) || !preg_match('/^[a-f0-9]+\.\w+$/', $file)) {
    flash('Invalid image reference.', 'error');
    redirect('gallery.php');
}

$path = UPLOAD_DIR . $file;
if (!file_exists($path)) {
    flash('Image not found.', 'error');
    redirect('gallery.php');
}

$processor = new ImageProcessor($path);
$mime = $processor->getMimeType();
$size = $processor->getFileSize();
$dims = $processor->getDimensions();
$ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
$uploaded = date('F j, Y \a\t g:i A', filemtime($path));

$flash = getFlash();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($file) ?> &mdash; <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/" class="logo"><b>Pixel</b>Vault</a>
        <div class="nav-links">
            <a href="/">Upload</a>
            <a href="gallery.php">Gallery</a>
        </div>
    </nav>

    <?php if ($flash): ?>
    <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
        <div class="flash-inner"><?= htmlspecialchars($flash['message']) ?></div>
    </div>
    <?php endif; ?>

    <div class="container" style="max-width: 900px;">
        <div class="view-card">
            <div class="view-image">
                <img src="uploads/<?= htmlspecialchars($file) ?>" alt="<?= htmlspecialchars($file) ?>">
            </div>
            <div class="view-details">
                <div class="view-title"><?= htmlspecialchars($file) ?></div>
                <div class="meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">Format</span>
                        <span class="meta-value"><?= htmlspecialchars($ext) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">File Size</span>
                        <span class="meta-value"><?= formatSize($size) ?></span>
                    </div>
                    <?php if ($dims): ?>
                    <div class="meta-item">
                        <span class="meta-label">Dimensions</span>
                        <span class="meta-value"><?= $dims['width'] ?> &times; <?= $dims['height'] ?> px</span>
                    </div>
                    <?php endif; ?>
                    <div class="meta-item">
                        <span class="meta-label">Uploaded</span>
                        <span class="meta-value"><?= $uploaded ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">MIME Type</span>
                        <span class="meta-value"><?= htmlspecialchars($mime) ?></span>
                    </div>
                </div>
                <div class="view-actions">
                    <a href="uploads/<?= htmlspecialchars($file) ?>" class="btn btn-primary" download>Download</a>
                    <a href="gallery.php" class="btn btn-secondary">Back to Gallery</a>
                    <a href="delete.php?file=<?= urlencode($file) ?>" class="btn btn-secondary" style="color: var(--error);" onclick="return confirm('Delete this image?')">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer"><?= SITE_NAME ?> &copy; <?= date('Y') ?></footer>
</body>
</html>
