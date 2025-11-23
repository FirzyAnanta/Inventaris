<?php
require 'config/koneksi.php';

// Kita buat password "123" menjadi kode rahasia (Hash)
$password_baru = password_hash("123", PASSWORD_DEFAULT);

// 1. Update Admin (NIP 888)
$query1 = "UPDATE users SET password = '$password_baru' WHERE nomor_induk = '888'";
mysqli_query($conn, $query1);

// 2. Update Superadmin (NIP 999)
$query2 = "UPDATE users SET password = '$password_baru' WHERE nomor_induk = '999'";
mysqli_query($conn, $query2);

echo "<h1>Berhasil!</h1>";
echo "Password Admin (888) dan Superadmin (999) sudah diubah menjadi: <b>123</b><br>";
echo "<a href='index.php'>Klik disini untuk Login</a>";
?>