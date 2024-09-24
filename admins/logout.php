<?php
session_start(); // Mulai sesi

// Hapus semua variabel sesi
session_unset();

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: index.php"); // Ganti dengan nama file halaman login Anda jika berbeda
exit();
?>