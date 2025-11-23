<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];

if (isset($_POST['ajukan_pinjam'])) {
    $id_barang   = $_POST['id_barang'];
    $jumlah      = $_POST['jumlah'];
    $tgl_kembali = $_POST['tgl_kembali'];
    $tgl_sekarang = date('Y-m-d');

    $cek_barang = mysqli_query($conn, "SELECT stok_tersedia FROM barang WHERE id_barang = '$id_barang'");
    $data_barang = mysqli_fetch_assoc($cek_barang);

    if ($jumlah > $data_barang['stok_tersedia']) {
        echo "<script>alert('Gagal! Jumlah pinjam melebihi stok tersedia.'); window.location='dashboard.php';</script>";
    } elseif ($tgl_kembali < $tgl_sekarang) {
        echo "<script>alert('Gagal! Tanggal kembali tidak boleh kurang dari hari ini.'); window.location='dashboard.php';</script>";
    } else {
        $query = "INSERT INTO peminjaman (id_user, id_barang, tanggal_pinjam, tanggal_kembali, jumlah, status) 
                  VALUES ('$id_user', '$id_barang', '$tgl_sekarang', '$tgl_kembali', '$jumlah', 'pending')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Berhasil diajukan! Tunggu persetujuan Admin.'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan sistem.');</script>";
        }
    }
}


$where = " WHERE 1=1 "; 
if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
    $kat_id = $_GET['kategori'];
    $where .= " AND b.id_kategori = '$kat_id' ";
}
if (isset($_GET['q']) && $_GET['q'] != '') {
    $search = $_GET['q'];
    $where .= " AND b.nama_barang LIKE '%$search%' ";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SMK Telkom</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">

    <style>
        .hero-banner {
            background: linear-gradient(135deg, #800000, #b30000);
            border-radius: 20px;
            padding: 60px 40px;
            color: white;
            box-shadow: 0 10px 30px rgba(128, 0, 0, 0.2);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .hero-banner::before {
            content: ''; position: absolute; top: -50px; right: -50px;
            width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;
        }
        
        .product-card {
            border: none; border-radius: 15px; transition: all 0.3s ease;
            background: white; overflow: hidden; height: 100%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .product-card:hover {
            transform: translateY(-5px); box-shadow: 0 15px 30px rgba(128,0,0,0.15);
        }
        .product-img {
            height: 200px; object-fit: cover; width: 100%; border-bottom: 1px solid #f0f0f0;
        }
        .badge-stok {
            position: absolute; top: 15px; right: 15px;
            padding: 5px 12px; border-radius: 20px; font-size: 0.8rem;
        }
        .filter-bar {
            background: white; padding: 20px; border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); margin-bottom: 30px;
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0 12px;
        }
        .table-modern tbody tr {
            background-color: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.03);
            transition: transform 0.2s;
        }
        .table-modern tbody tr:hover {
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .table-modern td {
            border: none;
            padding: 15px 20px;
            vertical-align: middle;
        }
        .table-modern td:first-child { border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
        .table-modern td:last-child { border-top-right-radius: 15px; border-bottom-right-radius: 15px; }

        .badge-soft-warning { background-color: #fff8e1; color: #f57c00; }
        .badge-soft-success { background-color: #e8f5e9; color: #2e7d32; }
        .badge-soft-danger  { background-color: #ffebee; color: #c62828; }
        .badge-soft-secondary { background-color: #f5f5f5; color: #616161; }
        .badge-pill-modern {
            padding: 8px 15px; border-radius: 30px; font-weight: 600;
            font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #800000;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-school me-2"></i>Inventaris Telkom</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3 text-white">
                        Halo, <b><?= $nama_user; ?></b>
                    </li>
                    <li class="nav-item">
                        <a href="#" id="btn-logout" class="btn btn-sm btn-light text-danger fw-bold">Logout <i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <div class="hero-banner d-flex align-items-center justify-content-between">
            <div>
                <h1 class="fw-bold mb-2">Selamat Datang di Inventaris!</h1>
                <p class="lead mb-4 opacity-75">Pinjam peralatan sekolah SMK Telkom Lampung dengan mudah, cepat, dan terdata.</p>
                <a href="#katalog" class="btn btn-light text-danger fw-bold px-4 py-2 shadow-sm">
                    <i class="fas fa-search me-2"></i>Cari Barang Sekarang
                </a>
            </div>
            <div class="d-none d-md-block">
                <i class="fas fa-laptop-code" style="font-size: 150px; opacity: 0.2;"></i>
            </div>
        </div>

        <div class="filter-bar" id="katalog">
            <form action="" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <h5 class="fw-bold mb-0 text-secondary"><i class="fas fa-filter me-2"></i>Filter Barang</h5>
                    </div>
                    <div class="col-md-3">
                        <select name="kategori" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <?php
                            $kat_query = mysqli_query($conn, "SELECT * FROM kategori");
                            while ($k = mysqli_fetch_assoc($kat_query)) {
                                $selected = (isset($_GET['kategori']) && $_GET['kategori'] == $k['id_kategori']) ? 'selected' : '';
                                echo "<option value='".$k['id_kategori']."' $selected>".$k['nama_kategori']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Ketik nama barang..." value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>">
                            <button class="btn btn-danger" type="submit"><i class="fas fa-search"></i> Cari</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <?php
            $query_barang = mysqli_query($conn, "SELECT b.*, k.nama_kategori FROM barang b LEFT JOIN kategori k ON b.id_kategori = k.id_kategori $where ORDER BY b.id_barang DESC");
            
            if(mysqli_num_rows($query_barang) > 0) {
                while ($row = mysqli_fetch_assoc($query_barang)) { 
                    $gambar = $row['gambar'] ? "../assets/img/".$row['gambar'] : "https://via.placeholder.com/300x200?text=No+Image";
                    $stok = $row['stok_tersedia'];
                    $status_class = $stok > 0 ? "success" : "secondary";
                    $status_text  = $stok > 0 ? "Tersedia: $stok" : "Stok Habis";
                    $btn_disabled = $stok > 0 ? "" : "disabled";
            ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card product-card">
                    <div class="position-relative">
                        <img src="<?= $gambar ?>" class="product-img" alt="<?= $row['nama_barang'] ?>">
                        <span class="badge bg-<?= $status_class ?> badge-stok"><?= $status_text ?></span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-1"><?= $row['nama_kategori'] ?></small>
                        <h5 class="card-title fw-bold text-dark"><?= $row['nama_barang'] ?></h5>
                        <p class="card-text text-secondary small flex-grow-1"><?= substr($row['deskripsi'], 0, 50) ?>...</p>
                        
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalDetail"
                                    data-id="<?= $row['id_barang'] ?>"
                                    data-nama="<?= $row['nama_barang'] ?>"
                                    data-desk="<?= $row['deskripsi'] ?>"
                                    data-stok="<?= $stok ?>"
                                    data-img="<?= $gambar ?>"
                                    <?= $btn_disabled ?>>
                                <i class="fas fa-eye"></i> Lihat Detail & Pinjam
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo '<div class="col-12 text-center py-5"><h4 class="text-muted">Barang tidak ditemukan :(</h4><a href="dashboard.php" class="btn btn-outline-danger btn-sm">Reset Filter</a></div>';
            }
            ?>
        </div>

        <div class="mt-5 mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="fw-bold text-dark"><i class="fas fa-history me-2" style="color: #800000;"></i>Riwayat Peminjaman</h4>
                <span class="badge bg-white text-secondary border shadow-sm px-3 py-2">Terbaru</span>
            </div>

            <div class="table-responsive">
                <table class="table table-modern table-borderless">
                    <thead class="text-secondary small text-uppercase" style="letter-spacing: 1px;">
                        <tr>
                            <th class="ps-4">Barang</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_history = mysqli_query($conn, "SELECT p.*, b.nama_barang, b.gambar 
                                                              FROM peminjaman p 
                                                              JOIN barang b ON p.id_barang = b.id_barang 
                                                              WHERE p.id_user = '$id_user' 
                                                              ORDER BY p.id_peminjaman DESC");
                        
                        if(mysqli_num_rows($query_history) > 0) {
                            while ($hist = mysqli_fetch_assoc($query_history)) {
                                if($hist['status'] == 'pending') {
                                    $badge_class = 'badge-soft-warning';
                                    $icon = '<i class="fas fa-clock"></i>';
                                    $text_status = 'Menunggu Konfirmasi';
                                } elseif($hist['status'] == 'disetujui') {
                                    $badge_class = 'badge-soft-success';
                                    $icon = '<i class="fas fa-check-circle"></i>';
                                    $text_status = 'Sedang Dipinjam';
                                } elseif($hist['status'] == 'ditolak') {
                                    $badge_class = 'badge-soft-danger';
                                    $icon = '<i class="fas fa-times-circle"></i>';
                                    $text_status = 'Ditolak';
                                } else { 
                                    $badge_class = 'badge-soft-secondary';
                                    $icon = '<i class="fas fa-box-open"></i>';
                                    $text_status = 'Sudah Dikembalikan';
                                }

                                $img_thumb = $hist['gambar'] ? "../assets/img/".$hist['gambar'] : "https://via.placeholder.com/50";
                        ?>
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <img src="<?= $img_thumb ?>" class="rounded-3 shadow-sm me-3" width="50" height="50" style="object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?= $hist['nama_barang'] ?></h6>
                                        <small class="text-muted"><?= $hist['jumlah'] ?> Unit</small>
                                    </div>
                                </div>
                            </td>
                            
                            <td>
                                <div class="text-dark fw-bold"><?= date('d M Y', strtotime($hist['tanggal_pinjam'])) ?></div>
                                <small class="text-muted">Diajukan</small>
                            </td>
                            
                            <td>
                                <div class="text-danger fw-bold"><?= date('d M Y', strtotime($hist['tanggal_kembali'])) ?></div>
                                <small class="text-muted">Batas Kembali</small>
                            </td>
                            
                            <td>
                                <span class="badge-pill-modern <?= $badge_class ?>">
                                    <?= $icon ?> <?= $text_status ?>
                                </span>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo '<tr><td colspan="4" class="text-center py-5 text-muted bg-white rounded-3 shadow-sm">Belum ada riwayat peminjaman.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #800000;">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Barang & Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="" id="modal_img" class="img-fluid rounded shadow-sm mb-3" style="width:100%; height:250px; object-fit:cover;">
                                <h5 class="fw-bold mb-1" id="modal_nama_display"></h5>
                                <p class="text-muted small" id="modal_desk_display"></p>
                                <div class="alert alert-warning py-2 small">
                                    <i class="fas fa-exclamation-triangle"></i> Stok Tersedia: <b id="modal_stok_display">0</b>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">Isi Formulir Peminjaman</h6>
                                <input type="hidden" name="id_barang" id="modal_id_barang">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Pinjam</label>
                                    <input type="number" name="jumlah" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rencana Kembali</label>
                                    <input type="date" name="tgl_kembali" class="form-control" required>
                                </div>
                                <div class="alert alert-light border small text-muted">
                                    <ul>
                                        <li>Kembalikan tepat waktu.</li>
                                        <li>Jaga barang agar tidak rusak.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="ajukan_pinjam" class="btn btn-danger">Ajukan Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const modalDetail = document.getElementById('modalDetail')
        modalDetail.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget
            modalDetail.querySelector('#modal_id_barang').value = button.getAttribute('data-id')
            modalDetail.querySelector('#modal_nama_display').textContent = button.getAttribute('data-nama')
            modalDetail.querySelector('#modal_desk_display').textContent = button.getAttribute('data-desk')
            modalDetail.querySelector('#modal_stok_display').textContent = button.getAttribute('data-stok')
            modalDetail.querySelector('#modal_img').src = button.getAttribute('data-img')
            modalDetail.querySelector('input[name="jumlah"]').setAttribute('max', button.getAttribute('data-stok'))
        })
    </script>

    <script>
    document.getElementById('btn-logout').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Keluar!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../logout.php'; 
            }
        })
    });
    </script>
</body>
</html>