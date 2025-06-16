<?php
include 'koneksi.php';

if (isset($_GET['tipe']) && isset($_GET['id'])) {
    $tipe = $_GET['tipe'];
    $id = intval($_GET['id']);

    if ($tipe == 'petugas') {
        $query = $koneksi->query("DELETE FROM petugas WHERE id_user = $id");
    } elseif ($tipe == 'manager') {
        $query = $koneksi->query("DELETE FROM manager WHERE id_user = $id");
    }

    header("Location: user.php"); // ganti dengan file yang sesuai
    exit;
}
