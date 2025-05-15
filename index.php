<?php
require 'function.php';
require 'cek.php';

// Proses jika form ditambah barang disubmit
if (isset($_POST['tambahbarang'])) {
    $nama_barang = $_POST['nama_barang'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];

    // Panggil fungsi untuk menambah barang
    $tambahBarang = tambahBarang($nama_barang, $deskripsi, $stok);

    if ($tambahBarang) {
        echo "<script>alert('Barang berhasil ditambahkan!');</script>";
        header('Location: index.php');  // Redirect ke halaman yang sama untuk memperbarui data
        exit;
    } else {
        echo "<script>alert('Gagal menambahkan barang!');</script>";
    }
}

// Ambil data stok barang dari database
$barang = getBarang();
// Proses delete barang
if (isset($_GET['hapus'])) {
    $id_barang = $_GET['hapus'];
    if (hapusBarang($id_barang)) {
        echo "<script>alert('Barang berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus barang!'); window.location='index.php';</script>";
    }
}
if (isset($_POST['editbarang'])) {
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];

    $hasil = editBarang($id_barang, $nama_barang, $deskripsi, $stok);

    if ($hasil) {
        echo "<script>alert('Barang berhasil diedit!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengedit barang!');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Stok Barang</title>
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
                <h1 class="mt-4">Stok Barang</h1>

                <!-- Tombol Tambah Barang -->
                <button class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#myModal">Tambah Barang</button>

                <!-- Tabel Barang -->
                <div class="card-body table-responsive">
                    <table id="datatablesSimple" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Deskripsi</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($barang)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        <td><?= $row['stok']; ?></td>
                        
                        <td>

                            <!-- Edit Button -->
                            <a href="edit_barang.php?id_barang=<?= $row['id_barang']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <!-- Delete Button -->
                            <a href="index.php?hapus=<?= $row['id_barang']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">Delete</a>
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

<!-- Modal Tambah Barang -->
 <?php
// Tambahkan modal edit untuk setiap barang
mysqli_data_seek($barang, 0); // Reset pointer
while ($row = mysqli_fetch_assoc($barang)) {
?>
<div class="modal fade" id="editModal<?= $row['id_barang']; ?>" tabindex="-1" aria-labelledby="editLabel<?= $row['id_barang']; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="editLabel<?= $row['id_barang']; ?>">Edit Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST">
        <div class="modal-body">
          <input type="hidden" name="id_barang" value="<?= $row['id_barang']; ?>">
          <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" class="form-control" name="nama_barang" value="<?= htmlspecialchars($row['nama_barang']); ?>" required>
          </div>
          <div class="mb-3">
            <label>Deskripsi</label>
            <textarea class="form-control" name="deskripsi" required><?= htmlspecialchars($row['deskripsi']); ?></textarea>
          </div>
          <div class="mb-3">
            <label>Stok</label>
            <input type="number" class="form-control" name="stok" value="<?= $row['stok']; ?>" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="editbarang" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>

    </div>
  </div>
</div>
<?php } ?>

<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Tambah Barang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <div class="modal-body">
            <form id="formBarang" method="post">
              <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
              </div>
              <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
              </div>
              <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" required>
              </div>
              <input type="hidden" name="tambahbarang" value="1">
          </div>
          <div class="modal-footer">
            <button type="submit" form="formBarang" class="btn btn-primary">Simpan</button>
          </div>
          </form>

        </div>
    </div>
</div>

</body>
</html>
