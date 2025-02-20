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

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    thead th {
      background-color: #fbb117;
      color: white;
      text-align: left;
      padding: 12px;
      font-weight: bold;
    }

    tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tbody tr:nth-child(odd) {
      background-color: #ffffff;
    }

    tbody tr:hover {
      background-color: #ffe5b4;
      cursor: pointer;
    }

    td,
    th {
      padding: 12px;
      border-bottom: 1px solid #ddd;
    }

    td {
      font-size: 14px;
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

      table {
        font-size: 12px;
      }

      td,
      th {
        padding: 8px;
      }
    }


    /* Tambahan CSS */
    table {
      width: 100%;
      border-collapse: collapse;
    }

    table,
    th,
    td {
      border: 1px solid #ddd;
    }

    th,
    td {
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }
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

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }
  </script>
</body>

</html>