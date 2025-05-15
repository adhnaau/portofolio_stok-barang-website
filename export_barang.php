<?php
require 'function.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=stok_barang.xls");

$data = getBarang();
?>

<h3>Data Stok Barang</h3>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Deskripsi</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($data)) {
            echo "<tr>";
            echo "<td>{$no}</td>";
            echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
            echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
            echo "<td>{$row['stok']}</td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </tbody>
</table>
