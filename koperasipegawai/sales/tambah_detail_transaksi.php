<?php
include 'koneksi.php';

if (!isset($_GET['id_sales'])) {
    header("Location: sales.php");
    exit();
}
$id_sales = $koneksi->real_escape_string($_GET['id_sales']);

// Ambil data item untuk dropdown
$sql_items = "SELECT id_item, nama_item, uom, harga_jual FROM item ORDER BY nama_item ASC";
$result_items = $koneksi->query($sql_items);

if (isset($_POST['submit'])) {
    $id_item = $koneksi->real_escape_string($_POST['id_item']);
    $quantity = $koneksi->real_escape_string($_POST['quantity']);

    // Ambil harga_jual dari item yang dipilih
    $sql_get_price = "SELECT harga_jual FROM item WHERE id_item = '$id_item'";
    $result_get_price = $koneksi->query($sql_get_price);
    $item_price_data = $result_get_price->fetch_assoc();
    $price = $item_price_data['harga_jual'];
    $amount = $quantity * $price;

    $sql_tambah = "INSERT INTO transaction (id_sales, id_item, quantity, price, amount) VALUES ('$id_sales', '$id_item', '$quantity', '$price', '$amount')";

    if ($koneksi->query($sql_tambah) === TRUE) {
        header("Location: detail_sales.php?id_sales=$id_sales&status=detail_added");
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
    <title>Tambah Item Transaksi untuk Sales #<?php echo $id_sales; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // JavaScript untuk mengisi harga jual otomatis
        function updatePrice() {
            var itemId = document.getElementById('id_item').value;
            if (itemId) {
                // Lakukan AJAX request atau simpan harga di data-attribute option
                // Untuk contoh sederhana, kita akan membaca dari data-harga-jual
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
        <h2>Tambah Item Transaksi untuk Sales #<?php echo $id_sales; ?></h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="id_item" class="form-label">Pilih Item</label>
                <select class="form-select" id="id_item" name="id_item" onchange="updatePrice()" required>
                    <option value="">Pilih Item</option>
                    <?php while ($item = $result_items->fetch_assoc()): ?>
                        <option value="<?php echo $item['id_item']; ?>" data-harga-jual="<?php echo $item['harga_jual']; ?>">
                            <?php echo $item['nama_item'] . ' (' . $item['uom'] . ') - Rp' . number_format($item['harga_jual'], 0, ',', '.'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="price_display" class="form-label">Harga Satuan (Otomatis)</label>
                <input type="text" class="form-control" id="price_display" readonly disabled>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            <a href="detail_sales.php?id_sales=<?php echo $id_sales; ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $koneksi->close(); ?>