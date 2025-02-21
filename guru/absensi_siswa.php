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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['absen']) && is_array($_POST['absen'])) {
  $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
  $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
  $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);

  // Cek apakah tanggal, jam mulai, dan jam selesai terisi
  if (empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
    echo "<p style='color: red;'>❌ Harap isi semua field tanggal, jam mulai, dan jam selesai!</p>";
  } else {
    // Validasi tiap absensi siswa
    $error = false;
    foreach ($_POST['absen'] as $siswa_id => $data) {
      $status_kehadiran = isset($data['status']) ? $data['status'] : '';

      if (empty($status_kehadiran)) {
        echo "<p style='color: red;'>❌ Status kehadiran siswa dengan ID $siswa_id harus dipilih!</p>";
        $error = true;
      }
    }

    // Jika tidak ada error, lanjutkan proses penyimpanan
    if (!$error) {
      foreach ($_POST['absen'] as $siswa_id => $data) {
        $siswa_id = mysqli_real_escape_string($conn, $siswa_id);
        $status_kehadiran = mysqli_real_escape_string($conn, $data['status']);
        $keterangan = isset($data['keterangan']) ? mysqli_real_escape_string($conn, $data['keterangan']) : '';

        $cek_absen_query = "SELECT * FROM absensi_siswa WHERE id_siswa = '$siswa_id' AND tanggal = '$tanggal'";
        $cek_absen = mysqli_query($conn, $cek_absen_query);

        if (!$cek_absen) {
          die("Error pada query cek absensi: " . mysqli_error($conn) . "<br>Query: " . $cek_absen_query);
        }

        if (mysqli_num_rows($cek_absen) > 0) {
          $update_query = "UPDATE absensi_siswa SET status_kehadiran = '$status_kehadiran', jam_mulai = '$jam_mulai', jam_selesai = '$jam_selesai', keterangan = '$keterangan' WHERE id_siswa = '$siswa_id' AND tanggal = '$tanggal'";
          if (!mysqli_query($conn, $update_query)) {
            die("Error saat update absensi: " . mysqli_error($conn));
          }
        } else {
          $insert_query = "INSERT INTO absensi_siswa (id_siswa, tanggal, jam_mulai, jam_selesai, status_kehadiran, keterangan) VALUES ('$siswa_id', '$tanggal', '$jam_mulai', '$jam_selesai', '$status_kehadiran', '$keterangan')";
          if (!mysqli_query($conn, $insert_query)) {
            die("Error saat insert absensi: " . mysqli_error($conn));
          }
        }
      }
      echo "<p style='color: green;'>✅ Absensi berhasil disimpan!</p>";
    }
  }
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
  <link rel="stylesheet" href="../css/guru/absensi_siswa.css">
  <style>

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
        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d'); ?>" required>

        <label for="jam_mulai">Jam Mulai:</label>
        <input type="time" name="jam_mulai" id="jam_mulai" value="<?= date('H:i'); ?>" required>

        <label for="jam_selesai">Jam Selesai:</label>
        <input type="time" name="jam_selesai" id="jam_selesai" required>

        <table border="1" cellspacing="0" cellpadding="5">
          <tr>
            <th>Nama Siswa</th>
            <th>Status Kehadiran</th>
            <th>Keterangan (Opsional)</th>
          </tr>
          <?php foreach ($siswa as $s): ?>
            <tr>
              <td><?= $s['nama_lengkap']; ?> (<?= $s['kelas']; ?>)</td>
              <td>
                <select name="absen[<?php echo $s['id']; ?>][status]" required>
                  <option value="">Pilih Status</option>
                  <option value="Hadir">Hadir</option>
                  <option value="Izin">Izin</option>
                  <option value="Sakit">Sakit</option>
                  <option value="Alfa">Alfa</option>
                </select>
              </td>

              <td>
                <input type="text" name="absen[<?= $s['id']; ?>][keterangan]" placeholder="Masukkan keterangan (opsional)">
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
        <button type="submit" style="margin-top: 10px;">Simpan Absensi</button>
      </form>
    <?php endif; ?>
  </div>

  <script src="../js/guru/absensi_siswa.js">

  </script>
</body>

</html>