<?php
require 'config/koneksi.php';
$success = false;

if (isset($_POST['register'])) {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; 

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE nomor_induk = '$nis'");
    if (mysqli_num_rows($cek) > 0) {
        $error_msg = "NIS sudah terdaftar di sistem!";
    } else {
        $query = "INSERT INTO users (nomor_induk, nama_lengkap, password, role) VALUES ('$nis', '$nama', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Inventaris Telkom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="auth-body">

    <div class="container">
        <div class="auth-box">
            <div class="row g-0 h-100 flex-row-reverse"> <!-- flex-row-reverse biar gambarnya dikanan -->
                
                <!-- Kolom Kanan (Gambar/Info - Dibalik jadi di kanan) -->
                <div class="col-md-6 auth-left d-none d-md-flex">
                    <i class="fas fa-user-graduate fa-5x mb-4"></i>
                    <h2 class="fw-bold">Pendaftaran Siswa</h2>
                    <p class="mt-2 px-4">Buat akun untuk mulai meminjam peralatan praktik dan fasilitas sekolah.</p>
                </div>
                
                <!-- Kolom Kiri (Form Register) -->
                <div class="col-md-6 auth-right">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark">Buat Akun Baru</h3>
                        <p class="text-muted">Isi data diri Anda dengan benar.</p>
                    </div>

                    <form method="POST">
                        <div class="form-floating mb-3">
                            <input type="number" name="nis" class="form-control" id="regNis" placeholder="NIS" required>
                            <label for="regNis"><i class="fas fa-id-badge me-2"></i>Nomor Induk Siswa (NIS)</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="nama" class="form-control" id="regNama" placeholder="Nama Lengkap" required>
                            <label for="regNama"><i class="fas fa-user me-2"></i>Nama Lengkap</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" name="password" class="form-control" id="regPass" placeholder="Password" required>
                            <label for="regPass"><i class="fas fa-lock me-2"></i>Password</label>
                        </div>
                        <button type="submit" name="register" class="btn btn-auth w-100 mb-3">Daftar Sekarang</button>
                    </form>

                    <div class="text-center">
                        <small>Sudah punya akun? <a href="index.php" class="text-danger fw-bold text-decoration-none">Login Disini</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert Logic -->
    <?php if(isset($error_msg)) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Daftar',
            text: '<?= $error_msg ?>',
            confirmButtonColor: '#800000'
        });
    </script>
    <?php endif; ?>

    <?php if($success) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Pendaftaran Berhasil!',
            text: 'Akun Anda telah dibuat. Silakan login.',
            confirmButtonColor: '#800000',
            confirmButtonText: 'Ke Halaman Login'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'index.php';
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>