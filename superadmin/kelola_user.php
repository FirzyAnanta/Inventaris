<?php
session_start();
require '../config/koneksi.php';

// Keamanan: Hanya Superadmin yang boleh masuk sini
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../index.php");
    exit;
}

// --- LOGIKA TAMBAH USER BARU (ADMIN/USER) ---
if (isset($_POST['tambah_user'])) {
    $nomor_induk = $_POST['nomor_induk'];
    $nama        = $_POST['nama'];
    $role        = $_POST['role'];
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi

    // Cek apakah NIS/NIP sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE nomor_induk = '$nomor_induk'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIS/NIP sudah terdaftar!');</script>";
    } else {
        mysqli_query($conn, "INSERT INTO users (nomor_induk, nama_lengkap, password, role) VALUES ('$nomor_induk', '$nama', '$password', '$role')");
        echo "<script>alert('Berhasil menambah user baru!'); window.location='kelola_user.php';</script>";
    }
}

// --- LOGIKA HAPUS USER ---
if (isset($_GET['hapus_id'])) {
    $id = $_GET['hapus_id'];
    // Jangan biarkan superadmin menghapus dirinya sendiri
    if ($id == $_SESSION['id_user']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='kelola_user.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id'");
        echo "<script>alert('User berhasil dihapus.'); window.location='kelola_user.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola User - Superadmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-users-cog"></i> Manajemen Akun Pengguna</h3>
            <div>
                <a href="../admin/dashboard.php" class="btn btn-secondary me-2">Kembali ke Dashboard</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser"><i class="fas fa-user-plus"></i> Tambah Akun</button>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIS / NIP</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Tampilkan semua user kecuali akun yang sedang login
                        $my_id = $_SESSION['id_user'];
                        $query = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
                        
                        while ($row = mysqli_fetch_assoc($query)) { 
                            // Warna badge role
                            if($row['role'] == 'superadmin') $badge = 'danger';
                            elseif($row['role'] == 'admin') $badge = 'success';
                            else $badge = 'secondary';
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nomor_induk'] ?></td>
                            <td><?= $row['nama_lengkap'] ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= strtoupper($row['role']) ?></span></td>
                            <td>
                                <?php if($row['id_user'] != $my_id): ?>
                                <a href="kelola_user.php?hapus_id=<?= $row['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus user ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                <?php else: ?>
                                <span class="text-muted small">Akun Saya</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH USER -->
    <div class="modal fade" id="modalTambahUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>NIS / NIP</label>
                            <input type="number" name="nomor_induk" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role (Hak Akses)</label>
                            <select name="role" class="form-select">
                                <option value="user">Siswa (User)</option>
                                <option value="admin">Admin (Guru/Petugas)</option>
                                <option value="superadmin">Superadmin (Kepala)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_user" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>