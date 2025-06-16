<?php
include 'koneksi.php';

if (!isset($_GET['id_sales'])) {
    header("Location: sales.php");
    exit();
}

$id_sales = $koneksi->real_escape_string($_GET['id_sales']);

// Ambil info sales
$sql_sales_info = "SELECT s.*, c.nama_customer FROM sales s JOIN customer c ON s.id_customer = c.id_customer WHERE s.id_sales = '$id_sales'";
$result_sales_info = $koneksi->query($sql_sales_info);
if ($result_sales_info->num_rows == 0) {
    echo "Sales tidak ditemukan!";
    exit();
}
$sales_info = $result_sales_info->fetch_assoc();

// Logika Hapus Detail Transaksi
if (isset($_GET['action']) && $_GET['action'] == 'hapus_detail' && isset($_GET['id_transaction'])) {
    $id_transaction_to_delete = $koneksi->real_escape_string($_GET['id_transaction']);
    $sql_hapus_detail = "DELETE FROM transaction WHERE id_transaction = '$id_transaction_to_delete'";
    if ($koneksi->query($sql_hapus_detail) === TRUE) {
        header("Location: detail_sales.php?id_sales=$id_sales&status=detail_deleted");
        exit();
    } else {
        echo "Error: " . $sql_hapus_detail . "<br>" . $koneksi->error;
    }
}

// Ambil data transaksi (detail sales)
$sql_detail_trans = "SELECT t.*, i.nama_item, i.uom
                     FROM transaction t
                     JOIN item i ON t.id_item = i.id_item
                     WHERE t.id_sales = '$id_sales'
                     ORDER BY t.id_transaction ASC";
$result_detail_trans = $koneksi->query($sql_detail_trans);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sales #<?php echo $id_sales; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Detail Sales #<?php echo $sales_info['id_sales']; ?></h2>
        <p><strong>Tanggal Sales:</strong> <?php echo $sales_info['tgl_sales']; ?></p>
        <p><strong>Customer:</strong> <?php echo $sales_info['nama_customer']; ?></p>
        <p><strong>Nomor DO:</strong> <?php echo $sales_info['do_number']; ?></p>
        <p><strong>Status:</strong> <?php echo $sales_info['status']; ?></p>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'detail_added'): ?>
            <div class="alert alert-success">Detail transaksi berhasil ditambahkan!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'detail_updated'): ?>
            <div class="alert alert-success">Detail transaksi berhasil diperbarui!</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'detail_deleted'): ?>
            <div class="alert alert-warning">Detail transaksi berhasil dihapus!</div>
        <?php endif; ?>

        <a href="tambah_detail_transaksi.php?id_sales=<?php echo $id_sales; ?>" class="btn btn-success mb-3">Tambah Item Transaksi</a>
        <a href="sales.php" class="btn btn-secondary mb-3">Kembali ke Daftar Sales</a>

        <h4 class="mt-4">Item Transaksi</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total_amount_sales = 0; ?>
                    <?php if ($result_detail_trans->num_rows > 0): ?>
                        <?php while ($row = $result_detail_trans->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id_transaction']; ?></td>
                                <td><?php echo $row['nama_item'] . ' (' . $row['uom'] . ')'; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                                <td><?php echo number_format($row['amount'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="edit_detail_transaksi.php?id=<?php echo $row['id_transaction']; ?>&id_sales=<?php echo $id_sales; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="detail_sales.php?action=hapus_detail&id_transaction=<?php echo $row['id_transaction']; ?>&id_sales=<?php echo $id_sales; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus item transaksi ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php $total_amount_sales += $row['amount']; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada item untuk sales ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Amount Sales:</th>
                        <th><?php echo number_format($total_amount_sales, 0, ',', '.'); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $koneksi->close(); ?>