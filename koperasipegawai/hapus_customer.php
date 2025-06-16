<?php
include 'koneksi.php';
$id = $_GET['id'];
$koneksi->query("DELETE FROM customer WHERE id_customer = '$id'");
header("Location: customer.php");
exit;
