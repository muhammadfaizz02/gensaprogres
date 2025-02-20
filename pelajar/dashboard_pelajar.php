<?php
session_start();
include '../config.php';

// Pastikan pengguna sudah login dan memiliki role pelajar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelajar') {
  header('Location: ../login.php');
  exit;
}

// Ambil ID user dari session
$user_id = $_SESSION['user_id'];

// Ambil data pelajar dari database
$query_user = mysqli_query($conn, "SELECT nama_lengkap, foto FROM users WHERE id = '$user_id'");
if (mysqli_num_rows($query_user) > 0) {
  $user = mysqli_fetch_assoc($query_user);
} else {
  die("Error: Data pelajar tidak ditemukan!");
}

// Tentukan path foto
$foto_path = (!empty($user['foto']) && file_exists("../" . $user['foto'])) ? "../" . $user['foto'] : "../uploads/default-avatar.png";

// Ambil data nilai pelajaran dari database
$query_nilai = mysqli_query($conn, "
  SELECT mata_pelajaran, nilai
  FROM nilai_pelajaran
  WHERE siswa_id = '$user_id'
");

$nilai = [];
$mata_pelajaran = [];

// Isi data ke dalam array
while ($row = mysqli_fetch_assoc($query_nilai)) {
  $mata_pelajaran[] = $row['mata_pelajaran'];
  $nilai[] = (int)$row['nilai'];
}

// Tentukan halaman yang sedang aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pelajar</title>
  <link rel="stylesheet" href="../css/dashboard_pelajar.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      display: flex;
    }

    .profile {
      display: flex;
      align-items: center;
      border-bottom: 2px solid #ddd;
      /* Garis di bawah seluruh profil */
      margin-bottom: 20px;
      /* Memberikan jarak antara profil dan menu di bawah */
      padding-bottom: 20px;
      /* Menambahkan jarak sebelum garis */
    }

    .profile img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      aspect-ratio: 1;
      /* Memastikan gambar tetap bulat dan proporsional */
      border: 2px solid white;
      margin-right: 15px;
    }

    .profile h3 {
      font-size: 18px;
      margin: 0;
      word-wrap: break-word;
      white-space: normal;
      /* Izinkan teks turun ke baris berikutnya */
      line-height: 1.2;
    }


    ul {
      padding: 0;
      margin: 0;
      list-style-type: none;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #fbb117;
      color: white;
      padding: 20px;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease;
      transition: background-color 0.3s;
      height: calc(100vh - 20px);
    }

    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 10px;
      margin-bottom: 10px;
      background-color: #fbb117;
      text-align: center;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .sidebar a:hover {
      background-color: brown;
    }

    .sidebar a.active {
      background-color: brown;
    }

    .logout-button {
      background-color: red;
      text-align: center;
      margin-top: auto;
      /* Menjaga tombol logout di bagian bawah */
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
      text-decoration: none;
      color: white;
    }


    .content {
      margin-left: 300px;
      padding: 20px;
      width: calc(100% - 270px);
    }

    .menu-toggle {
      display: none;
      padding: 10px;
      background-color: #fbb117;
      color: white;
      border: none;
      border-radius: 5px;
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1000;
      cursor: pointer;
    }

    .menu-toggle:hover {
      background-color: brown;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .content {
        margin-left: 0;
        width: 100%;
      }

      .menu-toggle {
        display: block;
      }
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <a href="dashboard_pelajar.php" class="<?= $current_page == 'dashboard_pelajar.php' ? 'active' : ''; ?>">Dashboard</a>
    <a href="nilai.php" class="<?= $current_page == 'nilai.php' ? 'active' : ''; ?>">Nilai</a>
    <a href="jadwal.php" class="<?= $current_page == 'jadwal.php' ? 'active' : ''; ?>">Jadwal</a>
    <a href="absensi.php" class="<?= $current_page == 'absensi.php' ? 'active' : ''; ?>">Absensi</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <!-- Konten -->
  <div class="content">
    <h2>Selamat datang di Dashboard Pelajar</h2>
    <p>Gunakan menu di samping untuk melihat nilai, jadwal, dan absensi Anda.</p>

    <h3>Grafik Nilai Pelajaran</h3>
    <canvas id="nilaiChart"></canvas>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }

    // Data nilai pelajaran
    const nilaiData = <?= json_encode($nilai); ?>;
    const pelajaranLabels = <?= json_encode($mata_pelajaran); ?>;

    // Konfigurasi chart
    const ctx = document.getElementById('nilaiChart').getContext('2d');
    const nilaiChart = new Chart(ctx, {
      type: 'line', // Gunakan tipe line
      data: {
        labels: pelajaranLabels,
        datasets: [{
          label: 'Nilai Pelajaran',
          data: nilaiData,
          borderColor: 'rgba(54, 162, 235, 1)', // Warna garis biru
          backgroundColor: 'rgba(54, 162, 235, 0.2)', // Warna titik atau fill
          borderWidth: 2, // Ketebalan garis
          pointRadius: 5, // Ukuran titik lebih besar untuk menonjolkan setiap nilai
          pointBackgroundColor: 'rgba(54, 162, 235, 1)', // Warna titik
          fill: false, // Jangan isi area bawah grafik
          tension: 0 // Tension 0 untuk memastikan garis zig-zag (naik-turun tajam)
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true // Mulai dari nilai 0 pada sumbu Y
          }
        }
      }
    });
  </script>
</body>

</html>