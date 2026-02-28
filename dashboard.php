<?php
session_start();
include "config/koneksi.php";

// ========================
// CEK LOGIN
// ========================
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];

// ========================
// STATISTIK UTAMA
// ========================
$total_siswa = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM siswa"));
$total_konseling = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM konseling"));
$total_pelanggaran = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pelanggaran"));

// ==========================
// DATA GRAFIK PELANGGARAN
// ==========================

// Per Hari (7 hari terakhir)
$qHarian = mysqli_query($koneksi,"
SELECT DATE(tanggal) as tgl, COUNT(*) as jumlah
FROM pelanggaran
WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY DATE(tanggal)
");

$labelHarian = [];
$dataHarian  = [];
while($row = mysqli_fetch_assoc($qHarian)){
    $labelHarian[] = $row['tgl'];
    $dataHarian[]  = $row['jumlah'];
}

// Per Minggu
$qMinggu = mysqli_query($koneksi,"
SELECT WEEK(tanggal) as minggu, COUNT(*) as jumlah
FROM pelanggaran
GROUP BY WEEK(tanggal)
");

$labelMinggu = [];
$dataMinggu  = [];
while($row = mysqli_fetch_assoc($qMinggu)){
    $labelMinggu[] = "Minggu ".$row['minggu'];
    $dataMinggu[]  = $row['jumlah'];
}

// Per Bulan
$qBulan = mysqli_query($koneksi,"
SELECT MONTH(tanggal) as bulan, COUNT(*) as jumlah
FROM pelanggaran
GROUP BY MONTH(tanggal)
");

$labelBulan = [];
$dataBulan  = [];
while($row = mysqli_fetch_assoc($qBulan)){
    $labelBulan[] = "Bulan ".$row['bulan'];
    $dataBulan[]  = $row['jumlah'];
}

// Per Tahun
$qTahun = mysqli_query($koneksi,"
SELECT YEAR(tanggal) as tahun, COUNT(*) as jumlah
FROM pelanggaran
GROUP BY YEAR(tanggal)
");

$labelTahun = [];
$dataTahun  = [];
while($row = mysqli_fetch_assoc($qTahun)){
    $labelTahun[] = $row['tahun'];
    $dataTahun[]  = $row['jumlah'];
}

// Pelanggaran Terbanyak
$qTerbanyak = mysqli_query($koneksi,"
SELECT jenis_pelanggaran, COUNT(*) as total
FROM pelanggaran
GROUP BY jenis_pelanggaran
ORDER BY total DESC
LIMIT 1
");

$dataTerbanyak = mysqli_fetch_assoc($qTerbanyak);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard - Sistem Konseling</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { background:#f4f6f9; font-family:'Segoe UI',sans-serif; }

.sidebar {
    width:250px;height:100vh;position:fixed;
    background:linear-gradient(180deg,#0d6efd,#0a58ca);
    color:white;padding-top:20px;
}

.sidebar h4 { text-align:center;margin-bottom:30px; }

.sidebar a {
    color:white;text-decoration:none;
    padding:12px 20px;display:block;
    margin:5px 15px;border-radius:10px;
    transition:0.3s;
}

.sidebar a:hover {
    background:rgba(255,255,255,0.2);
    transform:translateX(5px);
}

.content { margin-left:260px;padding:30px; }

.navbar-top {
    margin-left:250px;background:white;
    padding:15px 30px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

.card-stat {
    border:none;border-radius:20px;
    color:white;transition:0.3s;
}

.card-stat:hover {
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.bg-siswa { background:linear-gradient(45deg,#0d6efd,#3d8bfd); }
.bg-konseling { background:linear-gradient(45deg,#198754,#20c997); }
.bg-pelanggaran { background:linear-gradient(45deg,#dc3545,#ff6b6b); }

@media(max-width:768px){
.sidebar{display:none;}
.content{margin-left:0;}
.navbar-top{margin-left:0;}
}
</style>
</head>

<body>

<div class="sidebar animate__animated animate__fadeInLeft">
    <h4>E-Konseling</h4>
    <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="data_siswa.php"><i class="bi bi-people"></i> Data Siswa</a>
    <a href="data_konseling.php"><i class="bi bi-chat-dots"></i> Konseling</a>
    <a href="data_pelanggaran.php"><i class="bi bi-exclamation-triangle"></i> Pelanggaran</a>
    <a href="jadwal.php"><i class="bi bi-calendar-event"></i> Jadwal</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="navbar-top d-flex justify-content-between align-items-center animate__animated animate__fadeInDown">
    <h5>Dashboard</h5>
    <div>
        👤 <strong><?= htmlspecialchars($username); ?></strong>
        (<?= htmlspecialchars($role); ?>)
    </div>
</div>

<div class="content">

<div class="row g-4 mt-2">
    <div class="col-md-4 animate__animated animate__fadeInUp">
        <div class="card card-stat bg-siswa p-4">
            <h6>Total Siswa</h6>
            <h2><?= $total_siswa; ?></h2>
        </div>
    </div>

    <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="card card-stat bg-konseling p-4">
            <h6>Total Konseling</h6>
            <h2><?= $total_konseling; ?></h2>
        </div>
    </div>

    <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
        <div class="card card-stat bg-pelanggaran p-4">
            <h6>Total Pelanggaran</h6>
            <h2><?= $total_pelanggaran; ?></h2>
        </div>
    </div>
</div>

<div class="card shadow-sm p-4 rounded-4 mt-5 animate__animated animate__fadeInUp">
    <h5>Grafik Statistik Pelanggaran</h5>
    <div class="row mt-3">
        <div class="col-md-6 mb-4"><canvas id="chartHarian"></canvas></div>
        <div class="col-md-6 mb-4"><canvas id="chartMinggu"></canvas></div>
        <div class="col-md-6 mb-4"><canvas id="chartBulan"></canvas></div>
        <div class="col-md-6 mb-4"><canvas id="chartTahun"></canvas></div>
    </div>

    <hr>
    <h5>Pelanggaran Terbanyak</h5>
    <h4 class="text-danger">
        <?= $dataTerbanyak['jenis_pelanggaran'] ?? 'Belum Ada Data'; ?>
    </h4>
    <p>Total: <?= $dataTerbanyak['total'] ?? 0; ?> kali</p>
</div>

</div>

<script>
new Chart(document.getElementById('chartHarian'),{
type:'line',
data:{labels:<?=json_encode($labelHarian);?>,
datasets:[{label:'Harian',data:<?=json_encode($dataHarian);?>}]}
});

new Chart(document.getElementById('chartMinggu'),{
type:'bar',
data:{labels:<?=json_encode($labelMinggu);?>,
datasets:[{label:'Mingguan',data:<?=json_encode($dataMinggu);?>}]}
});

new Chart(document.getElementById('chartBulan'),{
type:'bar',
data:{labels:<?=json_encode($labelBulan);?>,
datasets:[{label:'Bulanan',data:<?=json_encode($dataBulan);?>}]}
});

new Chart(document.getElementById('chartTahun'),{
type:'bar',
data:{labels:<?=json_encode($labelTahun);?>,
datasets:[{label:'Tahunan',data:<?=json_encode($dataTahun);?>}]}
});
</script>

</body>
</html>