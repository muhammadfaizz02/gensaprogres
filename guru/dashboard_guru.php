<?php
session_start();
include '../config.php';

// Pastikan pengguna sudah login dan memiliki role guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
  header('Location: ../login.php');
  exit;
}

// Ambil ID user dari session
$user_id = $_SESSION['user_id'];

// Ambil data guru dari database
$query_user = mysqli_query($conn, "SELECT nama_lengkap, foto FROM users WHERE id = '$user_id'");
if (mysqli_num_rows($query_user) > 0) {
  $user = mysqli_fetch_assoc($query_user);
} else {
  die("Error: Data guru tidak ditemukan!");
}

// Tentukan path foto
$foto_path = (!empty($user['foto']) && file_exists("../" . $user['foto'])) ? "../" . $user['foto'] : "../uploads/default-avatar.png";

// Ambil data absensi berdasarkan hari
$query_absensi = mysqli_query($conn, "
  SELECT DAYNAME(tanggal_waktu) AS hari, COUNT(*) AS jumlah_kehadiran
  FROM daftar_hadir
  WHERE guru_id = '$user_id' AND status_approval = 'approved'
  GROUP BY hari
");

$kehadiran_per_hari = [
  'Monday' => 0,
  'Tuesday' => 0,
  'Wednesday' => 0,
  'Thursday' => 0,
  'Friday' => 0,
  'Saturday' => 0
];

// Isi data ke dalam array
while ($row = mysqli_fetch_assoc($query_absensi)) {
  $kehadiran_per_hari[$row['hari']] = (int)$row['jumlah_kehadiran'];
}

// Tentukan halaman yang sedang aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Guru</title>
  <link rel="stylesheet" href="../css/guru/dashboard_guru.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>

  </style>
</head>

<body>
  <!-- Tombol Menu Toggle -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="profile">
      <img src="<?= htmlspecialchars($foto_path); ?>" alt="Foto Profil">
      <h3><?= htmlspecialchars($user['nama_lengkap']); ?></h3>
    </div>
    <a href="dashboard_guru.php" class="<?= $current_page == 'dashboard_guru.php' ? 'active' : ''; ?>">Dashboard</a>
    <a href="daftar_hadir.php" class="<?= $current_page == 'daftar_hadir.php' ? 'active' : ''; ?>">Daftar Hadir</a>
    <a href="input_nilai.php" class="<?= $current_page == 'input_nilai.php' ? 'active' : ''; ?>">Input Nilai</a>
    <a href="absensi_siswa.php" class="<?= $current_page == 'absensi_siswa.php' ? 'active' : ''; ?>">Absensi Siswa</a>
    <a href="administrasi.php" class="<?= $current_page == 'administrasi.php' ? 'active' : ''; ?>">Administrasi</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <!-- Konten -->
  <div class="content">
    <h2>Selamat datang di Dashboard Guru</h2>
    <p>Gunakan menu di samping untuk mengelola nilai, daftar hadir, dan administrasi.</p>

    <h3>Grafik Kehadiran Mingguan</h3>
    <canvas id="attendanceChart"></canvas>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }

    // Data kehadiran
    const kehadiranData = <?= json_encode(array_values($kehadiran_per_hari)); ?>;
    const hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    // Konfigurasi chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: hari,
        datasets: [{
          label: 'Jumlah Kehadiran',
          data: kehadiranData,
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: false,
            suggestedMin: 1,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
  </script>
</body>

</html>