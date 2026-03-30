<?php
// Koneksi database menggunakan MySQLi
$koneksi = mysqli_connect("localhost", "root", "", "db_aspirasi");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
