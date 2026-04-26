<?php
session_start();

$lang = $_GET['lang'] ?? 'en';
$page = $_GET['page'] ?? 'home';

$allowed_langs = ['en', 'fr', 'de', 'es'];
if (!in_array($lang, $allowed_langs)) {
    $lang = 'en';
}

$template = "templates/{$lang}/{$page}.php";
include($template);
