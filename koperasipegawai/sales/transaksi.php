<?php
session_start();
include '../koneksi.php'; // Sesuaikan path jika perlu

$message = '';
$errors = [];

// Fungsi untuk menghasilkan nomor transaksi otomatis (TRX000X)
function generateNextTransactionNumber($koneksi)
{
    $sql_last_id = "SELECT id_transaction FROM transaction ORDER BY id_transaction DESC LIMIT 1";
    $result_last_id = $koneksi->query($sql_last_id);

    $last_id = null;
    if ($result_last_id && $result_last_id->num_rows > 0) {
        $row = $result_last_id->fetch_assoc();
        $last_id = $row['id_transaction'];
    }

    if ($last_id) {
        $numeric_part = (int) substr($last_id, 3);
        $numeric_part++;
        $next_numeric_part = str_pad($numeric_part, 4, '0', STR_PAD_LEFT);
        $next_id_transaction = "TRX" . $next_numeric_part;
    } else {
        $next_id_transaction = "TRX0001";
    }
    return $next_id_transaction;
}

// Ambil semua Sales ID dan nama Customer untuk dropdown
$sales_options = [];
$sql_sales_options = "SELECT s.id_sales, c.nama_customer, s.tgl_sales 
                      FROM sales s JOIN customer c ON s.id_customer = c.id_customer 
                      ORDER BY s.tgl_sales DESC";
$result_sales_options = $koneksi->query($sql_sales_options);
if ($result_sales_options->num_rows > 0) {
    while ($row = $result_sales_options->fetch_assoc()) {
        $sales_options[] = $row;
    }
}

// Ambil semua item dari tabel 'item' untuk dropdown
$sql_items = "SELECT id_item, nama_item, harga_jual FROM item ORDER BY nama_item ASC";
$result_items = $koneksi->query($sql_items);

// Logika Tambah Transaksi
if (isset($_POST['add_transaction'])) {
    $id_sales = $koneksi->real_escape_string($_POST['id_sales']);
    $id_item = $koneksi->real_escape_string($_POST['id_item']);
    $quantity = (int)$koneksi->real_escape_string($_POST['quantity']);

    if (empty($id_sales)) {
        $errors[] = "Sales ID tidak boleh kosong.";
    }
    if (empty($id_item)) {
        $errors[] = "Item tidak boleh kosong.";
    }
    if ($quantity <= 0) {
        $errors[] = "Quantity harus lebih dari 0.";
    }

    if (empty($errors)) {
        $new_id_transaction = generateNextTransactionNumber($koneksi);

        // Ambil harga jual dari tabel item berdasarkan id_item yang dipilih
        $price = 0;
        $sql_get_item_price = "SELECT harga_jual FROM item WHERE id_item = '$id_item'";
        $result_item_price = $koneksi->query($sql_get_item_price);
        if ($result_item_price && $result_item_price->num_rows > 0) {
            $item_row = $result_item_price->fetch_assoc();
            $price = (float)$item_row['harga_jual'];
        } else {
            $errors[] = "Item yang dipilih tidak valid atau harga jual tidak ditemukan.";
        }

        if (empty($errors)) {
            $amount = $quantity * $price; // Hitung amount

            $sql_insert_transaction = "INSERT INTO transaction (id_transaction, id_sales, id_item, quantity, price, amount) 
                                         VALUES ('$new_id_transaction', '$id_sales', '$id_item', '$quantity', '$price', '$amount')";

            if ($koneksi->query($sql_insert_transaction) === TRUE) {
                $message = "Transaksi berhasil ditambahkan!";
                // Opsional: Redirect ke halaman transaksi.php detail sales
                // header("Location: transaksi.php?id_sales=$id_sales&status=added");
                // exit();
            } else {
                $message = "Error saat menambahkan transaksi: " . $koneksi->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Transaksi Baru - Koperasi Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="../index.php">Koperasi Pegawai</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user fa-fw"></i> <?= $_SESSION['user']['nama_user'] ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <!-- NAVBAR -->
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Utama</div>
                        <a class="nav-link" href="/index.html">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Menu</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Master
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="/user.php">User Account</a>
                                <a class="nav-link" href="/identity/identity.php">Identity</a>
                                <a class="nav-link" href="/customer.php">Customer</a>
                                <a class="nav-link" href="/items/items.php">Items</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Transaksi
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="sales/transaksi.php">Transaksi</a>
                                <a class="nav-link" href="/sales/invoice.php">Invoice</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLaporan" aria-expanded="false" aria-controls="collapseLaporan">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Laporan
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLaporan" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="sales.php">Sales</a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Menampilkan Nama User yg Login -->
                <div class="small">Logged in as:</div>
                <?php
                if (isset($_SESSION['user'])) {
                    $nama = $_SESSION['user']['nama_user'];
                    $level = $_SESSION['user']['level'] == 1 ? 'Petugas' : 'Manager';
                    echo "$nama ($level)";
                } else {
                    echo "Guest";
                }
                ?>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Transaksi Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="sales.php">Sales</a></li>
                        <li class="breadcrumb-item active">Tambah Transaksi</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-plus"></i> Form Transaksi Baru
                        </div>
                        <div class="card-body">
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-info"><?php echo $message; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <?php foreach ($errors as $error): echo $error . "<br>";
                                    endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <form action="tambah_transaksi.php" method="POST">
                                <div class="mb-3">
                                    <label for="id_sales" class="form-label">Sales ID</label>
                                    <select class="form-select" id="id_sales" name="id_sales" required>
                                        <option value="">Pilih Sales ID</option>
                                        <?php foreach ($sales_options as $sales): ?>
                                            <option value="<?php echo htmlspecialchars($sales['id_sales']); ?>">
                                                <?php echo htmlspecialchars($sales['id_sales'] . ' - ' . $sales['nama_customer'] . ' (' . $sales['tgl_sales'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="id_item" class="form-label">Item</label>
                                    <select class="form-select" id="id_item" name="id_item" required onchange="getItemPrice(this.value, 'price')">
                                        <option value="">Pilih Item</option>
                                        <?php
                                        // Reset pointer result_items
                                        $result_items->data_seek(0);
                                        while ($item = $result_items->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo htmlspecialchars($item['id_item']); ?>" data-harga_jual="<?php echo htmlspecialchars($item['harga_jual']); ?>">
                                                <?php echo htmlspecialchars($item['nama_item']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required oninput="calculateAmount('quantity', 'price', 'amount')">
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price (Harga Jual)</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required readonly>
                                </div>
                                <button type="submit" name="add_transaction" class="btn btn-success">Simpan Transaksi</button>
                                <a href="sales.php" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script>
        // Fungsi untuk mengambil harga jual item dan mengisi input price
        function getItemPrice(itemId, priceInputId) {
            const selectElement = document.getElementById('id_item');
            const selectedOption = selectElement.querySelector(`option[value="${itemId}"]`);
            const priceInput = document.getElementById(priceInputId);
            const quantityInput = document.getElementById('quantity');
            const amountInput = document.getElementById('amount');

            if (selectedOption) {
                const hargaJual = selectedOption.dataset.harga_jual;
                priceInput.value = parseFloat(hargaJual).toFixed(2);
            } else {
                priceInput.value = '0.00';
            }
            calculateAmount(quantityInput.id, priceInput.id, amountInput.id);
        }

        // Fungsi untuk menghitung amount (quantity * price)
        function calculateAmount(quantityInputId, priceInputId, amountInputId) {
            const quantity = parseFloat(document.getElementById(quantityInputId).value) || 0;
            const price = parseFloat(document.getElementById(priceInputId).value) || 0;
            const amount = quantity * price;
            document.getElementById(amountInputId).value = amount.toFixed(2);
        }

        // Inisialisasi awal saat halaman dimuat jika ada item default terpilih
        document.addEventListener('DOMContentLoaded', function() {
            const idItemSelect = document.getElementById('id_item');
            if (idItemSelect.value) { // Jika ada item yang terpilih secara default
                getItemPrice(idItemSelect.value, 'price');
            } else if (idItemSelect.options.length > 1) { // Jika ada opsi selain 'Pilih Item'
                // Otomatis pilih item pertama dan isi harganya
                idItemSelect.value = idItemSelect.options[1].value;
                getItemPrice(idItemSelect.value, 'price');
            }
        });
    </script>
</body>

</html>
<?php $koneksi->close(); ?>