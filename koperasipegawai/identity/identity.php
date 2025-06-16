<?php
session_start();
include '../koneksi.php'; // Sesuaikan path jika perlu

// Logika Hapus Data
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_identitas_to_delete = $koneksi->real_escape_string($_GET['id']);

    // Ambil nama file foto sebelum dihapus dari database
    $sql_get_foto = "SELECT foto FROM identitas WHERE id_identitas = '$id_identitas_to_delete'";
    $result_get_foto = $koneksi->query($sql_get_foto);
    $foto_data = null;
    if ($result_get_foto && $result_get_foto->num_rows > 0) {
        $foto_data = $result_get_foto->fetch_assoc();
    }

    $sql_hapus = "DELETE FROM identitas WHERE id_identitas = '$id_identitas_to_delete'";

    if ($koneksi->query($sql_hapus) === TRUE) {
        // Hapus file foto dari server jika ada
        if ($foto_data && !empty($foto_data['foto'])) {
            $foto_path = "../uploads/identity/" . $foto_data['foto'];
            if (file_exists($foto_path)) {
                unlink($foto_path);
            }
        }
        header("Location: identity.php?status=deleted");
        exit();
    } else {
        $message = "Error: " . $sql_hapus . "<br>" . $koneksi->error;
    }
}

// Ambil semua data identitas untuk ditampilkan
$sql_tampil = "SELECT * FROM identitas ORDER BY nama_identitas ASC";
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
    <title>Manajemen Identitas - Koperasi Pegawai</title>
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
                                <a class="nav-link" href="identity/identity.php">Identity</a>
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
                                <a class="nav-link" href="/sales/sales.php">Sales</a>
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
                    <h1 class="mt-4">Data Identity</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Identity</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-building"></i> Data Identitas
                            <div class="float-end">
                                <a href="tambah_identity.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Data</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['status'])): ?>
                                <?php if ($_GET['status'] == 'added'): ?>
                                    <div class="alert alert-success">Data identitas berhasil ditambahkan!</div>
                                <?php elseif ($_GET['status'] == 'updated'): ?>
                                    <div class="alert alert-info">Data identitas berhasil diupdate!</div>
                                <?php elseif ($_GET['status'] == 'deleted'): ?>
                                    <div class="alert alert-warning">Data identitas berhasil dihapus!</div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (isset($message)): ?>
                                <div class="alert alert-danger"><?php echo $message; ?></div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Identitas</th>
                                            <th>Badan Hukum</th>
                                            <th>NPWP</th>
                                            <th>Email</th>
                                            <th>URL</th>
                                            <th>Alamat</th>
                                            <th>Telepon</th>
                                            <th>Fax</th>
                                            <th>Rekening</th>
                                            <th>Foto</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_tampil->num_rows > 0):
                                            $no = 1;
                                            while ($row = $result_tampil->fetch_assoc()):
                                        ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_identitas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['badan_hukum'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['npwp'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['url'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['alamat'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['telp'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['fax'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['rekening'] ?? '-'); ?></td>
                                                    <td>
                                                        <?php if (!empty($row['foto'])): ?>
                                                            <img src="../uploads/identity/<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto" style="max-width: 80px; height: auto;">
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit_identity.php?id=<?php echo $row['id_identitas']; ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                                        <a href="identity.php?action=hapus&id=<?php echo $row['id_identitas']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus data ini?');"><i class="fas fa-trash-alt"></i></a>
                                                    </td>
                                                </tr>
                                            <?php
                                            endwhile;
                                        else:
                                            ?>
                                            <tr>
                                                <td colspan="12" class="text-center">Tidak ada data identitas.</td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="../js/scripts.js"></script>
    <script>
        // Inisialisasi Simple-Datatables
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