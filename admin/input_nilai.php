<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login.php');
  exit;
}

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
  <link rel="stylesheet" href="../style.css">
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

    form {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      margin: 20px auto;
    }

    /* Judul form */
    h2 {
      text-align: center;
      color: #333;
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    /* Label form */
    label {
      display: block;
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 5px;
      color: #333;
    }

    /* Input dan select */
    input[type="text"],
    input[type="number"],
    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      box-sizing: border-box;
      transition: border-color 0.3s;
    }

    /* Efek hover/focus */
    input[type="text"]:focus,
    input[type="number"]:focus,
    select:focus {
      border-color: #fbb117;
      outline: none;
    }

    /* Tombol submit */
    form button {
      background-color: #fbb117;
      color: white;
      font-size: 16px;
      font-weight: bold;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease, transform 0.1s ease;
    }

    /* Efek hover pada tombol */
    form button:hover {
      background-color: brown;
    }

    /* Efek klik */
    form button:active {
      transform: scale(0.98);
    }

    /* Pesan berhasil dan pesan error */
    p {
      text-align: center;
      font-size: 16px;
      margin-top: 20px;
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
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
</head>

<body>
  <!-- Button to toggle sidebar -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <div class="sidebar" id="sidebar">
    <h2>Admin Panel</h2>

    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <a href="admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="acc_user.php" class="<?= $current_page == 'acc_user.php' ? 'active' : '' ?>">ACC User</a>
    <a href="input_guru.php" class="<?= $current_page == 'input_guru.php' ? 'active' : '' ?>">Input User Guru</a>
    <a href="input_nilai.php" class="<?= $current_page == 'input_nilai.php' ? 'active' : '' ?>">Input Nilai</a>
    <a href="acc_absen_guru.php" class="<?= $current_page == 'acc_absen_guru.php' ? 'active' : '' ?>">ACC Absen Guru</a>
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
      document.querySelector('.sidebar').classList.toggle('active');
    }
  </script>
</body>

</html>