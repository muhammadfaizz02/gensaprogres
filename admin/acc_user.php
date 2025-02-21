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
  <link rel="stylesheet" href="../css/admin/acc_user.css">
  <style>

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

  <script src="../js/admin/acc_user.js">

  </script>
</body>

</html>