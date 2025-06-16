<?php
session_start(); // Mulai sesi di awal
include '../koneksi.php'; // Path disesuaikan karena koneksi.php ada di luar folder items

// Ambil data untuk Tampil
$sql_tampil = "SELECT * FROM item";
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
    <title>Manajemen Item - Koperasi Pegawai</title>
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
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
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
                                <a class="nav-link" href="user.php">User Account</a>
                                <a class="nav-link" href="identity/identity.php">Identity</a>
                                <a class="nav-link" href="customer.php">Customer</a>
                                <a class="nav-link" href="items/items.php">Items</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Transaksi
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="/sales/transaksi.php">Transaksi</a>
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
                                <a class="nav-link" href="sales/sales.php">Sales</a>
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
                    <h1 class="mt-4">Manajemen Item</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Items</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-box"></i> Data Item
                        </div>
                        <div class="card-body">
                            <?php
                            // Pesan status (added, updated, deleted)
                            if (isset($_GET['status']) && $_GET['status'] == 'added'): ?>
                                <div class="alert alert-success">Data item berhasil ditambahkan!</div>
                            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
                                <div class="alert alert-success">Data item berhasil diperbarui!</div>
                            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                                <div class="alert alert-warning">Data item berhasil dihapus!</div>
                            <?php endif; ?>

                            <a href="tambah_item.php" class="btn btn-primary mb-3">Tambah Item</a>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>ID Item</th>
                                            <th>Nama Item</th>
                                            <th>UOM</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID Item</th>
                                            <th>Nama Item</th>
                                            <th>UOM</th>
                                            <th>Harga Beli</th>
                                            <th>Harga Jual</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php if ($result_tampil->num_rows > 0): ?>
                                            <?php while ($row = $result_tampil->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $row['id_item']; ?></td>
                                                    <td><?php echo $row['nama_item']; ?></td>
                                                    <td><?php echo $row['uom']; ?></td>
                                                    <td><?php echo number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                                                    <td><?php echo number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <a href="edit_item.php?id=<?php echo $row['id_item']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                        <a href="hapus_item.php?id=<?php echo $row['id_item']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus item ini?')">Hapus</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data item.</td>
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
        // Inisialisasi Simple-Datatables jika belum ada di scripts.js
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