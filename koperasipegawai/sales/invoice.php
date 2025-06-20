<?php
session_start(); // Mulai sesi di awal
include '../koneksi.php'; // Path ini tetap jika koneksi.php ada di direktori induk

// Logika Hapus (tetap ada di backend, meskipun tombol dihilangkan dari frontend)
// Jika Anda ingin mengimplementasikan fungsi hapus untuk invoice, Anda harus berhati-hati
// karena invoice adalah catatan pembayaran/penjualan, menghapusnya bisa merusak integritas data.
// Biasanya, invoice hanya bisa dibatalkan (statusnya diubah menjadi 'cancelled' atau sejenisnya).
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_sales = $koneksi->real_escape_string($_GET['id']); // Asumsi id invoice sama dengan id_sales

    // Ini hanya contoh, disarankan untuk tidak menghapus invoice secara langsung
    // melainkan mengubah statusnya menjadi 'cancelled' atau 'voided'
    $sql_hapus = "DELETE FROM sales WHERE id_sales = '$id_sales'";
    // Anda mungkin perlu menghapus juga data transaksi terkait jika ada di tabel transaction atau transaction_detail
    // $koneksi->query("DELETE FROM transaction WHERE id_sales = '$id_sales'");

    if ($koneksi->query($sql_hapus) === TRUE) {
        header("Location: invoice.php?status=deleted");
        exit();
    } else {
        echo "Error: " . $sql_hapus . "<br>" . $koneksi->error;
    }
}

// Ambil data sales (yang akan kita tampilkan sebagai invoice)
// Join dengan customer untuk menampilkan nama customer
// UBAH BARIS INI UNTUK PENGURUTAN:
$sql_tampil = "SELECT s.id_sales, s.tgl_sales, s.do_number, s.status, c.nama_customer 
               FROM sales s 
               JOIN customer c ON s.id_customer = c.id_customer 
               ORDER BY s.tgl_sales DESC, s.id_sales DESC"; // Diurutkan berdasarkan tanggal sales terbaru, lalu ID sales terbaru
$result_tampil = $koneksi->query($sql_tampil);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Manajemen Invoice - Koperasi Pegawai</title>
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
                                <a class="nav-link" href="transaksi.php">Transaksi</a>
                                <a class="nav-link" href="invoice.php">Invoice</a>
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
                    <h1 class="mt-4">Data Invoice</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-file-invoice"></i> Data Invoice
                            <div class="float-end">
                                <a href="tambah_invoice.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Data</a>
                                <button class="btn btn-warning btn-sm"><i class="fas fa-cog"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                                <div class="alert alert-warning">Data invoice berhasil dihapus!</div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Sales No</th>
                                            <th>DO No</th>
                                            <th>Tgl Sales</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Sales No</th>
                                            <th>DO No</th>
                                            <th>Tgl Sales</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                        if ($result_tampil->num_rows > 0):
                                            $no = 1;
                                            while ($row = $result_tampil->fetch_assoc()):
                                        ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo $row['id_sales']; ?></td>
                                                    <td><?php echo $row['do_number']; ?></td>
                                                    <td><?php echo $row['tgl_sales']; ?></td>
                                                    <td><?php echo $row['nama_customer']; ?></td>
                                                    <td>
                                                        <?php
                                                        $status_class = '';
                                                        $display_status = $row['status'];

                                                        if (strtolower($row['status']) == 'paid') {
                                                            $status_class = 'bg-success';
                                                        } elseif (strtolower($row['status']) == 'cancel') {
                                                            $status_class = 'bg-danger';
                                                        } else {
                                                            $status_class = 'bg-warning';
                                                        }
                                                        echo '<span class="badge ' . $status_class . '">' . $display_status . '</span>';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a href="generate_pdf_invoice.php?id=<?php echo $row['id_sales']; ?>" class="btn btn-secondary btn-sm" title="Print Invoice (PDF)" target="_blank"><i class="fas fa-print"></i></a>
                                                        <a href="detail_invoice.php?id=<?php echo $row['id_sales']; ?>" class="btn btn-info btn-sm" title="View Detail Invoice"><i class="fas fa-file-alt"></i></a>
                                                    </td>
                                                </tr>
                                            <?php
                                            endwhile;
                                        else:
                                            ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada data invoice.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const datatablesSimple = document.getElementById('datatablesSimple');
            if (datatablesSimple) {
                new simpleDatatables.DataTable(datatablesSimple);
            }
        });
    </script>
</body>

</html>
<?php $koneksi->close(); ?>