<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    echo "<script>alert('Bạn không có quyền truy cập Admin!'); window.location.href='../index.php';</script>";
    exit();
}
?>