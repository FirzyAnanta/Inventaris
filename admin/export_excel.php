<?php
require '../config/koneksi.php';

// Header agar browser membaca ini sebagai file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Peminjaman.xls");

?>

<h3>Laporan Peminjaman Barang - SMK Telkom Lampung</h3>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Peminjam</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali (Rencana)</th>
            <th>Tgl Dikembalikan</th>
            <th>Status</th>
            <th>Kondisi Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        // Ambil semua data tanpa filter status
        $query = mysqli_query($conn, "SELECT p.*, u.nama_lengkap, b.nama_barang 
                                      FROM peminjaman p 
                                      JOIN users u ON p.id_user = u.id_user 
                                      JOIN barang b ON p.id_barang = b.id_barang 
                                      ORDER BY p.id_peminjaman DESC");
        
        while ($row = mysqli_fetch_assoc($query)) {
            $tgl_real = $row['tanggal_real_kembali'] ? $row['tanggal_real_kembali'] : '-';
            $kondisi  = $row['kondisi_kembali'] ? $row['kondisi_kembali'] : '-';
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama_lengkap'] ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= $row['tanggal_pinjam'] ?></td>
            <td><?= $row['tanggal_kembali'] ?></td>
            <td><?= $tgl_real ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $kondisi ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>