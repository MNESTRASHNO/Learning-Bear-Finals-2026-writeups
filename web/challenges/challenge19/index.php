<?php
// Vulnerable web application — CTF Challenge: LFI + PHPINFO Race
// The "page" parameter is vulnerable to Local File Inclusion.

$page = isset($_GET['page']) ? $_GET['page'] : null;
?>
<!DOCTYPE html>
<html>
<head><title>TechNotes - Internal Wiki</title></head>
<body>
<h1>TechNotes Internal Wiki</h1>
<nav>
    <a href="?page=pages/home.php">Home</a> |
    <a href="?page=pages/about.php">About</a> |
    <a href="?page=pages/contact.php">Contact</a>
</nav>
<hr>
<?php
if ($page) {
    // LFI vulnerability: no sanitization on user input
    if (!include($page)) {
        echo "<p>Page not found.</p>";
    }
} else {
    echo "<p>Welcome to TechNotes. Select a page above.</p>";
}
?>
</body>
</html>
