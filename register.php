<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];

  // Periksa apakah email sudah digunakan
  $email_check_query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
  $email_check_result = mysqli_query($conn, $email_check_query);

  if (mysqli_num_rows($email_check_result) > 0) {
    // Email sudah ada, tampilkan pesan error
    echo "<script>
            alert('Email sudah digunakan! Silakan gunakan email lain.');
            window.history.back();
          </script>";
  } else {
    // Lanjutkan proses registrasi
    $nama = $_POST['nama_lengkap'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $ttl = $_POST['ttl'];
    $no_telp = $_POST['no_telp'];
    $jenjang = $_POST['jenjang_pendidikan'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $kelas = $_POST['kelas'];
    $pilihan_bahasa = $_POST['pilihan_bahasa'];
    $no_wali = $_POST['no_wali'];
    $password = md5($_POST['password']);

    $target_dir = "uploads/";
    $foto_nama = basename($_FILES["foto"]["name"]);
    $target_file = $target_dir . time() . "_" . $foto_nama;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Periksa apakah file yang diunggah adalah gambar
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check === false) {
      die("File bukan gambar!");
    }

    // Hanya izinkan file gambar dengan format tertentu
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
      die("Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.");
    }

    // Proses upload file foto
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
      // Query untuk menyimpan data ke database
      $sql = "INSERT INTO users (nama_lengkap, jenis_kelamin, ttl, no_telp, email, jenjang_pendidikan, asal_sekolah, kelas, pilihan_bahasa, no_wali, password, foto) 
              VALUES ('$nama', '$jenis_kelamin', '$ttl', '$no_telp', '$email', '$jenjang', '$asal_sekolah', '$kelas', '$pilihan_bahasa', '$no_wali', '$password', '$target_file')";

      if (mysqli_query($conn, $sql)) {
        // Jika registrasi berhasil, tampilkan alert dan arahkan ke halaman login
        echo "<script>
                alert('Registrasi berhasil! Silakan hubungi admin untuk bisa login.');
                window.location.href = 'login.php';
              </script>";
      } else {
        echo "Error: " . mysqli_error($conn);
      }
    } else {
      echo "Maaf, terjadi kesalahan saat mengunggah foto.";
    }
  }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link rel="stylesheet" href="css/register.css" />
</head>

<body>
  <div class="container">
    <div class="header-image"></div>
    <h2>Register GenSa English & Arabic Center</h2>

    <form method="POST" enctype="multipart/form-data">
      <div class="input-group">
        <label>Nama Lengkap :</label>
        <input type="text" name="nama_lengkap" required />
      </div>

      <div class="input-group">
        <label>Asal Sekolah :</label>
        <input type="text" name="asal_sekolah" required />
      </div>

      <div class="input-group">
        <label>Nomor Wali :</label>
        <input type="text" name="no_wali" required />
      </div>

      <div class="input-group">
        <label>Kelas :</label>
        <input type="text" name="kelas" required />
      </div>

      <div class="input-group">
        <label>Email :</label>
        <input type="email" name="email" required />
      </div>

      <div class="input-group">
        <label>Tanggal Lahir :</label>
        <input type="date" name="ttl" required />
      </div>

      <div class="input-group">
        <label>Password :</label>
        <input type="password" name="password" required />
      </div>

      <div class="input-group">
        <label>Gender :</label>
        <select name="jenis_kelamin">
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
        </select>
      </div>

      <div class="input-group">
        <label>Nomor Telepon :</label>
        <input type="text" name="no_telp" required />
      </div>

      <div class="input-group">
        <label>Jenjang :</label>
        <select name="jenjang_pendidikan">
          <option value="TK">TK</option>
          <option value="SD">SD</option>
          <option value="SMP">SMP</option>
          <option value="SMA">SMA</option>
        </select>
      </div>

      <div class="input-group">
        <label>Mata Pelajaran :</label>
        <select name="pilihan_bahasa">
          <option value="Arab">Arab</option>
          <option value="Inggris">Inggris</option>
          <option value="Keduanya">Keduanya</option>
        </select>
      </div>

      <div class="input-group">
        <label>Upload Foto :</label>
        <input type="file" name="foto" accept="image/*" onchange="previewImage(event)" required />
        <img id="foto-preview" src="#" alt="Pratinjau Gambar" style="display: none; width: 100px; height: auto; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px;">
      </div>

      <button type="submit">Submit</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    <p>Butuh bantuan? <a href="">Tanya Admin</a></p>
  </div>
  <script>
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
  </script>
</body>

</html>