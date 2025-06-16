<?php
include 'koneksi.php';
$id = $_GET['id'];
$data = $koneksi->query("SELECT * FROM customer WHERE id_customer = '$id'")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $koneksi->prepare("UPDATE customer SET nama_customer=?, alamat=?, telp=?, fax=?, email=? WHERE id_customer=?");
    $stmt->bind_param("sssssi", $_POST['nama'], $_POST['alamat'], $_POST['telp'], $_POST['fax'], $_POST['email'], $id);
    $stmt->execute();
    header("Location: customer.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <h3>Edit Customer</h3>
    <form method="post">
        <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" value="<?= $data['nama_customer'] ?>"></div>
        <div class="mb-3"><label>Alamat</label><input type="text" name="alamat" class="form-control" value="<?= $data['alamat'] ?>"></div>
        <div class="mb-3"><label>Telp</label><input type="text" name="telp" class="form-control" value="<?= $data['telp'] ?>"></div>
        <div class="mb-3"><label>Fax</label><input type="text" name="fax" class="form-control" value="<?= $data['fax'] ?>"></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= $data['email'] ?>"></div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>

</html>