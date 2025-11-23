<?php
session_start();
require '../config/koneksi.php';


if (!isset($_SESSION['login']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    header("Location: ../index.php");
    exit;
}


if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_barang') {
    $id_barang = $_GET['id'];
    

    $q = mysqli_query($conn, "SELECT gambar FROM barang WHERE id_barang = '$id_barang'");
    $img = mysqli_fetch_assoc($q);
    if (!empty($img['gambar']) && file_exists("../assets/img/" . $img['gambar'])) {
        unlink("../assets/img/" . $img['gambar']); 
    }

    mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id_barang'");
    echo "<script>alert('Barang BERHASIL Dihapus!'); window.location='dashboard.php';</script>";
}


if (isset($_GET['aksi']) && $_GET['aksi'] == 'approve') {
    $id_pinjam = $_GET['id'];
    $id_barang = $_GET['id_barang'];
    $jml       = $_GET['jml'];

    mysqli_query($conn, "UPDATE barang SET stok_tersedia = stok_tersedia - $jml WHERE id_barang = '$id_barang'");
    mysqli_query($conn, "UPDATE peminjaman SET status = 'disetujui' WHERE id_peminjaman = '$id_pinjam'");
    
    echo "<script>alert('Peminjaman DISETUJUI!'); window.location='dashboard.php';</script>";
}


if (isset($_GET['aksi']) && $_GET['aksi'] == 'tolak') {
    $id_pinjam = $_GET['id'];
    mysqli_query($conn, "UPDATE peminjaman SET status = 'ditolak' WHERE id_peminjaman = '$id_pinjam'");
    echo "<script>alert('Peminjaman DITOLAK!'); window.location='dashboard.php';</script>";
}


if (isset($_POST['proses_kembali'])) {
    $id_pinjam = $_POST['id_peminjaman'];
    $id_barang = $_POST['id_barang'];
    $jml       = $_POST['jumlah'];
    $kondisi   = $_POST['kondisi'];

    if ($kondisi == 'baik') {
        mysqli_query($conn, "UPDATE barang SET stok_tersedia = stok_tersedia + $jml WHERE id_barang = '$id_barang'");
    } else {
        mysqli_query($conn, "UPDATE barang SET stok_rusak = stok_rusak + $jml WHERE id_barang = '$id_barang'");
    }

    mysqli_query($conn, "UPDATE peminjaman SET status = 'dikembalikan', kondisi_kembali = '$kondisi', tanggal_real_kembali = NOW() WHERE id_peminjaman = '$id_pinjam'");
    
    echo "<script>alert('Barang Diterima. Kondisi: ".strtoupper($kondisi)."'); window.location='dashboard.php';</script>";
}


$total_barang = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM barang"));
$total_pinjam = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='disetujui'"));
$total_stok   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stok_tersedia) as total FROM barang"));
$total_rusak  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stok_rusak) as total FROM barang"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin - SMK Telkom</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">

    <style>
        .navbar-maroon {
            background-color: #800000 !important; 
        }
        
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; height: 100%; width: 4px;
            background-color: #800000;
        }
        .stat-icon {
            width: 50px; height: 50px;
            background-color: #fff0f1;
            color: #800000;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
        }
        .bg-header-maroon {
            background-color: #800000;
            color: white;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-maroon shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-network-wired me-2"></i>Admin Panel</a>
            <div class="d-flex align-items-center">
                
                <?php if($_SESSION['role'] == 'superadmin') { ?>
                    <a href="../superadmin/kelola_user.php" class="btn btn-sm btn-outline-light me-3">
                        <i class="fas fa-users-cog"></i> Kelola User
                    </a>
                <?php } ?>

                <span class="navbar-text text-white me-3">Halo, <b><?= $_SESSION['nama'] ?></b></span>
                <a href="#" id="btn-logout" class="btn btn-sm btn-danger shadow-sm" style="background-color: #b30000; border:none;">Logout <i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </nav>

    <div class="container">

        <div class="row mb-4 g-3"> 
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_barang ?></h3>
                        <small class="text-secondary">Jenis Barang</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_stok['total'] ?? 0 ?></h3>
                        <small class="text-secondary">Stok Tersedia</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_pinjam ?></h3>
                        <small class="text-secondary">Sedang Dipinjam</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #ffe6e6; color: #dc3545;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-0"><?= $total_rusak['total'] ?? 0 ?></h3>
                        <small class="text-secondary">Barang Rusak</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-dark"><i class="fas fa-chart-line me-2" style="color: #800000;"></i>Dashboard Monitoring</h4>
            <a href="export_excel.php" class="btn btn-success shadow-sm">
                <i class="fas fa-file-excel me-2"></i> Download Laporan
            </a>
        </div>

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-header-maroon">
                <h6 class="mb-0"><i class="fas fa-bell me-2"></i> Permintaan Peminjaman (Pending)</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Barang</th>
                            <th>Jml</th>
                            <th>Tgl Pinjam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_req = mysqli_query($conn, "SELECT p.*, u.nama_lengkap, b.nama_barang 
                                                          FROM peminjaman p JOIN users u ON p.id_user = u.id_user JOIN barang b ON p.id_barang = b.id_barang 
                                                          WHERE p.status = 'pending'");
                        if (mysqli_num_rows($query_req) > 0) {
                            while ($req = mysqli_fetch_assoc($query_req)) { ?>
                            <tr>
                                <td><?= $req['nama_lengkap'] ?></td>
                                <td><?= $req['nama_barang'] ?></td>
                                <td><?= $req['jumlah'] ?></td>
                                <td><?= $req['tanggal_pinjam'] ?></td>
                                <td>
                                    <a href="dashboard.php?aksi=approve&id=<?= $req['id_peminjaman'] ?>&id_barang=<?= $req['id_barang'] ?>&jml=<?= $req['jumlah'] ?>" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>
                                    <a href="dashboard.php?aksi=tolak&id=<?= $req['id_peminjaman'] ?>" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                                </td>
                            </tr>
                            <?php } 
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Tidak ada permintaan baru.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header text-dark bg-white border-bottom">
                <h6 class="mb-0" style="color: #800000; font-weight: bold;"><i class="fas fa-clock me-2"></i> Barang Sedang Dipinjam</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Peminjam</th>
                            <th>Barang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_aktif = mysqli_query($conn, "SELECT p.*, u.nama_lengkap, b.nama_barang 
                                                            FROM peminjaman p JOIN users u ON p.id_user = u.id_user JOIN barang b ON p.id_barang = b.id_barang 
                                                            WHERE p.status = 'disetujui'");
                        while ($aktif = mysqli_fetch_assoc($query_aktif)) { ?>
                        <tr>
                            <td><?= $aktif['nama_lengkap'] ?></td>
                            <td><?= $aktif['nama_barang'] ?> <span class="badge bg-secondary rounded-pill"><?= $aktif['jumlah'] ?> Unit</span></td>
                            <td><span class="badge bg-success">Sedang Dipinjam</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" 
                                        style="background-color: #800000; border: none;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalKembali"
                                        data-id="<?= $aktif['id_peminjaman'] ?>"
                                        data-barang="<?= $aktif['id_barang'] ?>"
                                        data-nama="<?= $aktif['nama_barang'] ?>"
                                        data-jml="<?= $aktif['jumlah'] ?>">
                                    <i class="fas fa-clipboard-check"></i> Proses Kembali
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
    <h4 class="fw-bold text-dark"><i class="fas fa-boxes me-2" style="color: #800000;"></i>Stok Gudang</h4>
    <div>

        <a href="kelola_kategori.php" class="btn btn-outline-danger shadow-sm me-2">
            <i class="fas fa-tags me-2"></i> Kelola Kategori
        </a>

        <a href="tambah_barang.php" class="btn btn-success shadow-sm" style="background-color: #800000; border:none;">
            <i class="fas fa-plus me-2"></i> Tambah Barang
        </a>
    </div>
</div>
        </div>

        <div class="card shadow-sm mb-5 border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-header-maroon">
                        <tr>
                            <th class="text-white">Nama Barang</th>
                            <th class="text-white">Kategori</th>
                            <th class="text-center text-white">Stok Ada</th>
                            <th class="text-center text-white">Stok Rusak</th>
                            <th class="text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_brg = mysqli_query($conn, "SELECT b.*, k.nama_kategori FROM barang b LEFT JOIN kategori k ON b.id_kategori = k.id_kategori ORDER BY b.id_barang DESC");
                        while ($brg = mysqli_fetch_assoc($query_brg)) { ?>
                        <tr>
                            <td class="fw-bold"><?= $brg['nama_barang'] ?></td>
                            <td><?= $brg['nama_kategori'] ?></td>
                            <td class="text-center"><b class="text-success"><?= $brg['stok_tersedia'] ?></b></td>
                            <td class="text-center"><b class="text-danger"><?= $brg['stok_rusak'] ?></b></td>
                            <td>
                                <a href="edit_barang.php?id=<?= $brg['id_barang'] ?>" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a>
                                <a href="dashboard.php?aksi=hapus_barang&id=<?= $brg['id_barang'] ?>" 
                                class="btn btn-sm btn-danger" 
                                onclick="return confirm('Yakin ingin menghapus barang ini selamanya?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <div class="modal fade" id="modalKembali" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-header-maroon text-white">
                    <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_peminjaman" id="modal_id_pinjam">
                        <input type="hidden" name="id_barang" id="modal_id_barang">
                        <input type="hidden" name="jumlah" id="modal_jumlah">
                        
                        <p>Siswa mengembalikan barang: <b id="modal_nama_barang" style="color: #800000;"></b></p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bagaimana Kondisi Barang?</label>
                            <select name="kondisi" class="form-select" required>
                                <option value="baik">✅ BAIK (Stok kembali tersedia)</option>
                                <option value="rusak">❌ RUSAK (Masuk stok rusak)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_kembali" class="btn btn-primary" style="background-color: #800000; border:none;">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    

    <script>
        const modalKembali = document.getElementById('modalKembali')
        modalKembali.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget
            modalKembali.querySelector('#modal_id_pinjam').value = button.getAttribute('data-id')
            modalKembali.querySelector('#modal_id_barang').value = button.getAttribute('data-barang')
            modalKembali.querySelector('#modal_jumlah').value = button.getAttribute('data-jml')
            modalKembali.querySelector('#modal_nama_barang').textContent = button.getAttribute('data-nama')
        })
    </script>

    <script>
    document.getElementById('btn-logout').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: "Sesi Anda akan diakhiri!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../logout.php'; 
            }
        })
    });
    </script>
</body>
</html>