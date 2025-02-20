<?php
session_start();
include '../config.php';

// Cek apakah user login dan memiliki role 'guru'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
  header('Location: ../login.php');
  exit;
}

// Ambil data guru untuk menampilkan foto dan nama
$user_id = $_SESSION['user_id'];
$query_user = mysqli_query($conn, "SELECT nama_lengkap, foto FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query_user);
$foto_path = (!empty($user['foto']) && file_exists("../" . $user['foto'])) ? "../" . $user['foto'] : "../uploads/default-avatar.png";

// Ambil data jenjang pendidikan dari database
$query_jenjang = mysqli_query($conn, "SELECT DISTINCT jenjang_pendidikan FROM users WHERE jenjang_pendidikan IS NOT NULL");
$jenjang_pendidikan = mysqli_fetch_all($query_jenjang, MYSQLI_ASSOC);

// Proses input nilai
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siswa_id'], $_POST['mata_pelajaran'], $_POST['nilai'])) {
  $jenjang = mysqli_real_escape_string($conn, $_POST['jenjang_pendidikan']);
  $siswa_id = mysqli_real_escape_string($conn, $_POST['siswa_id']);
  $mata_pelajaran = mysqli_real_escape_string($conn, $_POST['mata_pelajaran']);
  $nilai = mysqli_real_escape_string($conn, $_POST['nilai']);

  $sql = "INSERT INTO nilai_pelajaran (siswa_id, mata_pelajaran, nilai, jenjang_pendidikan) 
            VALUES ('$siswa_id', '$mata_pelajaran', '$nilai', '$jenjang')";

  if (mysqli_query($conn, $sql)) {
    echo "<p style='color: green;'>✅ Nilai berhasil ditambahkan!</p>";
  } else {
    echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
  }
}

// Ambil daftar siswa berdasarkan jenjang yang dipilih
$siswa = [];
$selected_jenjang = isset($_POST['jenjang_pendidikan']) ? mysqli_real_escape_string($conn, $_POST['jenjang_pendidikan']) : "";

if (!empty($selected_jenjang)) {
  $query_siswa = mysqli_query($conn, "SELECT * FROM users WHERE jenjang_pendidikan = '$selected_jenjang' AND role = 'pelajar'");
  $siswa = mysqli_fetch_all($query_siswa, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Input Nilai</title>
  <link rel="stylesheet" href="../css/">
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

    .content form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      /* Batasi lebar form */
    }

    .content label {
      font-weight: bold;
      font-size: 16px;
      margin-bottom: 5px;
    }

    .content select,
    .content input[type="text"],
    .content input[type="number"] {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 5px;
      outline: none;
      width: 100%;
      box-sizing: border-box;
    }

    .content select:focus,
    .content input[type="text"]:focus,
    .content input[type="number"]:focus {
      border-color: #fbb117;
    }

    .content button {
      padding: 12px 20px;
      font-size: 16px;
      font-weight: bold;
      color: white;
      background-color: #fbb117;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
      width: 100%;
      /* Buat tombol memenuhi lebar form */
    }

    .content button:hover {
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

      .content form {
        padding: 15px;
      }

      .content select,
      .content input[type="text"],
      .content input[type="number"],
      .content button {
        font-size: 14px;
      }
    }
  </style>
</head>

<body>
  <!-- Button to toggle sidebar -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <div class="sidebar" id="sidebar">
    <div class="profile">
      <img src="<?= htmlspecialchars($foto_path); ?>" alt="Foto Profil">
      <h3><?= htmlspecialchars($user['nama_lengkap']); ?></h3>
    </div>
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <a href="dashboard_guru.php" class="<?= $current_page == 'dashboard_guru.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="daftar_hadir.php" class="<?= $current_page == 'daftar_hadir.php' ? 'active' : '' ?>">Daftar Hadir</a>
    <a href="input_nilai.php" class="<?= $current_page == 'input_nilai.php' ? 'active' : '' ?>">Input Nilai</a>
    <a href="absensi_siswa.php" class="<?= $current_page == 'absensi_siswa.php' ? 'active' : '' ?>">Absensi Siswa</a>
    <a href="administrasi.php" class="<?= $current_page == 'administrasi.php' ? 'active' : '' ?>">Administrasi</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <div class="content">
    <h2>Input Nilai Pelajar</h2>

    <form method="POST">
      <label>Pilih Jenjang Pendidikan:</label>
      <select name="jenjang_pendidikan" onchange="this.form.submit()">
        <option value="">Pilih Jenjang</option>
        <?php foreach ($jenjang_pendidikan as $row): ?>
          <option value="<?= $row['jenjang_pendidikan']; ?>" <?= ($selected_jenjang == $row['jenjang_pendidikan']) ? 'selected' : '' ?>>
            <?= $row['jenjang_pendidikan']; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php if (!empty($siswa)): ?>
      <form method="POST">
        <input type="hidden" name="jenjang_pendidikan" value="<?= $selected_jenjang; ?>">

        <label>Pilih Siswa:</label>
        <select name="siswa_id" required>
          <option value="">Pilih Siswa</option>
          <?php foreach ($siswa as $s): ?>
            <option value="<?= $s['id']; ?>"><?= $s['nama_lengkap']; ?> (<?= $s['kelas']; ?>)</option>
          <?php endforeach; ?>
        </select>

        <label>Mata Pelajaran:</label>
        <input type="text" name="mata_pelajaran" required>

        <label>Nilai:</label>
        <input type="number" name="nilai" required>

        <button type="submit">Tambahkan Nilai</button>
      </form>
    <?php elseif (!empty($selected_jenjang)): ?>
      <p>Tidak ada siswa ditemukan untuk jenjang ini.</p>
    <?php endif; ?>
  </div>

  <script>
    function toggleSidebar() {
      var sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("show");
    }
  </script>
</body>

</html>