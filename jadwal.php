<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* ================= TAMBAH ================= */
if (isset($_POST['simpan'])) {

    $id_siswa   = $_POST['id_siswa'] ?? '';
    $tanggal    = $_POST['tanggal'] ?? '';
    $jam        = $_POST['jam'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    if ($id_siswa != '' && $tanggal != '' && $jam != '') {

        mysqli_query($koneksi, "INSERT INTO jadwal
        (id_siswa,tanggal,jam,keterangan)
        VALUES
        ('$id_siswa','$tanggal','$jam','$keterangan')");

        header("Location: jadwal.php");
        exit;
    }
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $id_jadwal  = $_POST['id_jadwal'] ?? '';
    $id_siswa   = $_POST['id_siswa'] ?? '';
    $tanggal    = $_POST['tanggal'] ?? '';
    $jam        = $_POST['jam'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    if ($id_jadwal != '') {

        mysqli_query($koneksi, "UPDATE jadwal SET
        id_siswa='$id_siswa',
        tanggal='$tanggal',
        jam='$jam',
        keterangan='$keterangan'
        WHERE id_jadwal='$id_jadwal'");

        header("Location: jadwal.php");
        exit;
    }
}

/* ================= DELETE ================= */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM jadwal WHERE id_jadwal='$id'");
    header("Location: jadwal.php");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($koneksi,"
SELECT jadwal.*, siswa.nama
FROM jadwal
LEFT JOIN siswa ON jadwal.id_siswa = siswa.id_siswa
ORDER BY tanggal DESC, jam DESC
");

$total = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM jadwal"));
$siswa = mysqli_query($koneksi,"SELECT * FROM siswa");
?>

<!DOCTYPE html>
<html>
<head>
<title>Jadwal Konseling</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }

.sidebar {
    height:100vh;
    background:linear-gradient(180deg,#36b9cc,#258391);
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
    background:#36b9cc;
    color:white;
}
</style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fas fa-calendar-alt"></i> E-Konseling</h4>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
    <a href="data_konseling.php"><i class="fas fa-comment-dots"></i> Data Konseling</a>
    <a href="data_pelanggaran.php"><i class="fas fa-exclamation-circle"></i> Data Pelanggaran</a>
    <a href="jadwal.php"><i class="fas fa-calendar-check"></i> Jadwal</a>
</div>

<div class="content">

<h3 class="mb-4">📅 Jadwal Konseling</h3>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-stat bg-info p-4">
            <h5>Total Jadwal</h5>
            <h2><?= $total ?></h2>
        </div>
    </div>
</div>

<button class="btn btn-info text-white mb-3"
data-bs-toggle="modal"
data-bs-target="#modalTambah">
<i class="fas fa-plus"></i> Tambah Jadwal
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
<th>Jam</th>
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
<td><?= htmlspecialchars($row['jam'] ?? '') ?></td>
<td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
<td>

<button class="btn btn-sm btn-warning"
data-bs-toggle="modal"
data-bs-target="#modalEdit<?= $row['id_jadwal'] ?>">
<i class="fas fa-edit"></i>
</button>

<a href="?hapus=<?= $row['id_jadwal'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Yakin hapus jadwal ini?')">
<i class="fas fa-trash"></i>
</a>

</td>
</tr>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit<?= $row['id_jadwal'] ?>">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Edit Jadwal</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">

<input type="hidden" name="id_jadwal"
value="<?= $row['id_jadwal'] ?>">

<select name="id_siswa"
class="form-control mb-2" required>
<?php
$siswa2 = mysqli_query($koneksi,"SELECT * FROM siswa");
while($s=mysqli_fetch_assoc($siswa2)){
$selected = ($s['id_siswa']==$row['id_siswa']) ? "selected" : "";
echo "<option value='{$s['id_siswa']}' $selected>{$s['nama']}</option> - jadwal.php:219";
}
?>
</select>

<input type="date"
name="tanggal"
class="form-control mb-2"
value="<?= $row['tanggal'] ?>" required>

<input type="time"
name="jam"
class="form-control mb-2"
value="<?= $row['jam'] ?>" required>

<input type="text"
name="keterangan"
class="form-control mb-2"
value="<?= htmlspecialchars($row['keterangan']) ?>">

</div>
<div class="modal-footer">
<button type="submit"
name="update"
class="btn btn-warning">
Update
</button>
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
<h5>Tambah Jadwal Konseling</h5>
<button type="button"
class="btn-close"
data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<select name="id_siswa"
class="form-control mb-2"
required>
<option value="">Pilih Siswa</option>
<?php
$siswa3 = mysqli_query($koneksi,"SELECT * FROM siswa");
while($s=mysqli_fetch_assoc($siswa3)){
echo "<option value='{$s['id_siswa']}'>{$s['nama']}</option> - jadwal.php:283";
}
?>
</select>

<input type="date"
name="tanggal"
class="form-control mb-2"
required>

<input type="time"
name="jam"
class="form-control mb-2"
required>

<input type="text"
name="keterangan"
class="form-control mb-2"
placeholder="Keterangan">

</div>

<div class="modal-footer">
<button type="submit"
name="simpan"
class="btn btn-info text-white">
Simpan
</button>
</div>

</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>