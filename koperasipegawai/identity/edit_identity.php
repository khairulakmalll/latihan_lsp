<?php
session_start();
include '../koneksi.php'; // Sesuaikan path jika perlu

$identitas_data = null;
$message = '';

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id_to_edit = $koneksi->real_escape_string($_GET['id']);
    $sql_get_edit = "SELECT * FROM identitas WHERE id_identitas = '$id_to_edit'";
    $result_get_edit = $koneksi->query($sql_get_edit);

    if ($result_get_edit && $result_get_edit->num_rows > 0) {
        $identitas_data = $result_get_edit->fetch_assoc();
    } else {
        $message = "Data identitas tidak ditemukan.";
    }
} else {
    $message = "ID identitas tidak diberikan.";
}

// Proses form submission untuk update
if ($_SERVER["REQUEST_METHOD"] == "POST" && $identitas_data) {
    // Ambil data dari form dan sanitasi
    $id_identitas = $koneksi->real_escape_string($_POST['id_identitas']); // Hidden input
    $nama_identitas = $koneksi->real_escape_string($_POST['nama_identitas']);
    $badan_hukum = $koneksi->real_escape_string($_POST['badan_hukum']); // Ambil nilai badan_hukum
    $npwp = $koneksi->real_escape_string($_POST['npwp']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $url = $koneksi->real_escape_string($_POST['url']);
    $alamat = $koneksi->real_escape_string($_POST['alamat']);
    $telp = $koneksi->real_escape_string($_POST['telp']);
    $fax = $koneksi->real_escape_string($_POST['fax']);
    $rekening = $koneksi->real_escape_string($_POST['rekening']);

    $foto_path = $identitas_data['foto']; // Default ke foto lama
    // Penanganan upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../uploads/identity/"; // Pastikan folder ini ada dan writable
        $file_name = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $uploadOk = 1;
        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check !== false) { /* File is an image. */
        } else {
            $message .= "File bukan gambar.<br>";
            $uploadOk = 0;
        }
        if ($_FILES["foto"]["size"] > 500000) {
            $message .= "Ukuran file terlalu besar, maksimal 500KB.<br>";
            $uploadOk = 0;
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $message .= "Hanya format JPG, JPEG, PNG & GIF yang diizinkan.<br>";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                // Hapus foto lama jika ada dan berbeda dari yang baru
                if (!empty($identitas_data['foto']) && file_exists($target_dir . $identitas_data['foto']) && $identitas_data['foto'] != $file_name) {
                    unlink($target_dir . $identitas_data['foto']);
                }
                $foto_path = $file_name; // Update path foto baru
            } else {
                $message .= "Terjadi kesalahan saat mengupload file.<br>";
            }
        }
    }

    if (empty($message)) { // Jika tidak ada pesan error dari validasi upload
        // Query UPDATE
        $sql_update = "UPDATE identitas SET 
            nama_identitas = '$nama_identitas',
            badan_hukum = '$badan_hukum', -- Update kolom badan_hukum
            npwp = '$npwp',
            email = '$email',
            url = '$url',
            alamat = '$alamat',
            telp = '$telp',
            fax = '$fax',
            rekening = '$rekening',
            foto = '$foto_path'
            WHERE id_identitas = '$id_identitas'";

        if ($koneksi->query($sql_update) === TRUE) {
            header("Location: identity.php?status=updated");
            exit();
        } else {
            $message = "Error: " . $sql_update . "<br>" . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Identitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Utama</div>
                        <a class="nav-link" href="../index.php">
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
                                <a class="nav-link" href="../user.php">User Account</a>
                                <a class="nav-link active" href="identity.php">Identity</a>
                                <a class="nav-link" href="../customer.php">Customer</a>
                                <a class="nav-link" href="../items.php">Items</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Transaksi
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="sales.php">Sales</a>
                                <a class="nav-link" href="invoice.php">Invoice</a>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php
                    if (isset($_SESSION['user'])) {
                        $nama = $_SESSION['user']['nama_user'];
                        $level = $_SESSION['user']['level'] == 1 ? 'Petugas' : 'Manager';
                        echo "<div class='text-white'>$nama ($level)</div>";
                    } else {
                        echo "<div class='text-white'>Guest</div>";
                    }
                    ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Edit Identitas</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="identity.php">Identity</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>

                    <?php if ($message): ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <?php if ($identitas_data): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <i class="fas fa-edit"></i> Form Edit Identitas
                            </div>
                            <div class="card-body">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id_identitas" value="<?php echo htmlspecialchars($identitas_data['id_identitas']); ?>">

                                    <div class="mb-3">
                                        <label for="nama_identitas" class="form-label">Nama Perusahaan</label>
                                        <input type="text" class="form-control" id="nama_identitas" name="nama_identitas" value="<?php echo htmlspecialchars($identitas_data['nama_identitas'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="badan_hukum" class="form-label">Badan Hukum</label>
                                        <input type="text" class="form-control" id="badan_hukum" name="badan_hukum" value="<?php echo htmlspecialchars($identitas_data['badan_hukum'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="npwp" class="form-label">NPWP</label>
                                        <input type="text" class="form-control" id="npwp" name="npwp" value="<?php echo htmlspecialchars($identitas_data['npwp'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($identitas_data['email'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="url" class="form-label">URL Website</label>
                                        <input type="url" class="form-control" id="url" name="url" value="<?php echo htmlspecialchars($identitas_data['url'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="alamat" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($identitas_data['alamat'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="telp" class="form-label">Telepon</label>
                                        <input type="tel" class="form-control" id="telp" name="telp" value="<?php echo htmlspecialchars($identitas_data['telp'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fax" class="form-label">Fax</label>
                                        <input type="tel" class="form-control" id="fax" name="fax" value="<?php echo htmlspecialchars($identitas_data['fax'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="rekening" class="form-label">Rekening Bank</label>
                                        <input type="text" class="form-control" id="rekening" name="rekening" value="<?php echo htmlspecialchars($identitas_data['rekening'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="foto" class="form-label">Logo/Foto Perusahaan</label>
                                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                        <?php if ($identitas_data && $identitas_data['foto']): ?>
                                            <div class="mt-2">
                                                <img src="../uploads/identity/<?php echo htmlspecialchars($identitas_data['foto']); ?>" alt="Logo Perusahaan" style="max-width: 150px; height: auto;">
                                                <small class="text-muted d-block">Logo saat ini</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="identity.php" class="btn btn-secondary">Batal</a>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Data identitas tidak ditemukan atau terjadi kesalahan. Kembali ke <a href="identity.php">halaman Identitas</a>.
                        </div>
                    <?php endif; ?>
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
    <script src="../js/scripts.js"></script>
</body>

</html>
<?php $koneksi->close(); ?>