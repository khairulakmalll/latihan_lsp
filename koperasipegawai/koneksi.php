<?php
$koneksi = new mysqli("localhost", "root", "", "koperasipegawai");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
