<?php
require 'function.php';
$conn = getKoneksi();
$barang = getBarang();

// Proses barang keluar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['barangkeluar'])) {
    $id_barang = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $quantity = (int) $_POST['quantity'];

    // Validasi jumlah
    if ($quantity > 0) {
        // Ambil stok sekarang
        $cekStok = mysqli_query($conn, "SELECT stok FROM stok WHERE id_barang = '$id_barang'");
        $stokSekarang = mysqli_fetch_assoc($cekStok)['stok'];

        if ($stokSekarang >= $quantity) {
            // Simpan ke tabel keluar (dengan quantity)
            $insert = mysqli_query($conn, "INSERT INTO keluar (id_barang, tanggal, quantity, penerima) 
                                           VALUES ('$id_barang', NOW(), '$quantity', '$penerima')");

            if ($insert) {
                // Kurangi stok
                mysqli_query($conn, "UPDATE stok SET stok = stok - $quantity WHERE id_barang = '$id_barang'");
                header("Location: keluar.php");
                exit;
            } else {
                echo "<script>alert('Gagal menyimpan data');</script>";
            }
        } else {
            echo "<script>alert('Stok tidak mencukupi');</script>";
        }
    } else {
        echo "<script>alert('Jumlah harus lebih dari 0');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Barang Keluar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <style>
    body {
        background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        background-color: white;
        border-radius: 20px;
    }

    .card-header h3 {
        color: #6a11cb;
        font-weight: bold;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    .form-control {
        border-radius: 10px;
    }
</style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<!-- Navbar -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Tegal Agung</a>
</nav>

<!-- Sidebar + Konten -->
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link" href="index.php"><i class="fas fa-boxes"></i> Stok Barang</a>
                    <a class="nav-link" href="masuk.php"><i class="fas fa-arrow-down"></i> Barang Masuk</a>
                    <a class="nav-link" href="keluar.php"><i class="fas fa-arrow-up"></i> Barang Keluar</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </nav>
    </div>
<!-- Konten Utama -->
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Barang Keluar</h1>
<body>
    
   <!-- Tombol Tambah Barang -->
<button class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#modalKeluar">Tambah Barang Keluar</button>


    <!-- Tabel BarangKeluar -->
    <div class="card-body table-responsive">
                    <table id="datatablesSimple" class="table table-bordered">
               <thead>
                            <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Quantity</th>
                    <th>Penerima</th>
                </tr>
            </thead>
                        <tbody>
                <?php
                $no = 1;
                $data = mysqli_query($conn, "SELECT k.*, s.nama_barang FROM keluar k JOIN stok s ON k.id_barang = s.id_barang ORDER BY k.tanggal DESC");
                while ($row = mysqli_fetch_assoc($data)) {
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td class="text-center"><?= $row['quantity'] ?></td>
                        <td><?= htmlspecialchars($row['penerima']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Barang Keluar -->
<div class="modal fade" id="modalKeluar" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Tambah Barang Keluar</h5>
        <a href="export_barang.php" class="btn btn-success my-3"><i class="fas fa-file-excel"></i> Export Data</a>

        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="barangnya" class="form-label">Pilih Barang</label>
            <select class="form-select" name="barangnya" required>
                <option disabled selected>-- Pilih Barang --</option>
                <?php foreach ($barang as $b) { ?>
                    <option value="<?= $b['id_barang'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Jumlah</label>
            <input type="number" class="form-control" name="quantity" min="1" required>
        </div>
        <div class="mb-3">
            <label for="penerima" class="form-label">Penerima</label>
            <input type="text" class="form-control" name="penerima" placeholder="Masukkan nama penerima" required>
        </div>
        <input type="hidden" name="barangkeluar" value="1">
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>
</body>
</html>
