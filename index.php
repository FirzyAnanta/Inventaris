<?php
session_start();
require 'config/koneksi.php';

if (isset($_POST['login'])) {
    $nomor_induk = $_POST['nomor_induk'];
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE nomor_induk = '$nomor_induk'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['nama'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role'];

            // Redirect Sesuai Role
            if ($row['role'] == 'superadmin') {
                header("Location: admin/dashboard.php"); // Superadmin numpang dashboard admin
            } elseif ($row['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris Telkom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="auth-body">

    <div class="container">
        <div class="auth-box">
            <div class="row g-0 h-100">
                <!-- Kolom Kiri (Gambar/Info) -->
                <div class="col-md-6 auth-left d-none d-md-flex">
                    <i class="fas fa-school fa-5x mb-4"></i>
                    <h2 class="fw-bold">SMK Telkom Lampung</h2>
                    <p class="mt-2 px-4">Sistem Informasi Inventaris Sarana & Prasarana Sekolah.</p>
                    <small class="mt-5 text-white-50">&copy; 2025 Inventaris Team</small>
                </div>
                
                <!-- Kolom Kanan (Form) -->
                <div class="col-md-6 auth-right">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark">Selamat Datang!</h3>
                        <p class="text-muted">Silakan login untuk mengakses inventaris.</p>
                    </div>

                    <form method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" name="nomor_induk" class="form-control" id="floatingInput" placeholder="NIS/NIP" required>
                            <label for="floatingInput"><i class="fas fa-id-card me-2"></i>NIS / NIP</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                            <label for="floatingPassword"><i class="fas fa-lock me-2"></i>Password</label>
                        </div>
                        <button type="submit" name="login" class="btn btn-auth w-100 mb-3">Masuk Sekarang</button>
                    </form>

                    <div class="text-center">
                        <small>Siswa belum punya akun? <a href="register.php" class="text-danger fw-bold text-decoration-none">Daftar Disini</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert untuk Error -->
    <?php if(isset($error)) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: 'NIS/NIP atau Password yang Anda masukkan salah!',
            confirmButtonColor: '#800000'
        });
    </script>
    <?php endif; ?>

</body>
</html>