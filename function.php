<?php
// Cegah error jika session belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi koneksi database
function getKoneksi() {
    $conn = mysqli_connect("localhost", "root", "", "portofolio_stok_barang");
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
    return $conn;
}

// Fungsi menjalankan query
function runQuery($query) {
    $conn = getKoneksi();
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query gagal: " . mysqli_error($conn));
    }
    return $result;
}

// Ambil semua barang
function getBarang() {
    return runQuery("SELECT * FROM stok");
}

// Ambil data barang masuk
function getBarangMasuk() {
    return runQuery("SELECT * FROM masuk");
}

// Ambil data barang keluar
function getBarangKeluar() {
    return runQuery("SELECT * FROM keluar");
}

// Proses Barang Masuk
function tambahBarangMasuk($id_barang, $quantity, $keterangan) {
    $conn = getKoneksi();

    // Ambil stok saat ini
    $stokSekarangQuery = mysqli_query($conn, "SELECT stok FROM stok WHERE id_barang = '$id_barang'");
    if ($stokSekarangQuery) {
        $stokSekarang = mysqli_fetch_assoc($stokSekarangQuery)['stok'];

        // Simpan data ke tabel masuk
        $insert = mysqli_query($conn, "INSERT INTO masuk (id_barang, tanggal, quantity, keterangan) 
                                       VALUES ('$id_barang', NOW(), '$quantity', '$keterangan')");

        // Update stok
        if ($insert) {
            $updateStok = mysqli_query($conn, "UPDATE stok SET stok = stok + $quantity WHERE id_barang = '$id_barang'");
            if ($updateStok) {
                header('Location: masuk.php');
                exit;
            } else {
                echo "Gagal memperbarui stok.";
            }
        } else {
            echo "Gagal menambahkan barang masuk.";
        }
    } else {
        echo "Gagal mendapatkan stok barang.";
    }
}

// Proses Barang Keluar (✅ diperbaiki: tambahkan quantity)
function prosesBarangKeluar($id_barang, $quantity, $penerima) {
    $conn = getKoneksi();

    // Ambil stok saat ini
    $stokSekarangQuery = mysqli_query($conn, "SELECT stok FROM stok WHERE id_barang = '$id_barang'");
    if ($stokSekarangQuery) {
        $stokSekarang = mysqli_fetch_assoc($stokSekarangQuery)['stok'];

        if ($stokSekarang >= $quantity) {
            // Simpan data ke tabel keluar (✅ tambahkan quantity)
            $insert = mysqli_query($conn, "INSERT INTO keluar (id_barang, tanggal, quantity, penerima) 
                                           VALUES ('$id_barang', NOW(), '$quantity', '$penerima')");

            // Update stok
            $update = mysqli_query($conn, "UPDATE stok SET stok = stok - $quantity WHERE id_barang = '$id_barang'");

            if ($insert && $update) {
                header('Location: keluar.php');
                exit;
            } else {
                echo "Gagal menambahkan barang keluar.";
            }
        } else {
            echo "Stok tidak mencukupi.";
        }
    } else {
        echo "Gagal mendapatkan stok barang.";
    }
}

// Fungsi untuk menambah barang ke database
function tambahBarang($nama_barang, $deskripsi, $stok) {
    $conn = getKoneksi();

    // Gunakan prepared statement
    $query = "INSERT INTO stok (nama_barang, deskripsi, stok) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $nama_barang, $deskripsi, $stok);

    return mysqli_stmt_execute($stmt);
}
function hapusBarangMasuk($id_masuk, $id_barang, $quantity) {
    $conn = getKoneksi();

    // Hapus data dari tabel masuk
    $query = "DELETE FROM masuk WHERE id_masuk = '$id_masuk'";
    if (mysqli_query($conn, $query)) {
        // Update stok
        $updateStok = "UPDATE stok SET stok = stok - $quantity WHERE id_barang = '$id_barang'";
        mysqli_query($conn, $updateStok);

        echo "<script>alert('Data berhasil dihapus.'); window.location='masuk.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
// Fungsi untuk menghapus barang
function hapusBarang($id_barang) {
    $conn = getKoneksi();
    $query = "DELETE FROM stok WHERE id_barang = '$id_barang'";

    if (mysqli_query($conn, $query)) {
        return true;
    } else {
        return false;
    }
}

// Fungsi untuk mengedit barang
function editBarang($id_barang, $nama_barang, $deskripsi, $stok) {
    $conn = getKoneksi();
    $query = "UPDATE stok SET nama_barang = '$nama_barang', deskripsi = '$deskripsi', stok = '$stok' WHERE id_barang = '$id_barang'";

    if (mysqli_query($conn, $query)) {
        return true;
    } else {
        return false;
    }
}

?>
