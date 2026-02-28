<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* ================= TAMBAH ================= */
if (isset($_POST['simpan'])) {

    $id_siswa          = $_POST['id_siswa'] ?? '';
    $tanggal           = $_POST['tanggal'] ?? '';
    $jenis_pelanggaran = $_POST['jenis_pelanggaran'] ?? '';
    $poin              = $_POST['poin'] ?? 0;
    $keterangan        = $_POST['keterangan'] ?? '';

    if ($id_siswa != '' && $tanggal != '') {

        mysqli_query($koneksi, "INSERT INTO pelanggaran
        (id_siswa,tanggal,jenis_pelanggaran,poin,keterangan)
        VALUES
        ('$id_siswa','$tanggal','$jenis_pelanggaran','$poin','$keterangan')");

        header("Location: data_pelanggaran.php");
        exit;
    }
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $id_pelanggaran    = $_POST['id_pelanggaran'] ?? '';
    $id_siswa          = $_POST['id_siswa'] ?? '';
    $tanggal           = $_POST['tanggal'] ?? '';
    $jenis_pelanggaran = $_POST['jenis_pelanggaran'] ?? '';
    $poin              = $_POST['poin'] ?? 0;
    $keterangan        = $_POST['keterangan'] ?? '';

    if ($id_pelanggaran != '') {

        mysqli_query($koneksi, "UPDATE pelanggaran SET
        id_siswa='$id_siswa',
        tanggal='$tanggal',
        jenis_pelanggaran='$jenis_pelanggaran',
        poin='$poin',
        keterangan='$keterangan'
        WHERE id_pelanggaran='$id_pelanggaran'");

        header("Location: data_pelanggaran.php");
        exit;
    }
}

/* ================= DELETE ================= */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pelanggaran WHERE id_pelanggaran='$id'");
    header("Location: data_pelanggaran.php");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($koneksi,"
SELECT pelanggaran.*, siswa.nama
FROM pelanggaran
LEFT JOIN siswa ON pelanggaran.id_siswa = siswa.id_siswa
ORDER BY id_pelanggaran DESC
");

$total = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM pelanggaran"));
$total_poin = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT SUM(poin) as total FROM pelanggaran"))['total'] ?? 0;

$siswa = mysqli_query($koneksi,"SELECT * FROM siswa");
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Pelanggaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }

.sidebar {
    height:100vh;
    background:linear-gradient(180deg,#e74a3b,#be2617);
    color:white;
    position:fixed;
    width:220px;
}

.sidebar h4 { padding:20px; }

.sidebar a {
    display:block;
    padding:12px 20px;
    color:white;
    text-decoration:none;
    transition:0.3s;
}

.sidebar a:hover {
    background:rgba(255,255,255,0.2);
}

.content {
    margin-left:220px;
    padding:30px;
}

.card-stat {
    border:none;
    border-radius:15px;
    color:white;
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn {
    from {opacity:0; transform:translateY(20px);}
    to {opacity:1; transform:translateY(0);}
}

.table thead {
    background:#e74a3b;
    color:white;
}
</style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fas fa-exclamation-triangle"></i> E-Konseling</h4>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
    <a href="data_konseling.php"><i class="fas fa-comment-dots"></i> Data Konseling</a>
    <a href="data_pelanggaran.php"><i class="fas fa-exclamation-circle"></i> Data Pelanggaran</a>
</div>

<div class="content">

<h3 class="mb-4">🚨 Data Pelanggaran</h3>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-stat bg-danger p-4">
            <h5>Total Pelanggaran</h5>
            <h2><?= $total ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-stat bg-warning p-4">
            <h5>Total Poin</h5>
            <h2><?= $total_poin ?></h2>
        </div>
    </div>
</div>

<button class="btn btn-danger mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="fas fa-plus"></i> Tambah Pelanggaran
</button>

<div class="card shadow-sm">
<div class="card-body">
<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>Tanggal</th>
<th>Jenis</th>
<th>Poin</th>
<th>Keterangan</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($row=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama'] ?? '') ?></td>
<td><?= htmlspecialchars($row['tanggal'] ?? '') ?></td>
<td><?= htmlspecialchars($row['jenis_pelanggaran'] ?? '') ?></td>
<td><span class="badge bg-danger"><?= htmlspecialchars($row['poin'] ?? 0) ?></span></td>
<td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
<td>
<button class="btn btn-sm btn-warning"
data-bs-toggle="modal"
data-bs-target="#modalEdit<?= $row['id_pelanggaran'] ?>">
<i class="fas fa-edit"></i>
</button>

<a href="?hapus=<?= $row['id_pelanggaran'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Yakin hapus data ini?')">
<i class="fas fa-trash"></i>
</a>
</td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit<?= $row['id_pelanggaran'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Edit Data</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">

<input type="hidden" name="id_pelanggaran" value="<?= $row['id_pelanggaran'] ?>">

<select name="id_siswa" class="form-control mb-2" required>
<?php
$siswa2 = mysqli_query($koneksi,"SELECT * FROM siswa");
while($s=mysqli_fetch_assoc($siswa2)){
$selected = ($s['id_siswa']==$row['id_siswa']) ? "selected" : "";
echo "<option value='{$s['id_siswa']}' $selected>{$s['nama']}</option> - data_pelanggaran.php:225";
}
?>
</select>

<input type="date" name="tanggal" class="form-control mb-2"
value="<?= $row['tanggal'] ?>">

<input type="text" name="jenis_pelanggaran"
class="form-control mb-2"
value="<?= htmlspecialchars($row['jenis_pelanggaran']) ?>">

<input type="number" name="poin"
class="form-control mb-2"
value="<?= $row['poin'] ?>">

<textarea name="keterangan"
class="form-control mb-2"><?= htmlspecialchars($row['keterangan']) ?></textarea>

</div>
<div class="modal-footer">
<button type="submit" name="update" class="btn btn-warning">Update</button>
</div>
</form>
</div>
</div>
</div>

<?php } ?>
</tbody>
</table>
</div>
</div>
</div>

</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Tambah Data</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">

<select name="id_siswa" class="form-control mb-2" required>
<option value="">Pilih Siswa</option>
<?php
$siswa3 = mysqli_query($koneksi,"SELECT * FROM siswa");
while($s=mysqli_fetch_assoc($siswa3)){
echo "<option value='{$s['id_siswa']}'>{$s['nama']}</option> - data_pelanggaran.php:278";
}
?>
</select>

<input type="date" name="tanggal" class="form-control mb-2" required>
<input type="text" name="jenis_pelanggaran" class="form-control mb-2" placeholder="Jenis Pelanggaran">
<input type="number" name="poin" class="form-control mb-2" placeholder="Poin">
<textarea name="keterangan" class="form-control mb-2" placeholder="Keterangan"></textarea>

</div>
<div class="modal-footer">
<button type="submit" name="simpan" class="btn btn-danger">Simpan</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>