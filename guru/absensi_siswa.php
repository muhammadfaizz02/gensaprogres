<?php
session_start();
include '../config.php';

// Cek apakah user login dan memiliki role 'guru'
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

// Ambil data jenjang pendidikan dari database
$query_jenjang = mysqli_query($conn, "SELECT DISTINCT jenjang_pendidikan FROM users WHERE jenjang_pendidikan IS NOT NULL");
$jenjang_pendidikan = mysqli_fetch_all($query_jenjang, MYSQLI_ASSOC);

// Ambil daftar siswa berdasarkan jenjang yang dipilih
$siswa = [];
$selected_jenjang = isset($_POST['jenjang_pendidikan']) ? mysqli_real_escape_string($conn, $_POST['jenjang_pendidikan']) : "";

if (!empty($selected_jenjang)) {
  $query_siswa = mysqli_query($conn, "SELECT * FROM users WHERE jenjang_pendidikan = '$selected_jenjang' AND role = 'pelajar'");
  $siswa = mysqli_fetch_all($query_siswa, MYSQLI_ASSOC);
}

// Proses absensi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['absen'])) {
  foreach ($_POST['absen'] as $siswa_id => $status_absensi) {
    $tanggal = date('Y-m-d');
    $siswa_id = mysqli_real_escape_string($conn, $siswa_id);
    $status_absensi = mysqli_real_escape_string($conn, $status_absensi);

    $cek_absen = mysqli_query($conn, "SELECT * FROM absensi WHERE siswa_id = '$siswa_id' AND tanggal = '$tanggal'");
    if (mysqli_num_rows($cek_absen) > 0) {
      mysqli_query($conn, "UPDATE absensi SET status_absensi = '$status_absensi' WHERE siswa_id = '$siswa_id' AND tanggal = '$tanggal'");
    } else {
      mysqli_query($conn, "INSERT INTO absensi (siswa_id, tanggal, status_absensi) VALUES ('$siswa_id', '$tanggal', '$status_absensi')");
    }
  }
  echo "<p style='color: green;'>✅ Absensi berhasil disimpan!</p>";
}

// Tentukan halaman yang sedang aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Absensi Siswa</title>
  <link rel="stylesheet" href="../css/dashboard_guru.css">
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
      margin-bottom: 20px;
      padding-bottom: 20px;
    }

    .profile img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      aspect-ratio: 1;
      border: 2px solid white;
      margin-right: 15px;
    }

    .profile h3 {
      font-size: 18px;
      margin: 0;
      word-wrap: break-word;
      white-space: normal;
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
      width: auto;
      height: auto;
    }

    .menu-toggle:hover {
      background-color: brown;
    }


    input[type="text"],
    input[type="file"],
    button {
      width: 100%;
      box-sizing: border-box;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th,
    table td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }

    button[type="submit"] {
      background-color: #fbb117;
      color: white;
      border: none;
      padding: 10px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
      width: 150px;
      margin-top: 20px;
      /* Jarak tombol dengan elemen lainnya */
    }

    button[type="submit"]:hover {
      background-color: brown;
    }

    /* Atur lebar dropdown */
    select[name="jenjang_pendidikan"] {
      width: 300px;
      padding: 5px;
      font-size: 16px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-sizing: border-box;
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
  </style>
</head>

<body>
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>
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

  <div class="content">
    <h2>Absensi Siswa</h2>
    <form method="POST">
      <label>Pilih Jenjang Pendidikan:</label>
      <select name="jenjang_pendidikan" onchange="this.form.submit()">
        <option value="">Pilih Jenjang</option>
        <?php foreach ($jenjang_pendidikan as $row): ?>
          <option value="<?= $row['jenjang_pendidikan']; ?>" <?= ($selected_jenjang == $row['jenjang_pendidikan']) ? 'selected' : '' ?>><?= $row['jenjang_pendidikan']; ?></option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php if (!empty($siswa)): ?>
      <form method="POST">
        <table border="1" cellspacing="0" cellpadding="5">
          <tr>
            <th>Nama Siswa</th>
            <th>Status Kehadiran</th>
          </tr>
          <?php foreach ($siswa as $s): ?>
            <tr>
              <td><?= $s['nama_lengkap']; ?> (<?= $s['kelas']; ?>)</td>
              <td>
                <select name="absen[<?= $s['id']; ?>]">
                  <option value="Hadir">Hadir</option>
                  <option value="Tidak Hadir">Tidak Hadir</option>
                  <option value="Izin">Izin</option>
                  <option value="Sakit">Sakit</option>
                </select>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
        <button type="submit">Simpan Absensi</button>
      </form>
    <?php elseif (!empty($selected_jenjang)): ?>
      <p>Tidak ada siswa ditemukan untuk jenjang ini.</p>
    <?php endif; ?>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }
  </script>
</body>

</html>