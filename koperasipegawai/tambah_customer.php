<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $koneksi->prepare("INSERT INTO customer (nama_customer, alamat, telp, fax, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $_POST['nama'], $_POST['alamat'], $_POST['telp'], $_POST['fax'], $_POST['email']);
    $stmt->execute();
    header("Location: customer.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Tambah Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <h3>Tambah Customer</h3>
    <form method="post">
        <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
        <div class="mb-3"><label>Alamat</label><input type="text" name="alamat" class="form-control"></div>
        <div class="mb-3"><label>Telp</label><input type="text" name="telp" class="form-control"></div>
        <div class="mb-3"><label>Fax</label><input type="text" name="fax" class="form-control"></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>

</html>