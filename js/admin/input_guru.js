// Function to toggle sidebar visibility
function toggleSidebar() {
  document.querySelector('.sidebar').classList.toggle('active');
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