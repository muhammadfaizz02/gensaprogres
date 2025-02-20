<?php
session_start();
include '../config.php';

// Pastikan hanya guru yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
  header('Location: ../login.php');
  exit;
}

// Ambil data form
$guru_id = $_SESSION['user_id'];
$tanggal_waktu = date('Y-m-d H:i:s'); // Ambil waktu saat ini
$status_kehadiran = $_POST['status_kehadiran'];
$lokasi = $_POST['lokasi'];
$foto_absensi = '';

// Proses upload foto
if (!empty($_FILES['foto_absen']['name'])) {
  $target_dir = "../uploads/";
  $foto_absensi = $target_dir . basename($_FILES['foto_absen']['name']);

  // Cek apakah direktori "uploads" sudah ada, jika belum buat direktori
  if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
  }

  // Upload file ke server
  if (!move_uploaded_file($_FILES['foto_absen']['tmp_name'], $foto_absensi)) {
    echo "<script>alert('Gagal mengupload foto absensi.'); window.location.href = 'daftar_hadir.php';</script>";
    exit;
  }
}

// Simpan data ke database
$stmt = $conn->prepare("INSERT INTO daftar_hadir (guru_id, tanggal_waktu, status_kehadiran, foto_absensi, lokasi) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $guru_id, $tanggal_waktu, $status_kehadiran, $foto_absensi, $lokasi);

if ($stmt->execute()) {
  echo "<script>alert('Absensi berhasil disimpan!'); window.location.href = 'daftar_hadir.php';</script>";
} else {
  echo "<script>alert('Terjadi kesalahan saat menyimpan absensi.'); window.location.href = 'daftar_hadir.php';</script>";
}

$stmt->close();
$conn->close();
