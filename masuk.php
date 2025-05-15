<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getKoneksi() {
    $conn = mysqli_connect("localhost", "root", "", "portofolio_stok_barang");
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
    return $conn;
}

function runQuery($query) {
    $conn = getKoneksi();
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query gagal: " . mysqli_error($conn));
    }
    return $result;
}

function tambahBarangMasuk($id_barang, $quantity, $keterangan) {
    $conn = getKoneksi();

    // Simpan data ke tabel masuk
    $query = "INSERT INTO masuk (id_barang, quantity, keterangan, tanggal) 
              VALUES ('$id_barang', '$quantity', '$keterangan', NOW())";

    if (mysqli_query($conn, $query)) {
        // Update jumlah stok barang
        $updateStok = "UPDATE stok SET stok = stok + $quantity WHERE id_barang = '$id_barang'";
        mysqli_query($conn, $updateStok);

        echo "<script>alert('Barang masuk berhasil ditambahkan.'); window.location='masuk.php';</script>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}

// Fungsi untuk menghapus barang masuk
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

function getBarang() {
    return runQuery("SELECT * FROM stok");
}

function getBarangMasuk() {
    return runQuery("SELECT masuk.*, stok.nama_barang FROM masuk JOIN stok ON masuk.id_barang = stok.id_barang ORDER BY masuk.tanggal DESC");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['barangmasuk'])) {
        $id_barang = $_POST['barangnya'];
        $keterangan = $_POST['keterangan'];
        $quantity = $_POST['quantity'];

        if (!empty($id_barang) && !empty($keterangan) && !empty($quantity)) {
            tambahBarangMasuk($id_barang, $quantity, $keterangan);
        } else {
            echo "<script>alert('Semua kolom harus diisi.');</script>";
        }
    }

    // Menangani request hapus barang masuk
    if (isset($_POST['hapus_id'])) {
        $id_masuk = $_POST['hapus_id'];
        $id_barang = $_POST['id_barang'];
        $quantity = $_POST['quantity'];
        hapusBarangMasuk($id_masuk, $id_barang, $quantity);
    }
}

$barangMasuk = getBarangMasuk();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Barang Masuk</title>
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
    body {
 
}

</style>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

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
                <h1 class="mt-4">Barang Masuk</h1>

                <!-- Tombol Tambah Barang -->
                <button class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#myModal">Tambah Barang Masuk</button>

                <!-- Tabel Barang Masuk -->
                <div class="card-body table-responsive">
                    <table id="datatablesSimple" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($barangMasuk)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                                <td><?= htmlspecialchars($row['quantity']); ?></td>
                                <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        <input type="hidden" name="hapus_id" value="<?= $row['id_masuk']; ?>">
                                        <input type="hidden" name="id_barang" value="<?= $row['id_barang']; ?>">
                                        <input type="hidden" name="quantity" value="<?= $row['quantity']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4 text-muted small d-flex justify-content-between">
                <div>&copy; Tegal Agung 2025</div>
                <div>
                    <a href="#">Privacy Policy</a>
                    &middot;
                    <a href="#">Terms &amp; Conditions</a>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>

<!-- Modal Tambah Barang Masuk -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Tambah Barang Masuk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <form action="masuk.php" method="post">
          <div class="mb-3">
            <label for="barangnya" class="form-label">Nama Barang</label>
            <select class="form-control" id="barangnya" name="barangnya" required>
              <option value="">Pilih Barang</option>
              <?php
              $barang = getBarang();
              while ($item = mysqli_fetch_assoc($barang)) {
                  echo "<option value='" . $item['id_barang'] . "'>" . htmlspecialchars($item['nama_barang']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
          </div>
          <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
          </div>
          <input type="hidden" name="barangmasuk" value="1">
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

</body>
</html>
