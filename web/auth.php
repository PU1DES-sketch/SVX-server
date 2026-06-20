<?php
session_start();

$ADMIN_USER = 'admin';
$ADMIN_PASS_HASH = password_hash('admin123', PASSWORD_DEFAULT);

function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
?>
