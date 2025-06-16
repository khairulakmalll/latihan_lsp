<?php
include '../koneksi.php'; // Path disesuaikan

if (!isset($_GET['id'])) {
    header("Location: items.php"); // Redirect ke items.php di folder yang sama
    exit();
}

$id_item = $koneksi->real_escape_string($_GET['id']);

// Ambil data item yang akan diedit
$sql_ambil_data = "SELECT * FROM item WHERE id_item = '$id_item'";
$result_ambil_data = $koneksi->query($sql_ambil_data);

if ($result_ambil_data->num_rows == 0) {
    echo "Item tidak ditemukan!";
    exit();
}

$data_item = $result_ambil_data->fetch_assoc();

if (isset($_POST['submit'])) {
    $nama_item = $koneksi->real_escape_string($_POST['nama_item']);
    $uom = $koneksi->real_escape_string($_POST['uom']);
    $harga_beli = $koneksi->real_escape_string($_POST['harga_beli']);
    $harga_jual = $koneksi->real_escape_string($_POST['harga_jual']);

    $sql_update = "UPDATE item SET
                    nama_item = '$nama_item',
                    uom = '$uom',
                    harga_beli = '$harga_beli',
                    harga_jual = '$harga_jual'
                    WHERE id_item = '$id_item'";

    if ($koneksi->query($sql_update) === TRUE) {
        header("Location: items.php?status=updated"); // Redirect ke items.php di folder yang sama
        exit();
    } else {
        echo "Error: " . $sql_update . "<br>" . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Item</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="nama_item" class="form-label">Nama Item</label>
                <input type="text" class="form-control" id="nama_item" name="nama_item" value="<?php echo $data_item['nama_item']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="uom" class="form-label">Unit of Measure (UOM)</label>
                <input type="text" class="form-control" id="uom" name="uom" value="<?php echo $data_item['uom']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga_beli" class="form-label">Harga Beli</label>
                <input type="number" class="form-control" id="harga_beli" name="harga_beli" step="0.01" value="<?php echo $data_item['harga_beli']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="harga_jual" class="form-label">Harga Jual</label>
                <input type="number" class="form-control" id="harga_jual" name="harga_jual" step="0.01" value="<?php echo $data_item['harga_jual']; ?>" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update</button>
            <a href="items.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $koneksi->close(); ?>