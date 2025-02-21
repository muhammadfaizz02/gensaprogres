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