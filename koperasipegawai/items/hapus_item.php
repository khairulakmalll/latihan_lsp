<?php
include '../koneksi.php'; // Path disesuaikan

if (isset($_GET['id'])) {
    $id_item = $koneksi->real_escape_string($_GET['id']);

    // Hapus data dari database
    $sql_hapus = "DELETE FROM item WHERE id_item = '$id_item'";

    if ($koneksi->query($sql_hapus) === TRUE) {
        header("Location: items.php?status=deleted"); // Redirect kembali ke items.php setelah hapus
        exit();
    } else {
        echo "Error: " . $sql_hapus . "<br>" . $koneksi->error;
    }
} else {
    // Jika tidak ada ID yang diberikan, redirect kembali
    header("Location: items.php");
    exit();
}

$koneksi->close();
