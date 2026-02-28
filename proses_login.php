<?php
session_start();
include "config/koneksi.php";

$username = $_POST['username'];
$password = md5($_POST['password']);

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
$data  = mysqli_fetch_assoc($query);

if ($data) {
    $_SESSION['username'] = $data['username'];
    $_SESSION['role']     = $data['role'];
    $_SESSION['id_user']  = $data['id_user'];
    header("Location: dashboard.php");
} else {
    echo "<script>alert('Login gagal!');window.location='login.php';</script> - proses_login.php:17";
}
?>