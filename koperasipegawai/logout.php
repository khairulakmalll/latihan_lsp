<?php
session_start();
session_destroy(); // Menghapus semua session
header("Location: index.php"); // Redirect ke index.php
exit();
