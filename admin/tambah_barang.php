<?php
session_start();
require '../config/koneksi.php';

// Cek Login Admin
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $stok        = $_POST['stok'];
    $deskripsi   = $_POST['deskripsi'];

    // Fitur Upload Gambar Sederhana
    // Pastikan folder assets/img/ sudah ada
    $nama_file = $_FILES['gambar']['name'];
    $tmp_file  = $_FILES['gambar']['tmp_name'];
    
    if($nama_file != "") {
        // Pindahkan file dari temp ke folder
        move_uploaded_file($tmp_file, "../assets/img/" . $nama_file);
    }

    $query = "INSERT INTO barang (id_kategori, nama_barang, deskripsi, stok_tersedia, stok_rusak, gambar) 
              VALUES ('$id_kategori', '$nama_barang', '$deskripsi', '$stok', 0, '$nama_file')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Barang Berhasil Ditambahkan!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal Menambah Data');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Barang - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h5>Tambah Barang Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data"> <!-- enctype penting untuk upload foto -->
                            
                            <div class="mb-3">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Kategori</label>
                                <select name="id_kategori" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php
                                    $kat = mysqli_query($conn, "SELECT * FROM kategori");
                                    while ($k = mysqli_fetch_assoc($kat)) {
                                        echo "<option value='" . $k['id_kategori'] . "'>" . $k['nama_kategori'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Jumlah Stok Awal</label>
                                    <input type="number" name="stok" class="form-control" min="1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Foto Barang</label>
                                    <input type="file" name="gambar" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="simpan" class="btn btn-success">Simpan Barang</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>