<?php
echo "<h1>Project 1: PHP & MySQL chay thanh cong!</h1>";
echo "<p>Chao mung Long den voi Docker.</p>";

$conn = new mysqli("db", "root", "matkhau123", "thuc_tap_db");
if ($conn->connect_error) {
    die("Ket noi Database that bai: " . $conn->connect_error);
}
echo "<h3 style='color:green'>Ket noi Database MySQL OK!</h3>";
?>