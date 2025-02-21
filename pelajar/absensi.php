<?php
session_start();
include '../config.php';

// Cek apakah user sudah login dan memiliki role 'pelajar'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar') {
  header('Location: ../login.php');
  exit;
}

// Ambil ID user dari session
$user_id = $_SESSION['user_id'];

// Ambil data user untuk sidebar
$query_user = mysqli_query($conn, "SELECT nama_lengkap, foto FROM users WHERE id = '$user_id'");
if (mysqli_num_rows($query_user) > 0) {
  $user = mysqli_fetch_assoc($query_user);
} else {
  die("Error: Data user tidak ditemukan!");
}

// Tentukan path foto profil
$foto_path = (!empty($user['foto']) && file_exists("../" . $user['foto'])) ? "../" . $user['foto'] : "../uploads/default-avatar.png";

// Ambil data absensi dari tabel absensi_siswa
$query_absensi = mysqli_query($conn, "SELECT * FROM absensi_siswa WHERE id_siswa = '$user_id' ORDER BY tanggal DESC");
$absensi_data = mysqli_fetch_all($query_absensi, MYSQLI_ASSOC);

// Tentukan halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kehadiran Siswa</title>
  <link rel="stylesheet" href="../css/pelajar/absensi.css">

</head>

<body>
  <!-- Sidebar dan menu toggle -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>
  <div class="sidebar" id="sidebar">
    <div class="profile">
      <img src="<?= htmlspecialchars($foto_path); ?>" alt="Foto Profil">
      <h3><?= htmlspecialchars($user['nama_lengkap']); ?></h3>
    </div>
    <a href="dashboard_pelajar.php" class="<?= $current_page == 'dashboard_pelajar.php' ? 'active' : ''; ?>">Dashboard</a>
    <a href="nilai.php" class="<?= $current_page == 'nilai.php' ? 'active' : ''; ?>">Nilai</a>
    <a href="jadwal.php" class="<?= $current_page == 'jadwal.php' ? 'active' : ''; ?>">Jadwal</a>
    <a href="absensi.php" class="<?= $current_page == 'absensi.php' ? 'active' : ''; ?>">Absensi</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <!-- Konten halaman -->
  <div class="content">
    <h2>Absensi Siswa</h2>
    <?php if (!empty($absensi_data)): ?>
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Status Kehadiran</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($absensi_data as $absen): ?>
            <tr>
              <td><?= htmlspecialchars($absen['tanggal']); ?></td>
              <td><?= htmlspecialchars($absen['jam_mulai']); ?></td>
              <td><?= htmlspecialchars($absen['jam_selesai']); ?></td>
              <td><?= htmlspecialchars($absen['status_kehadiran']); ?></td>
              <td><?= htmlspecialchars($absen['keterangan']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Belum ada data absensi yang tersedia.</p>
    <?php endif; ?>
  </div>

  <script src="../js/pelajar/absensi.js">

  </script>
</body>

</html>