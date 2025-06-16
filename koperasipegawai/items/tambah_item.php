<?php
include '../koneksi.php'; // Path disesuaikan

if (isset($_POST['submit'])) {
    $nama_item = $koneksi->real_escape_string($_POST['nama_item']);
    $uom = $koneksi->real_escape_string($_POST['uom']);
    $harga_beli = $koneksi->real_escape_string($_POST['harga_beli']);
    $harga_jual = $koneksi->real_escape_string($_POST['harga_jual']);

    $sql_tambah = "INSERT INTO item (nama_item, uom, harga_beli, harga_jual) VALUES ('$nama_item', '$uom', '$harga_beli', '$harga_jual')";

    if ($koneksi->query($sql_tambah) === TRUE) {
        header("Location: items.php?status=added"); // Redirect ke items.php di folder yang sama
        exit();
    } else {
        echo "Error: " . $sql_tambah . "<br>" . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Item Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Tambah Item Baru</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="nama_item" class="form-label">Nama Item</label>
                <input type="text" class="form-control" id="nama_item" name="nama_item" required>
            </div>
            <div class="mb-3">
                <label for="uom" class="form-label">Unit of Measure (UOM)</label>
                <input type="text" class="form-control" id="uom" name="uom" required>
            </div>
            <div class="mb-3">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="harga_jual" class="form-label">Harga Jual</label>
                <input type="number" class="form-control" id="harga_jual" name="harga_jual" step="0.01" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            <a href="items.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $koneksi->close(); ?>