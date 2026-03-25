<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /inventory/login.php');
    exit;
}
?>