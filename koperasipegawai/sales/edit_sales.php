<?php
include '../koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: sales.php");
    exit();
}

$id_sales = $koneksi->real_escape_string($_GET['id']);

// Ambil data sales yang akan diedit
$sql_ambil_data = "SELECT * FROM sales WHERE id_sales = '$id_sales'";
$result_ambil_data = $koneksi->query($sql_ambil_data);

if ($result_ambil_data->num_rows == 0) {
    echo "Sales tidak ditemukan!";
    exit();
}
$data_sales = $result_ambil_data->fetch_assoc();

// Ambil data customer untuk dropdown
$sql_customers = "SELECT id_customer, nama_customer FROM customer ORDER BY nama_customer ASC";
$result_customers = $koneksi->query($sql_customers);

if (isset($_POST['submit'])) {
    $tgl_sales = $koneksi->real_escape_string($_POST['tgl_sales']);
    $id_customer = $koneksi->real_escape_string($_POST['id_customer']);
    $do_number = $koneksi->real_escape_string($_POST['do_number']);
    $status = $koneksi->real_escape_string($_POST['status']);

    $sql_update = "UPDATE sales SET
                    tgl_sales = '$tgl_sales',
                    id_customer = '$id_customer',
                    do_number = '$do_number',
                    status = '$status'
                    WHERE id_sales = '$id_sales'";

    if ($koneksi->query($sql_update) === TRUE) {
        header("Location: sales.php?status=updated");
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
    <title>Edit Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Sales</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="tgl_sales" class="form-label">Tanggal Sales</label>
                <input type="date" class="form-control" id="tgl_sales" name="tgl_sales" value="<?php echo $data_sales['tgl_sales']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_customer" class="form-label">Customer</label>
                <select class="form-select" id="id_customer" name="id_customer" required>
                    <option value="">Pilih Customer</option>
                    <?php while ($customer = $result_customers->fetch_assoc()): ?>
                        <option value="<?php echo $customer['id_customer']; ?>" <?php echo ($customer['id_customer'] == $data_sales['id_customer']) ? 'selected' : ''; ?>>
                            <?php echo $customer['nama_customer']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="do_number" class="form-label">Nomor DO</label>
                <input type="text" class="form-control" id="do_number" name="do_number" value="<?php echo $data_sales['do_number']; ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Pending" <?php echo ($data_sales['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Paid" <?php echo ($data_sales['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="Canceled" <?php echo ($data_sales['status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update</button>
            <a href="sales.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $koneksi->close(); ?>