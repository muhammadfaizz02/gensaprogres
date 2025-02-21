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
  <link rel="stylesheet" href="../css/admin/input_nilai.css">
  <style>

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

  <script src="../js/admin/input_nilai.js">

  </script>
</body>

</html>