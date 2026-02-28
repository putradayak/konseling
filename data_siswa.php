<?php
session_start();
include "config/koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

/* ================= TAMBAH ================= */
if (isset($_POST['simpan'])) {

    $nis     = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas   = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_hp   = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $ortu    = mysqli_real_escape_string($koneksi, $_POST['nama_ortu']);

    $cek = mysqli_query($koneksi,"SELECT * FROM siswa WHERE nis='$nis'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIS sudah terdaftar!');</script> - data_siswa.php:23";
    } else {
        mysqli_query($koneksi,"INSERT INTO siswa 
        (nis,nama,kelas,jurusan,alamat,no_hp,nama_ortu)
        VALUES
        ('$nis','$nama','$kelas','$jurusan','$alamat','$no_hp','$ortu')");
        echo "<script>window.location='';</script> - data_siswa.php:29";
        exit;
    }
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {

    $id      = $_POST['id'];
    $nama    = $_POST['nama'];
    $kelas   = $_POST['kelas'];
    $jurusan = $_POST['jurusan'];
    $alamat  = $_POST['alamat'];
    $no_hp   = $_POST['no_hp'];
    $ortu    = $_POST['nama_ortu'];

    mysqli_query($koneksi,"UPDATE siswa SET
        nama='$nama',
        kelas='$kelas',
        jurusan='$jurusan',
        alamat='$alamat',
        no_hp='$no_hp',
        nama_ortu='$ortu'
        WHERE id_siswa='$id'
    ");

    echo "<script>window.location='';</script> - data_siswa.php:55";
    exit;
}

/* ================= DELETE ================= */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi,"DELETE FROM siswa WHERE id_siswa='$id'");
    echo "<script>window.location='';</script> - data_siswa.php:63";
    exit;
}

/* ================= DATA ================= */
$data = mysqli_query($koneksi,"SELECT * FROM siswa ORDER BY id_siswa DESC");
$total = mysqli_num_rows($data);
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Siswa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.sidebar {
    height:100vh;
    width:220px;
    position:fixed;
    background:linear-gradient(180deg,#4e73df,#224abe);
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
}
.table thead { background:#4e73df; color:white; }
.btn-modern { border-radius:20px; }
</style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fas fa-school"></i> E-Konseling</h4>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
</div>

<div class="content">
<h3 class="mb-4">📘 Data Siswa</h3>

<div class="row mb-4">
<div class="col-md-4">
<div class="card card-stat bg-primary p-4">
<h5>Total Siswa</h5>
<h2><?= $total ?></h2>
</div>
</div>
</div>

<div class="mb-3">
<button class="btn btn-success btn-modern" data-bs-toggle="modal" data-bs-target="#modalTambah">
<i class="fas fa-plus"></i> Tambah
</button>
</div>

<div class="card shadow-sm">
<div class="card-body table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>No</th>
<th>NIS</th>
<th>Nama</th>
<th>Kelas</th>
<th>Jurusan</th>
<th>No HP</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php $no=1; while($row=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $no++ ?></td>
<td><?= $row['nis'] ?></td>
<td><?= $row['nama'] ?></td>
<td><?= $row['kelas'] ?></td>
<td><?= $row['jurusan'] ?></td>
<td><?= $row['no_hp'] ?></td>
<td>
<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id_siswa'] ?>">
<i class="fas fa-edit"></i>
</button>

<a href="?hapus=<?= $row['id_siswa'] ?>" 
class="btn btn-sm btn-danger"
onclick="return confirm('Yakin ingin menghapus data ini?')">
<i class="fas fa-trash"></i>
</a>
</td>
</tr>

<!-- Modal Edit -->
<div class="modal fade" id="edit<?= $row['id_siswa'] ?>">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="POST">
<input type="hidden" name="id" value="<?= $row['id_siswa'] ?>">
<div class="modal-header">
<h5>Edit Data</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row">
<div class="col-md-6 mb-2"><input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>"></div>
<div class="col-md-6 mb-2"><input type="text" name="kelas" class="form-control" value="<?= $row['kelas'] ?>"></div>
<div class="col-md-6 mb-2"><input type="text" name="jurusan" class="form-control" value="<?= $row['jurusan'] ?>"></div>
<div class="col-md-6 mb-2"><input type="text" name="no_hp" class="form-control" value="<?= $row['no_hp'] ?>"></div>
<div class="col-md-12 mb-2"><input type="text" name="alamat" class="form-control" value="<?= $row['alamat'] ?>"></div>
<div class="col-md-12 mb-2"><input type="text" name="nama_ortu" class="form-control" value="<?= $row['nama_ortu'] ?>"></div>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="POST">
<div class="modal-header">
<h5>Tambah Siswa</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row">
<div class="col-md-6 mb-2"><input type="text" name="nis" class="form-control" placeholder="NIS" required></div>
<div class="col-md-6 mb-2"><input type="text" name="nama" class="form-control" placeholder="Nama" required></div>
<div class="col-md-6 mb-2"><input type="text" name="kelas" class="form-control" placeholder="Kelas"></div>
<div class="col-md-6 mb-2"><input type="text" name="jurusan" class="form-control" placeholder="Jurusan"></div>
<div class="col-md-6 mb-2"><input type="text" name="no_hp" class="form-control" placeholder="No HP"></div>
<div class="col-md-6 mb-2"><input type="text" name="alamat" class="form-control" placeholder="Alamat"></div>
<div class="col-md-12 mb-2"><input type="text" name="nama_ortu" class="form-control" placeholder="Nama Orang Tua"></div>
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