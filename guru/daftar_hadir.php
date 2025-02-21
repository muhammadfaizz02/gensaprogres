<?php
session_start();
include '../config.php';

// Cek apakah user login dan memiliki role 'guru'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
  header('Location: ../login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT nama_lengkap, foto FROM users WHERE id = '$user_id'");

// Pastikan data user ditemukan
if ($query && mysqli_num_rows($query) > 0) {
  $user = mysqli_fetch_assoc($query);
} else {
  $user = [
    'nama_lengkap' => 'Guru Tidak Dikenal',
    'foto' => 'uploads/default.png'
  ];
}

// Cek apakah foto tersedia, jika tidak gunakan foto default
$foto_path = (!empty($user['foto'])) ? "../" . $user['foto'] : "../uploads/default.png";

// Proses form absensi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $status_kehadiran = $_POST['status_kehadiran'];
  $tanggal_waktu = date('Y-m-d H:i:s');
  $lokasi = $_POST['lokasi'];
  $foto_absensi = '';

  // Upload foto
  if (!empty($_FILES['foto_absen']['name'])) {
    $target_dir = "../uploads/";
    $foto_absensi = $target_dir . basename($_FILES['foto_absen']['name']);
    move_uploaded_file($_FILES['foto_absen']['tmp_name'], $foto_absensi);
  }

  // Simpan ke database
  $stmt = $conn->prepare("INSERT INTO daftar_hadir (guru_id, tanggal_waktu, status_kehadiran, foto_absensi, lokasi) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $user_id, $tanggal_waktu, $status_kehadiran, $foto_absensi, $lokasi);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Absensi berhasil disimpan!'); window.location.href = 'daftar_hadir.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Hadir</title>
  <link rel="stylesheet" href="../css/guru/daftar_hadir.css">
  <style>

  </style>
</head>

<body>
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <div class="dashboard-container">
    <div class="sidebar" id="sidebar">
      <div class="profile">
        <img src="<?= $foto_path; ?>" alt="Foto Profil">
        <h3><?= htmlspecialchars($user['nama_lengkap']); ?></h3>
      </div>
      <a href="dashboard_guru.php">Dashboard</a>
      <a href="daftar_hadir.php" class="active">Daftar Hadir</a>
      <a href="input_nilai.php">Input Nilai</a>
      <a href="absensi_siswa.php">Absensi Siswa</a>
      <a href="administrasi.php">Administrasi</a>
      <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
    </div>

    <div class="content">
      <h2>Form Daftar Hadir</h2>
      <form action="daftar_hadir.php" method="post" enctype="multipart/form-data">
        <div>
          <label for="tanggal-absen">Tanggal/Waktu Absen</label>
          <input type="datetime-local" id="tanggal-absen" name="tanggal_absen" readonly>
        </div>

        <div>
          <label for="status-kehadiran">Status Kehadiran</label>
          <select id="status-kehadiran" name="status_kehadiran" required>
            <option value="Hadir">Hadir</option>
            <option value="Tidak Hadir">Tidak Hadir</option>
            <option value="Izin">Izin</option>
          </select>
        </div>

        <div>
          <label for="foto-absen">Upload Foto Saat Mengajar</label>
          <input type="file" id="foto-absen" name="foto_absen" required accept="image/*" onchange="previewImage(event)">
          <img id="foto-preview" src="#" alt="Pratinjau Gambar" style="display: none; width: 200px; height: auto; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div>
          <label for="location-display">Lokasi</label>
          <input type="text" id="location-display" name="lokasi" readonly>
          <button type="button" onclick="openMap()">Lihat di Map</button>
        </div>

        <button type="submit">Kirim Daftar Hadir</button>
      </form>
    </div>
  </div>

  <script src="../js/guru/daftar_hadir.js">

  </script>
</body>

</html>