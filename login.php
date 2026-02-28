<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Sistem Konseling</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
        }

        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        .btn-login {
            border-radius: 30px;
        }

        .form-control {
            border-radius: 10px;
        }

        .school-name {
            font-weight: bold;
            font-size: 18px;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="card login-card col-md-4 col-11 bg-white">
    
    <div class="text-center mb-3">
        <img src="assets/logo.png" alt="Logo Sekolah" class="logo mb-2">
        <div class="school-name">SISTEM INFORMASI KONSELING</div>
        <small class="text-muted">SMK TUNAS KASIH</small>
    </div>

    <form method="POST" action="proses_login.php">
        
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-login">
                Login
            </button>
        </div>

    </form>

    <div class="text-center mt-3">
        <small class="text-muted">
            ©  Sistem Konseling Sekolah
        </small>
    </div>

</div>

</body>
</html>