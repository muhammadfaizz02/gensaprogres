<?php
session_start();
include '../config.php';

// Cek apakah pengguna sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login.php');
  exit;
}

// Ambil daftar pengguna yang belum disetujui
$query_users = mysqli_query($conn, "SELECT * FROM users WHERE is_approved = 0");
$pending_users = mysqli_fetch_all($query_users, MYSQLI_ASSOC);

// Proses persetujuan atau penolakan user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_POST['user_id'];
  if (isset($_POST['approve'])) {
    mysqli_query($conn, "UPDATE users SET is_approved = 1 WHERE id = '$user_id'");
  } elseif (isset($_POST['reject'])) {
    mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'");
  }
  header('Location: acc_user.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ACC User</title>
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
    }

    .menu-toggle:hover {
      background-color: brown;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: #f9f9f9;
      border-radius: 8px;
      overflow: hidden;
    }

    th,
    td {
      padding: 8px 10px;
      /* Mengurangi padding agar lebih ramping */
      text-align: left;
      border-bottom: 1px solid #ddd;
      font-size: 14px;
      /* Mengecilkan font */
    }

    th {
      background-color: #fbb117;
      color: white;
    }

    tr:hover {
      background-color: #fef3df;
    }

    .approve-btn,
    .reject-btn {
      padding: 8px 12px;
      /* Ukuran padding disesuaikan */
      font-size: 12px;
      /* Ukuran font standar */
      font-weight: bold;
      /* Menambahkan font tebal */
      width: 100px;
      /* Lebar tombol seragam */
      border-radius: 5px;
      /* Memberikan border radius agar seragam */
      border: none;
      /* Menghapus border default */
      cursor: pointer;
      text-transform: uppercase;
    }

    .approve-btn {
      background-color: #28a745;
      color: white;
      transition: background-color 0.3s;
    }

    .approve-btn:hover {
      background-color: #218838;
      /* Warna hijau lebih gelap saat hover */
    }

    .reject-btn {
      background-color: #dc3545;
      color: white;
      transition: background-color 0.3s;
    }

    .reject-btn:hover {
      background-color: #c82333;
      /* Warna merah lebih gelap saat hover */
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
        font-size: 12px;
        padding: 6px 8px;
        /* Padding lebih kecil */
      }

      .approve-btn,
      .reject-btn {
        font-size: 10px;
        padding: 4px 6px;
        width: 80px;
        /* Lebar tombol disesuaikan */
      }

      .action-buttons {
        display: flex;
        flex-direction: column;
        /* Atur tombol vertikal */
        gap: 5px;
        /* Jarak antar tombol */
      }
    }
  </style>
</head>

<body>
  <!-- Tombol Menu Toggle -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="acc_user.php" class="active">ACC User</a>
    <a href="input_guru.php">Input User Guru</a>
    <a href="input_nilai.php">Input Nilai</a>
    <a href="acc_absen_guru.php">ACC Absen Guru</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <!-- Konten -->
  <div class="content">
    <h2>ACC User</h2>
    <table>
      <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Aksi</th>
      </tr>
      <?php foreach ($pending_users as $user) : ?>
        <tr>
          <td><?= htmlspecialchars($user['nama_lengkap']); ?></td>
          <td><?= htmlspecialchars($user['email']); ?></td>
          <td>
            <div class="action-buttons">
              <form method="POST" style="display: inline;" onsubmit="return confirmApprove('<?= htmlspecialchars($user['nama_lengkap']); ?>')">
                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                <button type="submit" name="approve" class="approve-btn">Setujui</button>
              </form>
              <form method="POST" style="display: inline;" onsubmit="return confirmReject('<?= htmlspecialchars($user['nama_lengkap']); ?>')">
                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                <button type="submit" name="reject" class="reject-btn">Tolak</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <script>
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('active');
    }
    // Fungsi konfirmasi untuk persetujuan
    function confirmApprove(nama) {
      return confirm(`Yakin ingin menyetujui akun ${nama}?`);
    }

    // Fungsi konfirmasi untuk penolakan
    function confirmReject(nama) {
      return confirm(`Yakin ingin menolak akun ${nama}?`);
    }
  </script>
</body>

</html>