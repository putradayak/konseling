<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* ================= TAMBAH ================= */
if (isset($_POST['simpan'])) {

    $id_siswa     = mysqli_real_escape_string($koneksi,$_POST['id_siswa']);
    $tanggal      = mysqli_real_escape_string($koneksi,$_POST['tanggal']);
    $jenis_kasus  = mysqli_real_escape_string($koneksi,$_POST['jenis_kasus']);
    $deskripsi    = mysqli_real_escape_string($koneksi,$_POST['deskripsi']);
    $solusi       = mysqli_real_escape_string($koneksi,$_POST['solusi']);
    $tindak_lanjut= mysqli_real_escape_string($koneksi,$_POST['tindak_lanjut']);
    $id_guru_bk   = $_SESSION['id_user'] ?? 1;

    mysqli_query($koneksi,"INSERT INTO konseling
    (id_siswa,tanggal,jenis_kasus,deskripsi,solusi,tindak_lanjut,id_guru_bk)
    VALUES
    ('$id_siswa','$tanggal','$jenis_kasus','$deskripsi','$solusi','$tindak_lanjut','$id_guru_bk')");

    header("Location: data_konseling.php");
    exit;
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $id_konseling = $_POST['id_konseling'];
    $tanggal      = $_POST['tanggal'];
    $jenis_kasus  = $_POST['jenis_kasus'];
    $deskripsi    = $_POST['deskripsi'];
    $solusi       = $_POST['solusi'];
    $tindak_lanjut= $_POST['tindak_lanjut'];

    mysqli_query($koneksi,"UPDATE konseling SET
        tanggal='$tanggal',
        jenis_kasus='$jenis_kasus',
        deskripsi='$deskripsi',
        solusi='$solusi',
        tindak_lanjut='$tindak_lanjut'
        WHERE id_konseling='$id_konseling'
    ");

    header("Location: data_konseling.php");
    exit;
}

/* ================= DELETE ================= */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM konseling WHERE id_konseling='$id'");
    header("Location: data_konseling.php");
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($koneksi,"
SELECT konseling.*, siswa.nama 
FROM konseling
LEFT JOIN siswa ON konseling.id_siswa = siswa.id_siswa
ORDER BY id_konseling DESC
");

$total = mysqli_num_rows(mysqli_query($koneksi,"SELECT * FROM konseling"));
$siswa = mysqli_query($koneksi,"SELECT * FROM siswa");
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Konseling</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.sidebar {
    height:100vh;
    width:220px;
    position:fixed;
    background:linear-gradient(180deg,#1cc88a,#13855c);
    color:white;
}
.sidebar h4 { padding:20px; }
.sidebar a {
    display:block;
    padding:12px 20px;
    color:white;
    text-decoration:none;
}
.sidebar a:hover { background:rgba(255,255,255,0.2); }
.content { margin-left:220px; padding:30px; }
.card-stat {
    border:none;
    border-radius:15px;
    color:white;
    animation:fadeIn 0.8s ease;
}
@keyframes fadeIn {
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}
.table thead { background:#1cc88a; color:white; }
.btn-modern { border-radius:20px; }
</style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fas fa-comments"></i> E-Konseling</h4>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
    <a href="data_konseling.php"><i class="fas fa-comment-dots"></i> Data Konseling</a>
</div>

<div class="content">

<h3 class="mb-4">💬 Data Konseling</h3>

<div class="row mb-4">
<div class="col-md-4">
<div class="card card-stat bg-success p-4">
<h5>Total Konseling</h5>
<h2><?= $total ?></h2>
</div>
</div>
</div>

<button class="btn btn-success btn-modern mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="fas fa-plus"></i> Tambah Konseling
</button>

<div class="card shadow-sm">
<div class="card-body table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>No</th>
<th>Nama Siswa</th>
<th>Tanggal</th>
<th>Jenis Kasus</th>
<th>Deskripsi</th>
<th>Solusi</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($row=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $row['nama'] ?></td>
<td><?= $row['tanggal'] ?></td>
<td><?= $row['jenis_kasus'] ?></td>
<td><?= $row['deskripsi'] ?></td>
<td><?= $row['solusi'] ?></td>
<td>
<button class="btn btn-sm btn-primary"
data-bs-toggle="modal"
data-bs-target="#edit<?= $row['id_konseling'] ?>">
<i class="fas fa-edit"></i>
</button>

<a href="?hapus=<?= $row['id_konseling'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Yakin hapus data ini?')">
<i class="fas fa-trash"></i>
</a>
</td>
</tr>

<!-- Modal Edit -->
<div class="modal fade" id="edit<?= $row['id_konseling'] ?>">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="POST">
<input type="hidden" name="id_konseling" value="<?= $row['id_konseling'] ?>">
<div class="modal-header">
<h5>Edit Konseling</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row">
<div class="col-md-6 mb-2">
<input type="date" name="tanggal" class="form-control"
value="<?= $row['tanggal'] ?>">
</div>
<div class="col-md-6 mb-2">
<input type="text" name="jenis_kasus" class="form-control"
value="<?= $row['jenis_kasus'] ?>">
</div>
<div class="col-md-12 mb-2">
<textarea name="deskripsi" class="form-control"><?= $row['deskripsi'] ?></textarea>
</div>
<div class="col-md-12 mb-2">
<textarea name="solusi" class="form-control"><?= $row['solusi'] ?></textarea>
</div>
<div class="col-md-12 mb-2">
<textarea name="tindak_lanjut" class="form-control"><?= $row['tindak_lanjut'] ?></textarea>
</div>
</div>
</div>
<div class="modal-footer">
<button type="submit" name="update" class="btn btn-primary">Update</button>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Tambah Konseling</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row">

<div class="col-md-6 mb-2">
<select name="id_siswa" class="form-control" required>
<option value="">Pilih Siswa</option>
<?php mysqli_data_seek($siswa,0); while($s=mysqli_fetch_assoc($siswa)){ ?>
<option value="<?= $s['id_siswa'] ?>"><?= $s['nama'] ?></option>
<?php } ?>
</select>
</div>

<div class="col-md-6 mb-2">
<input type="date" name="tanggal" class="form-control" required>
</div>

<div class="col-md-12 mb-2">
<input type="text" name="jenis_kasus" class="form-control" placeholder="Jenis Kasus">
</div>

<div class="col-md-12 mb-2">
<textarea name="deskripsi" class="form-control" placeholder="Deskripsi"></textarea>
</div>

<div class="col-md-12 mb-2">
<textarea name="solusi" class="form-control" placeholder="Solusi"></textarea>
</div>

<div class="col-md-12 mb-2">
<textarea name="tindak_lanjut" class="form-control" placeholder="Tindak Lanjut"></textarea>
</div>

</div>
</div>
<div class="modal-footer">
<button type="submit" name="simpan" class="btn btn-success">Simpan</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>