<?php
session_start(); // Pastikan sesi dimulai di awal
include '../koneksi.php'; // Include file koneksi database

// Fungsi untuk menghasilkan nomor invoice otomatis (INV000X)
function generateNextInvoiceNumber($koneksi)
{
    // Query untuk mendapatkan ID sales terakhir
    $sql_last_id = "SELECT id_sales FROM sales ORDER BY id_sales DESC LIMIT 1";
    $result_last_id = $koneksi->query($sql_last_id);

    $last_id = null;
    if ($result_last_id && $result_last_id->num_rows > 0) {
        $row = $result_last_id->fetch_assoc();
        $last_id = $row['id_sales'];
    }

    if ($last_id) {
        // Ekstrak bagian angka dari ID terakhir (misal dari "INV0001" menjadi "0001")
        $numeric_part = (int) substr($last_id, 3);
        $numeric_part++; // Tambahkan 1
        // Format kembali menjadi string dengan 4 digit, tambahkan nol di depan jika perlu
        $next_numeric_part = str_pad($numeric_part, 4, '0', STR_PAD_LEFT);
        $next_id_sales = "INV" . $next_numeric_part;
    } else {
        // Jika belum ada data sales sama sekali, mulai dari INV0001
        $next_id_sales = "INV0001";
    }

    return $next_id_sales;
}

// Ambil data customer untuk dropdown
$sql_customers = "SELECT id_customer, nama_customer FROM customer ORDER BY nama_customer ASC";
$result_customers = $koneksi->query($sql_customers);

// Proses form jika disubmit
if (isset($_POST['submit'])) {
    // Tanggal Sales
    $tgl_sales = $koneksi->real_escape_string($_POST['tgl_sales']);
    // ID Customer
    $id_customer = $koneksi->real_escape_string($_POST['id_customer']);
    // Nomor DO
    $do_number = $koneksi->real_escape_string($_POST['do_number']);
    // Status
    $status = $koneksi->real_escape_string($_POST['status']);

    // --- Panggil fungsi untuk menghasilkan ID Sales baru ---
    $new_id_sales = generateNextInvoiceNumber($koneksi); // Panggil fungsi di sini
    // --------------------------------------------------------

    // Perintah SQL untuk menyimpan data sales
    // Pastikan untuk MENYERTAKAN kolom 'id_sales' dan nilainya '$new_id_sales'
    $sql_tambah = "INSERT INTO sales (id_sales, tgl_sales, id_customer, do_number, status) 
                   VALUES ('$new_id_sales', '$tgl_sales', '$id_customer', '$do_number', '$status')";

    if ($koneksi->query($sql_tambah) === TRUE) {
        // Jika berhasil, alihkan ke halaman sales.php atau invoice.php (sesuaikan)
        header("Location: sales.php?status=added");
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
    <title>Tambah Sales Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Tambah Sales Baru</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="tgl_sales" class="form-label">Tanggal Sales</label>
                <input type="date" class="form-control" id="tgl_sales" name="tgl_sales" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_customer" class="form-label">Customer</label>
                <select class="form-select" id="id_customer" name="id_customer" required>
                    <option value="">Pilih Customer</option>
                    <?php while ($customer = $result_customers->fetch_assoc()): ?>
                        <option value="<?php echo $customer['id_customer']; ?>"><?php echo $customer['nama_customer']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="do_number" class="form-label">Nomor DO</label>
                <input type="text" class="form-control" id="do_number" name="do_number">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                    <option value="Canceled">Canceled</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            <a href="sales.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php $koneksi->close(); ?>