<?php
session_start();
require '../config/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['login']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../index.php");
    exit;
}

// --- LOGIKA TAMBAH KATEGORI ---
if (isset($_POST['simpan'])) {
    $nama_kategori = $_POST['nama_kategori'];
    
    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Kategori Berhasil Ditambahkan!'); window.location='kelola_kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal Menambah Kategori');</script>";
    }
}

// --- LOGIKA HAPUS KATEGORI ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Hapus kategori
    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori = '$id'");
    echo "<script>alert('Kategori Dihapus!'); window.location='kelola_kategori.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Kategori - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #800000;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php"><i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            
            <!-- KOLOM KIRI: FORM TAMBAH -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white text-dark fw-bold border-bottom">
                        <i class="fas fa-plus-circle text-danger me-2"></i> Tambah Kategori
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label>Nama Kategori Baru</label>
                                <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Multimedia, Lab..." required>
                            </div>
                            <button type="submit" name="simpan" class="btn btn-danger w-100" style="background-color: #800000; border:none;">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: TABEL DATA -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white text-dark fw-bold border-bottom">
                        <i class="fas fa-list text-danger me-2"></i> Daftar Kategori Saat Ini
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Nama Kategori</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id_kategori DESC");
                                while ($row = mysqli_fetch_assoc($kategori)) {
                                ?>
                                <tr>
                                    <td class="ps-4"><?= $no++; ?></td>
                                    <td class="fw-bold"><?= $row['nama_kategori'] ?></td>
                                    <td class="text-end pe-4">
                                        <a href="kelola_kategori.php?hapus=<?= $row['id_kategori'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Yakin hapus kategori ini? Barang dengan kategori ini akan kehilangan label kategorinya.')">
                                           <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>