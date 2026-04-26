<?php
require_once 'includes/config.php';
require_once 'includes/ImageProcessor.php';

$images = [];
$files = @scandir(UPLOAD_DIR);
if ($files) {
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) continue;
        $path = UPLOAD_DIR . $f;
        $images[] = [
            'name'  => $f,
            'size'  => filesize($path),
            'mtime' => filemtime($path),
        ];
    }
}
usort($images, function($a, $b) { return $b['mtime'] - $a['mtime']; });
$flash = getFlash();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery &mdash; <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/" class="logo"><b>Pixel</b>Vault</a>
        <div class="nav-links">
            <a href="/">Upload</a>
            <a href="gallery.php" class="active">Gallery</a>
        </div>
    </nav>

    <?php if ($flash): ?>
    <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
        <div class="flash-inner"><?= htmlspecialchars($flash['message']) ?></div>
    </div>
    <?php endif; ?>

    <div class="container-wide">
        <div class="page-header">
            <h2>Gallery</h2>
            <span class="badge"><?= count($images) ?> image<?= count($images) !== 1 ? 's' : '' ?></span>
        </div>

        <?php if (empty($images)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <h3>No images yet</h3>
            <p>Upload your first image to get started.</p>
            <a href="/" class="btn btn-primary">Upload Image</a>
        </div>
        <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($images as $img): ?>
            <a href="view.php?file=<?= urlencode($img['name']) ?>" class="gallery-card">
                <div class="gallery-thumb">
                    <img src="uploads/<?= htmlspecialchars($img['name']) ?>" alt="<?= htmlspecialchars($img['name']) ?>" loading="lazy">
                </div>
                <div class="gallery-info">
                    <div class="gallery-name"><?= htmlspecialchars($img['name']) ?></div>
                    <div class="gallery-meta">
                        <span><?= formatSize($img['size']) ?></span>
                        <span><?= date('M j, Y', $img['mtime']) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <footer class="footer"><?= SITE_NAME ?> &copy; <?= date('Y') ?></footer>
</body>
</html>
