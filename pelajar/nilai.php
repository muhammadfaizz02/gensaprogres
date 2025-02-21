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
  SELECT kode_pelajaran, mata_pelajaran, sks, nilai
  FROM nilai_pelajaran
  WHERE siswa_id = '$user_id'
");

$nilai = [];
$total_sks = 0;
$total_nilai = 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transkrip Nilai</title>
  <link rel="stylesheet" href="../css/pelajar/nilai.css">
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
    <a href="dashboard_pelajar.php">Dashboard</a>
    <a href="nilai.php" class="active">Nilai</a>
    <a href="jadwal.php">Jadwal</a>
    <a href="absensi.php">Absensi</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <!-- Konten -->
  <div class="content">
    <h2>Transkrip Nilai</h2>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Pelajaran</th>
          <th>Nama Pelajaran</th>
          <th>SKS</th>
          <th>Nilai</th>
          <th>Huruf Mutu</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($query_nilai)) {
          $kode_pelajaran = htmlspecialchars($row['kode_pelajaran']);
          $mata_pelajaran = htmlspecialchars($row['mata_pelajaran']);
          $sks = (int)$row['sks'];
          $nilai = (int)$row['nilai'];
          $huruf_mutu = '';

          // Hitung huruf mutu berdasarkan nilai
          if ($nilai >= 85) {
            $huruf_mutu = 'A';
          } elseif ($nilai >= 70) {
            $huruf_mutu = 'B';
          } elseif ($nilai >= 55) {
            $huruf_mutu = 'C';
          } elseif ($nilai >= 40) {
            $huruf_mutu = 'D';
          } else {
            $huruf_mutu = 'E';
          }

          // Hitung total SKS dan nilai
          $total_sks += $sks;
          $total_nilai += $sks * $nilai;

          echo "<tr>";
          echo "<td>{$no}</td>";
          echo "<td>{$kode_pelajaran}</td>";
          echo "<td>{$mata_pelajaran}</td>";
          echo "<td>{$sks}</td>";
          echo "<td>{$nilai}</td>";
          echo "<td>{$huruf_mutu}</td>";
          echo "</tr>";

          $no++;
        }
        ?>
      </tbody>
    </table>

    <h3>IPK</h3>
    <p>
      <?php
      if ($total_sks > 0) {
        $ipk = $total_nilai / $total_sks;
        echo "IPK Anda adalah: " . number_format($ipk, 2);
      } else {
        echo "Belum ada data nilai.";
      }
      ?>
    </p>
  </div>

  <script src="../js/pelajar/nilai.js">
  </script>
</body>

</html>