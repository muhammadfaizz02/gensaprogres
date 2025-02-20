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

    .table-container {
      width: 100%;
      overflow-x: auto;
      border-radius: 6px;
      box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #ffffff;
      border-radius: 6px;
      overflow: hidden;
    }

    th,
    td {
      padding: 10px 8px;
      /* Mengurangi padding */
      text-align: left;
      border-bottom: 1px solid #ddd;
      font-size: 14px;
      /* Mengecilkan font */
    }

    th {
      background-color: #fbb117;
      color: white;
      font-size: 14px;
      /* Font lebih kecil */
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #fef3df;
    }

    td img {
      width: 60px;
      /* Lebih kecil */
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #ddd;
    }

    /* Tombol Styling */
    button {
      padding: 6px 12px;
      /* Lebih kecil */
      font-size: 12px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
    }

    .approve {
      background-color: #28a745;
      color: white;
    }

    .approve:hover {
      background-color: #218838;
    }

    .reject {
      background-color: #dc3545;
      color: white;
    }

    .reject:hover {
      background-color: #c82333;
    }

    /* Form Styling */
    form {
      display: inline-block;
      margin: 0 4px;
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

      th,
      td {
        padding: 8px 6px;
        font-size: 12px;
      }

      td img {
        width: 50px;
        /* Ukuran lebih kecil di HP */
        height: 50px;
      }

      button {
        padding: 4px 8px;
        font-size: 10px;
      }
    }
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

  <script>
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('active');
    }

    // Fungsi konfirmasi untuk menyetujui
    function confirmApprove(namaGuru) {
      return confirm(`Apakah Anda yakin akan menyetujui absensi guru ${namaGuru}?`);
    }

    // Fungsi konfirmasi untuk menolak
    function confirmReject(namaGuru) {
      return confirm(`Apakah Anda yakin akan menolak absensi guru ${namaGuru}?`);
    }

    // Fungsi untuk menampilkan modal gambar
    function showImageModal(imageSrc) {
      document.getElementById('modalImage').src = imageSrc;
      document.getElementById('imageModal').style.display = 'flex';
    }

    // Fungsi untuk menutup modal gambar
    function closeImageModal() {
      document.getElementById('imageModal').style.display = 'none';
    }
  </script>
</body>