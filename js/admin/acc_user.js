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