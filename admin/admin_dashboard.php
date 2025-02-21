<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin/admin_dashboard.css">
  <style>

  </style>
</head>

<body>
  <!-- Button to toggle sidebar -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <div class="sidebar" id="sidebar">
    <h2>Admin Panel</h2>

    <?php
    // Dapatkan nama file halaman saat ini
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
    <h2>Selamat Datang, Admin</h2>
    <p>Pilih menu di sidebar untuk mengelola pengguna dan nilai pelajar.</p>

    <!-- Notifikasi jumlah user yang perlu di-ACC -->
    <?php
    include '../config.php';

    // Hitung jumlah user yang belum di-approve
    $query_pending_users = mysqli_query($conn, "SELECT COUNT(*) AS total_pending FROM users WHERE is_approved = 0");
    $result = mysqli_fetch_assoc($query_pending_users);
    $total_pending_users = $result['total_pending'];
    ?>

    <h3>Notifikasi</h3>
    <p>Ada <?= $total_pending_users; ?> pengguna yang perlu di-ACC. <a href="acc_user.php">Lihat dan ACC User</a></p>
  </div>

  <script src="../js/admin/admin_dashboard.js">

  </script>
</body>

</html>