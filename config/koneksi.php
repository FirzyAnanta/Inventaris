<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_inventaris_telkom";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

$base_url = "http://localhost/inventaris_telkom/";
?>