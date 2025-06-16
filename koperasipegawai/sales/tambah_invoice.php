<?php
// Letakkan fungsi ini di bagian atas file tambah_invoice.php atau di file utility yang di-include
function generateNextInvoiceNumber($koneksi)
{
    // Query untuk mendapatkan ID sales terakhir
    // Kita asumsikan id_sales memiliki format 'INV' diikuti 4 digit angka
    $sql_last_id = "SELECT id_sales FROM sales ORDER BY id_sales DESC LIMIT 1";
    $result_last_id = $koneksi->query($sql_last_id);

    $last_id = null;
    if ($result_last_id && $result_last_id->num_rows > 0) {
        $row = $result_last_id->fetch_assoc();
        $last_id = $row['id_sales'];
    }

    if ($last_id) {
        // Ekstrak bagian angka dari ID terakhir (misal dari "INV0001" menjadi "0001")
        // substr($last_id, 3) akan mengambil string mulai dari karakter ke-4 (indeks 3)
        $numeric_part = (int) substr($last_id, 3);

        // Tambahkan 1 ke bagian angka
        $numeric_part++;

        // Format kembali menjadi string dengan 4 digit, tambahkan nol di depan jika perlu (misal: 1 menjadi "0001")
        $next_numeric_part = str_pad($numeric_part, 4, '0', STR_PAD_LEFT);

        // Gabungkan kembali dengan prefix "INV"
        $next_id_sales = "INV" . $next_numeric_part;
    } else {
        // Jika belum ada data sales sama sekali, mulai dari INV0001
        $next_id_sales = "INV0001";
    }

    return $next_id_sales;
}

// Cara penggunaan di tambah_invoice.php (contoh sederhana untuk ilustrasi):
/*
// Di dalam tambah_invoice.php, setelah meng-include koneksi.php
$new_id_sales = generateNextInvoiceNumber($koneksi);

// Kemudian, gunakan $new_id_sales ini saat Anda melakukan INSERT ke tabel `sales`
// Contoh:
// $sql_insert = "INSERT INTO sales (id_sales, tgl_sales, id_customer, do_number, status) 
//                VALUES ('$new_id_sales', '$tgl_sales', '$id_customer', '$do_number', '$status')";
// if ($koneksi->query($sql_insert) === TRUE) {
//     // Data berhasil disimpan
// } else {
//     // Error
// }
*/
