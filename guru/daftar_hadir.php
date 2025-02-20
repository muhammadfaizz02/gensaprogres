<?php
session_start();
include '../config.php';

// Cek apakah user login dan memiliki role 'guru'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'guru') {
  header('Location: ../login.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT nama_lengkap, foto FROM users WHERE id = '$user_id'");

// Pastikan data user ditemukan
if ($query && mysqli_num_rows($query) > 0) {
  $user = mysqli_fetch_assoc($query);
} else {
  $user = [
    'nama_lengkap' => 'Guru Tidak Dikenal',
    'foto' => 'uploads/default.png'
  ];
}

// Cek apakah foto tersedia, jika tidak gunakan foto default
$foto_path = (!empty($user['foto'])) ? "../" . $user['foto'] : "../uploads/default.png";

// Proses form absensi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $status_kehadiran = $_POST['status_kehadiran'];
  $tanggal_waktu = date('Y-m-d H:i:s');
  $lokasi = $_POST['lokasi'];
  $foto_absensi = '';

  // Upload foto
  if (!empty($_FILES['foto_absen']['name'])) {
    $target_dir = "../uploads/";
    $foto_absensi = $target_dir . basename($_FILES['foto_absen']['name']);
    move_uploaded_file($_FILES['foto_absen']['tmp_name'], $foto_absensi);
  }

  // Simpan ke database
  $stmt = $conn->prepare("INSERT INTO daftar_hadir (guru_id, tanggal_waktu, status_kehadiran, foto_absensi, lokasi) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $user_id, $tanggal_waktu, $status_kehadiran, $foto_absensi, $lokasi);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Absensi berhasil disimpan!'); window.location.href = 'daftar_hadir.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Hadir</title>
  <link rel="stylesheet" href="../css/dashboard_guru.css">
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
      transition: transform 0.3s ease, width 0.3s ease;
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

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      margin: 0 auto;
      width: 100%;
    }

    label {
      font-weight: bold;
      margin-bottom: 5px;
    }

    input[type="datetime-local"],
    input[type="text"],
    input[type="file"],
    select {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 5px;
      outline: none;
      box-sizing: border-box;
      transition: border-color 0.3s ease;
    }

    input:focus,
    select:focus {
      border-color: #fbb117;
    }

    /* Gaya tombol */
    button[type="submit"],
    button[type="button"] {
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button[type="submit"] {
      background-color: #fbb117;
    }

    button[type="submit"]:hover {
      background-color: brown;
    }

    button[type="button"] {
      background-color: green;
    }

    button[type="button"]:hover {
      background-color: #B2C248;
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

      form {
        padding: 15px;
        max-width: 85%;
        /* Sesuaikan lebar form agar lebih kecil */
      }
    }
  </style>
</head>

<body>
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <div class="dashboard-container">
    <div class="sidebar" id="sidebar">
      <div class="profile">
        <img src="<?= $foto_path; ?>" alt="Foto Profil">
        <h3><?= htmlspecialchars($user['nama_lengkap']); ?></h3>
      </div>
      <a href="dashboard_guru.php">Dashboard</a>
      <a href="daftar_hadir.php" class="active">Daftar Hadir</a>
      <a href="input_nilai.php">Input Nilai</a>
      <a href="absensi_siswa.php">Absensi Siswa</a>
      <a href="administrasi.php">Administrasi</a>
      <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
    </div>

    <div class="content">
      <h2>Form Daftar Hadir</h2>
      <form action="daftar_hadir.php" method="post" enctype="multipart/form-data">
        <div>
          <label for="tanggal-absen">Tanggal/Waktu Absen</label>
          <input type="datetime-local" id="tanggal-absen" name="tanggal_absen" readonly>
        </div>

        <div>
          <label for="status-kehadiran">Status Kehadiran</label>
          <select id="status-kehadiran" name="status_kehadiran" required>
            <option value="Hadir">Hadir</option>
            <option value="Tidak Hadir">Tidak Hadir</option>
            <option value="Izin">Izin</option>
          </select>
        </div>

        <div>
          <label for="foto-absen">Upload Foto Saat Mengajar</label>
          <input type="file" id="foto-absen" name="foto_absen" required accept="image/*" onchange="previewImage(event)">
          <img id="foto-preview" src="#" alt="Pratinjau Gambar" style="display: none; width: 200px; height: auto; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div>
          <label for="location-display">Lokasi</label>
          <input type="text" id="location-display" name="lokasi" readonly>
          <button type="button" onclick="openMap()">Lihat di Map</button>
        </div>

        <button type="submit">Kirim Daftar Hadir</button>
      </form>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }

    function previewImage(event) {
      var file = event.target.files[0]; // Ambil file yang dipilih
      if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
          // Tampilkan pratinjau gambar
          var imgPreview = document.getElementById('foto-preview');
          imgPreview.src = e.target.result;
          imgPreview.style.display = 'block'; // Tampilkan gambar pratinjau
        };
        reader.readAsDataURL(file); // Baca file sebagai Data URL
      }
    }

    function getCurrentLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
      } else {
        document.getElementById("location-display").value = "Geolocation tidak didukung oleh browser ini.";
      }
    }

    function showPosition(position) {
      let latitude = position.coords.latitude;
      let longitude = position.coords.longitude;

      // Format koordinat dalam format lat,long
      let coordinates = `${latitude},${longitude}`;

      // Isi input form lokasi dengan koordinat
      document.getElementById("location-display").value = coordinates;
    }

    function showError(error) {
      switch (error.code) {
        case error.PERMISSION_DENIED:
          document.getElementById("location-display").value = "Pengguna menolak permintaan lokasi.";
          break;
        case error.POSITION_UNAVAILABLE:
          document.getElementById("location-display").value = "Informasi lokasi tidak tersedia.";
          break;
        case error.TIMEOUT:
          document.getElementById("location-display").value = "Permintaan lokasi timeout.";
          break;
        case error.UNKNOWN_ERROR:
          document.getElementById("location-display").value = "Terjadi kesalahan yang tidak diketahui.";
          break;
      }
    }

    function openMap() {
      let coordinates = document.getElementById("location-display").value;
      if (!coordinates || coordinates.includes("tidak")) {
        alert("Lokasi tidak tersedia. Harap izinkan akses lokasi terlebih dahulu.");
        return;
      }

      // Buka lokasi di Google Maps
      let gmapUrl = `https://www.google.com/maps?q=${coordinates}`;
      window.open(gmapUrl, "_blank");
    }

    window.onload = function() {
      let now = new Date();
      let formattedDate = now.toISOString().slice(0, 16);
      document.getElementById("tanggal-absen").value = formattedDate;
      getCurrentLocation();
    };
  </script>
</body>

</html>