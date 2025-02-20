<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $absen_id = $_POST['absen_id'];
  $action = $_POST['action'];

  // Set status approval berdasarkan aksi
  $status_approval = $action === 'approve' ? 'approved' : 'rejected';

  // Update status approval di tabel daftar_hadir
  $query = "UPDATE daftar_hadir SET status_approval = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("si", $status_approval, $absen_id);
  $stmt->execute();

  // Jika disetujui, tambahkan ke jumlah_mengajar guru
  if ($action === 'approve') {
    $query_update_user = "UPDATE users 
                          JOIN daftar_hadir ON users.id = daftar_hadir.guru_id 
                          SET users.jumlah_mengajar = users.jumlah_mengajar + 1 
                          WHERE daftar_hadir.id = ?";
    $stmt_update_user = $conn->prepare($query_update_user);
    $stmt_update_user->bind_param("i", $absen_id);
    $stmt_update_user->execute();
  }

  header('Location: acc_absen_guru.php');
  exit;
}
