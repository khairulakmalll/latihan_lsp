<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $level = $_POST['level'];

    if ($level == 1) {
        // Simpan ke tabel petugas
        $query = $koneksi->prepare("INSERT INTO petugas (nama_user, username, password, level) VALUES (?, ?, ?, ?)");
    } elseif ($level == 2) {
        // Simpan ke tabel manager
        $query = $koneksi->prepare("INSERT INTO manager (nama_user, username, password, level) VALUES (?, ?, ?, ?)");
    } else {
        $error = "Level tidak valid.";
    }

    if (isset($query)) {
        $query->bind_param("sssi", $nama, $username, $password, $level);
        if ($query->execute()) {
            header("Location: login.php?success=1");
            exit;
        } else {
            $error = "Registrasi gagal: " . $koneksi->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h3>Register</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Level</label>
            <select name="level" class="form-select" required>
                <option value="">-- Pilih Level --</option>
                <option value="1">Petugas</option>
                <option value="2">Manager</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Daftar</button>
        <a href="login.php" class="btn btn-link">Sudah punya akun? Login</a>
    </form>
</body>

</html>