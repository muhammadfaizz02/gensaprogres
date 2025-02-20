<?php
// Memulai sesi
session_start();

// Menghapus semua session yang ada
session_unset();

// Menghancurkan session
session_destroy();

// Mengarahkan pengguna ke halaman login
header("Location: login.php");
exit();
