<?php
session_start();
include '../config.php';

// Periksa apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login.php');
  exit;
}

// Ambil semua data absensi guru yang belum disetujui atau ditolak
$query = "SELECT daftar_hadir.id, users.nama_lengkap, daftar_hadir.tanggal_waktu, daftar_hadir.foto_absensi, daftar_hadir.status_kehadiran, daftar_hadir.lokasi 
          FROM daftar_hadir 
          JOIN users ON daftar_hadir.guru_id = users.id 
          WHERE daftar_hadir.status_approval IS NULL";

$result = mysqli_query($conn, $query);

// Cek jika query gagal
if (!$result) {
  die("Error saat mengambil data absensi: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ACC Absen Guru</title>
  <link rel="stylesheet" href="../css/admin/acc_absen_guru.css">
  <style>

  </style>
</head>

<body>
  <!-- Tombol Menu Toggle -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <a href="admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="acc_user.php" class="<?= $current_page == 'acc_user.php' ? 'active' : '' ?>">ACC User</a>
    <a href="input_guru.php" class="<?= $current_page == 'input_guru.php' ? 'active' : '' ?>">Input User Guru</a>
    <a href="input_nilai.php" class="<?= $current_page == 'input_nilai.php' ? 'active' : '' ?>">Input Nilai</a>
    <a href="acc_absen_guru.php" class="<?= $current_page == 'acc_absen_guru.php' ? 'active' : '' ?>">ACC Absen Guru</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <!-- Konten -->
  <div class="content">
    <h2>ACC Absen Guru</h2>
    <table class="table-container">
      <thead>
        <tr>
          <th>Nama Guru</th>
          <th>Tanggal/Waktu</th>
          <th>Foto Absen</th>
          <th>Status Kehadiran</th>
          <th>Lokasi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0) : ?>
          <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
              <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
              <td><?= htmlspecialchars($row['tanggal_waktu']); ?></td>
              <td>
                <img src="<?= htmlspecialchars($row['foto_absensi']); ?>" alt="Foto Absen" onclick="showImageModal('<?= htmlspecialchars($row['foto_absensi']); ?>')" style="cursor: pointer; width: 100px; height: auto;">
              </td>
              <td><?= htmlspecialchars($row['status_kehadiran']); ?></td>
              <td>
                <a href="https://www.google.com/maps?q=<?= urlencode($row['lokasi']); ?>" target="_blank" style="color: blue; text-decoration: underline;">
                  Buka Lokasi di Peta
                </a>
              </td>
              <td>
                <form action="proses_acc_absen.php" method="post" style="display: inline;" onsubmit="return confirmApprove('<?= htmlspecialchars($row['nama_lengkap']); ?>')">
                  <input type="hidden" name="absen_id" value="<?= $row['id']; ?>">
                  <button type="submit" name="action" value="approve" class="approve">Setujui</button>
                </form>
                <form action="proses_acc_absen.php" method="post" style="display: inline;" onsubmit="return confirmReject('<?= htmlspecialchars($row['nama_lengkap']); ?>')">
                  <input type="hidden" name="absen_id" value="<?= $row['id']; ?>">
                  <button type="submit" name="action" value="reject" class="reject">Tolak</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else : ?>
          <tr>
            <td colspan="6">Tidak ada data absensi yang perlu disetujui.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal untuk menampilkan gambar -->
  <div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
    <img id="modalImage" src="" alt="Foto Absen" style="max-width: 90%; max-height: 90%; border: 5px solid white; border-radius: 10px;">
    <button onclick="closeImageModal()" style="position: absolute; top: 20px; right: 20px; background-color: red; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">Tutup</button>
  </div>

  <script src="../js/admin/acc_absen_guru.js">

  </script>
</body>