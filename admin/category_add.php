<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login/');
    exit;
}

include '../config.php';
$db = new Database();
?>

<?php include './header.php'; ?>

<div class="row">
    <!-- Bu yerda code -->
</div>

<?php include './footer.php'; ?>