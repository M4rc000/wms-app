function formatQuantity(qty, unit) {
  // Jika unit adalah liter, proses format sesuai ketentuan
  if (unit.toLowerCase() === "liter") {
    // Cek apakah nilai qty adalah bilangan bulat (misal 50.00)
    if (parseFloat(qty) % 1 === 0) {
      return parseInt(qty).toString();
    } else {
      // Jika ada nilai desimal, kembalikan dengan 2 angka di belakang koma
      return parseFloat(qty).toFixed(2);
    }
  }
  // Jika unit bukan liter, kembalikan nilai aslinya atau format sesuai kebutuhan
  return qty;
}

function formatDateToLong(dateString, locale = 'en-US') {
  const dateObj = new Date(dateString);
  if (isNaN(dateObj)) return 'Invalid date';

  const options = { day: '2-digit', month: 'long', year: 'numeric' };
  return dateObj.toLocaleDateString(locale, options);
}
