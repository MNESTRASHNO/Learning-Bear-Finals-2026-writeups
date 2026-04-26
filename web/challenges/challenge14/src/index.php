<?php
session_start();

$page = $_GET['page'] ?? 'home.php';
$path = 'pages/' . $page;

if (!file_exists($path)) {
    http_response_code(404);
    $path = 'pages/404.php';
}

$current = $page;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CloudDrop</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <div class="container header-inner">
            <a href="/" class="logo">
                <span class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 10h-1.3A8 8 0 103 16h15a5 5 0 000-10z"/>
                    </svg>
                </span>
                CloudDrop
            </a>
            <nav>
                <a href="/?page=home.php"<?= $current === 'home.php' ? ' class="active"' : '' ?>>Home</a>
                <a href="/?page=upload.php"<?= $current === 'upload.php' ? ' class="active"' : '' ?>>Upload</a>
                <a href="/?page=files.php"<?= $current === 'files.php' ? ' class="active"' : '' ?>>Files</a>
            </nav>
        </div>
    </header>
    <main class="container">
        <?php require($path); ?>
    </main>
    <footer>
        <div class="container footer-inner">
            <p>CloudDrop &mdash; Simple file hosting</p>
        </div>
    </footer>
</body>
</html>
