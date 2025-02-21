function toggleSidebar() {
  document.querySelector('.sidebar').classList.toggle('active');
}

// Fungsi konfirmasi untuk menyetujui
function confirmApprove(namaGuru) {
  return confirm(`Apakah Anda yakin akan menyetujui absensi guru ${namaGuru}?`);
}

// Fungsi konfirmasi untuk menolak
function confirmReject(namaGuru) {
  return confirm(`Apakah Anda yakin akan menolak absensi guru ${namaGuru}?`);
}

// Fungsi untuk menampilkan modal gambar
function showImageModal(imageSrc) {
  document.getElementById('modalImage').src = imageSrc;
  document.getElementById('imageModal').style.display = 'flex';
}

// Fungsi untuk menutup modal gambar
function closeImageModal() {
  document.getElementById('imageModal').style.display = 'none';
}