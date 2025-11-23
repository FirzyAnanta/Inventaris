<?php
session_start();
require '../config/koneksi.php';

if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];

// Ambil data barang yang mau diedit
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id'");
$data  = mysqli_fetch_assoc($query);

// Jika tombol Update ditekan
if (isset($_POST['update'])) {
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $stok        = $_POST['stok'];
    $deskripsi   = $_POST['deskripsi'];
    
    // Logika Ganti Gambar
    $nama_file = $_FILES['gambar']['name'];
    $tmp_file  = $_FILES['gambar']['tmp_name'];

    if ($nama_file != "") {
        // Jika upload gambar baru -> Hapus gambar lama & Upload yang baru
        if (!empty($data['gambar']) && file_exists("../assets/img/" . $data['gambar'])) {
            unlink("../assets/img/" . $data['gambar']);
        }
        move_uploaded_file($tmp_file, "../assets/img/" . $nama_file);
        
        // Query Update DENGAN Gambar
        $q_update = "UPDATE barang SET id_kategori='$id_kategori', nama_barang='$nama_barang', deskripsi='$deskripsi', stok_tersedia='$stok', gambar='$nama_file' WHERE id_barang='$id'";
    } else {
        // Query Update TANPA Ganti Gambar
        $q_update = "UPDATE barang SET id_kategori='$id_kategori', nama_barang='$nama_barang', deskripsi='$deskripsi', stok_tersedia='$stok' WHERE id_barang='$id'";
    }

    if (mysqli_query($conn, $q_update)) {
        echo "<script>alert('Data Berhasil Diupdate!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal Update!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Barang - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h5>Edit Data Barang</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="<?= $data['nama_barang'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Kategori</label>
                                <select name="id_kategori" class="form-select" required>
                                    <?php
                                    $kat = mysqli_query($conn, "SELECT * FROM kategori");
                                    while ($k = mysqli_fetch_assoc($kat)) {
                                        $selected = ($k['id_kategori'] == $data['id_kategori']) ? 'selected' : '';
                                        echo "<option value='" . $k['id_kategori'] . "' $selected>" . $k['nama_kategori'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Stok Tersedia (Bukan Rusak)</label>
                                <input type="number" name="stok" class="form-control" value="<?= $data['stok_tersedia'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>Ganti Foto (Biarkan kosong jika tidak diganti)</label>
                                <input type="file" name="gambar" class="form-control">
                                <?php if(!empty($data['gambar'])): ?>
                                    <small class="text-muted">Foto saat ini: <?= $data['gambar'] ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label>Deskripsi Singkat</label>
                                <textarea name="deskripsi" class="form-control" rows="3"><?= $data['deskripsi'] ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="update" class="btn btn-warning text-white">Update Data</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>