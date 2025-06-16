<?php
include 'koneksi.php';

if (!isset($_GET['id']) || !isset($_GET['id_sales'])) {
    header("Location: sales.php");
    exit();
}

$id_transaction = $koneksi->real_escape_string($_GET['id']);
$id_sales_parent = $koneksi->real_escape_string($_GET['id_sales']);

// Ambil data transaksi yang akan diedit
$sql_ambil_data = "SELECT t.*, i.nama_item, i.uom FROM transaction t JOIN item i ON t.id_item = i.id_item WHERE t.id_transaction = '$id_transaction' AND t.id_sales = '$id_sales_parent'";
$result_ambil_data = $koneksi->query($sql_ambil_data);

if ($result_ambil_data->num_rows == 0) {
    echo "Detail transaksi tidak ditemukan!";
    exit();
}
$data_transaksi = $result_ambil_data->fetch_assoc();

// Ambil data item untuk dropdown (termasuk item yang saat ini dipilih)
$sql_items = "SELECT id_item, nama_item, uom, harga_jual FROM item ORDER BY nama_item ASC";
$result_items = $koneksi->query($sql_items);

if (isset($_POST['submit'])) {
    $id_item_new = $koneksi->real_escape_string($_POST['id_item']);
    $quantity_new = $koneksi->real_escape_string($_POST['quantity']);

    // Ambil harga_jual terbaru dari item yang dipilih
    $sql_get_price_new = "SELECT harga_jual FROM item WHERE id_item = '$id_item_new'";
    $result_get_price_new = $koneksi->query($sql_get_price_new);
    $item_price_data_new = $result_get_price_new->fetch_assoc();
    $price_new = $item_price_data_new['harga_jual'];
    $amount_new = $quantity_new * $price_new;

    $sql_update = "UPDATE transaction SET
                    id_item = '$id_item_new',
                    quantity = '$quantity_new',
                    price = '$price_new',
                    amount = '$amount_new'
                    WHERE id_transaction = '$id_transaction' AND id_sales = '$id_sales_parent'";

    if ($koneksi->query($sql_update) === TRUE) {
        header("Location: detail_sales.php?id_sales=$id_sales_parent&status=detail_updated");
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
    <title>Edit Item Transaksi #<?php echo $id_transaction; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function updatePrice() {
            var itemId = document.getElementById('id_item').value;
            if (itemId) {
                var selectedOption = document.querySelector('#id_item option[value="' + itemId + '"]');
                var hargaJual = selectedOption.getAttribute('data-harga-jual');
                document.getElementById('price_display').value = parseFloat(hargaJual).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            } else {
                document.getElementById('price_display').value = '';
            }
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Item Transaksi untuk Sales #<?php echo $id_sales_parent; ?></h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="id_item" class="form-label">Pilih Item</label>
                <select class="form-select" id="id_item" name="id_item" onchange="updatePrice()" required>
                    <option value="">Pilih Item</option>
                    <?php while ($item = $result_items->fetch_assoc()): ?>
                        <option value="<?php echo $item['id_item']; ?>" data-harga-jual="<?php echo $item['harga_jual']; ?>" <?php echo ($item['id_item'] == $data_transaksi['id_item']) ? 'selected' : ''; ?>>
                            <?php echo $item['nama_item'] . ' (' . $item['uom'] . ') - Rp' . number_format($item['harga_jual'], 0, ',', '.'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="price_display" class="form-label">Harga Satuan (Otomatis)</label>
                <input type="text" class="form-control" id="price_display" value="<?php echo number_format($data_transaksi['price'], 0, ',', '.'); ?>" readonly disabled>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="<?php echo $data_transaksi['quantity']; ?>" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update</button>
            <a href="detail_sales.php?id_sales=<?php echo $id_sales_parent; ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Panggil updatePrice saat halaman dimuat untuk mengisi harga awal
        window.onload = updatePrice;
    </script>
</body>

</html>
<?php $koneksi->close(); ?>