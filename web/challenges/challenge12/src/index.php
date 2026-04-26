<?php
require_once 'includes/config.php';
$flash = getFlash();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> &mdash; Image Hosting</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="/" class="logo"><b>Pixel</b>Vault</a>
        <div class="nav-links">
            <a href="/" class="active">Upload</a>
            <a href="gallery.php">Gallery</a>
        </div>
    </nav>

    <section class="hero">
        <h1>Share Your Images</h1>
        <p>Fast, simple, and reliable image hosting</p>
    </section>

    <?php if ($flash): ?>
    <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
        <div class="flash-inner"><?= htmlspecialchars($flash['message']) ?></div>
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="card">
            <div class="tabs">
                <button class="tab active" data-tab="upload">Upload File</button>
                <button class="tab" data-tab="import">Import from URL</button>
            </div>

            <div class="tab-panel active" id="tab-upload">
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="dropzone" id="dropzone">
                        <input type="file" name="image" id="file-input" accept="image/*">
                        <div class="dropzone-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <h3>Drop your image here</h3>
                        <p>or click to browse &middot; JPG, PNG, GIF, WebP &middot; Max 5 MB</p>
                    </div>
                    <input type="hidden" name="action" value="upload">
                    <button type="submit" class="btn btn-primary btn-upload">Upload Image</button>
                </form>
            </div>

            <div class="tab-panel" id="tab-import">
                <form action="upload.php" method="POST">
                    <p class="url-form-hint">Fetch an image directly from any accessible URL</p>
                    <div class="url-input">
                        <input type="text" name="url" placeholder="https://example.com/photo.jpg" required>
                        <input type="hidden" name="action" value="import">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer"><?= SITE_NAME ?> &copy; <?= date('Y') ?></footer>

    <script>
    document.querySelectorAll('.tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.remove('active'); });
            tab.classList.add('active');
            document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
        });
    });

    var dropzone = document.getElementById('dropzone');
    var fileInput = document.getElementById('file-input');

    ['dragenter', 'dragover'].forEach(function(evt) {
        dropzone.addEventListener(evt, function(e) {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(function(evt) {
        dropzone.addEventListener(evt, function(e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        });
    });

    dropzone.addEventListener('drop', function(e) {
        fileInput.files = e.dataTransfer.files;
        showFile();
    });

    fileInput.addEventListener('change', showFile);

    function showFile() {
        if (fileInput.files.length) {
            dropzone.querySelector('h3').textContent = fileInput.files[0].name;
            dropzone.querySelector('p').textContent = (fileInput.files[0].size / 1024).toFixed(1) + ' KB';
        }
    }
    </script>
</body>
</html>
