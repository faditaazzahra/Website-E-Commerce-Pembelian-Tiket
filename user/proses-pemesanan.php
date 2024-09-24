<?php
session_start();

include '../config/config.php';

// Cek apakah formulir telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari formulir
    $destination_id = $_POST['destination_id'];
    $booking_date = $_POST['booking_date'];
    $num_adults = $_POST['num_adults'];
    $num_children = $_POST['num_children'];
    $customer_name = $_POST['customer_name'];
    $customer_contact = $_POST['customer_contact'];
    $customer_id_number = $_POST['customer_id_number'];
    $total_price = $_POST['total_price'];
    $payment_method = $_POST['payment_method'];

    // Validasi data (Anda perlu menambahkan validasi yang lebih lengkap sesuai kebutuhan)
    if (empty($destination_id) || empty($booking_date) || empty($customer_name) || empty($customer_contact) || empty($customer_id_number) || empty($total_price)) {
        die("Harap isi semua field yang diperlukan.");
    }

    // Simpan data ke database (tabel bookings)
    $sql = "INSERT INTO bookings (destination_id, booking_date, num_adults, num_children, total_price, customer_name, customer_contact, customer_id_number, payment_method, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isiiissss', $destination_id, $booking_date, $num_adults, $num_children, $total_price, $customer_name, $customer_contact, $customer_id_number, $payment_method);

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id; // Dapatkan ID booking yang baru dibuat

        // Buat pesan WhatsApp otomatis
        $whatsapp_message = "Halo, saya ingin memesan tiket wisata:\n\n";
        $whatsapp_message .= "Destinasi: " . getDestinationName($destination_id, $conn) . "\n";
        $whatsapp_message .= "Tanggal Kunjungan: " . $booking_date . "\n";
        $whatsapp_message .= "Pengunjung Dewasa: " . $num_adults . "\n";
        $whatsapp_message .= "Pengunjung Anak-Anak: " . $num_children . "\n";
        $whatsapp_message .= "Total Bayar: Rp " . number_format($total_price, 0, ',', '.') . "\n";
        $whatsapp_message .= "Nama: " . $customer_name . "\n";
        $whatsapp_message .= "No. HP: " . $customer_contact . "\n";
        $whatsapp_message .= "No. Identitas: " . $customer_id_number . "\n";
        $whatsapp_message .= "ID Pemesanan: " . $booking_id . "\n\n";
        $whatsapp_message .= "Mohon informasikan metode pembayaran dan konfirmasi lebih lanjut.";

        // Ganti nomor WhatsApp dengan nomor bisnis Anda
        $whatsapp_number = "6281573513752"; // Contoh nomor WhatsApp (gunakan format internasional)

        // URL WhatsApp dengan pesan otomatis
        $whatsapp_url = "https://api.whatsapp.com/send?phone=" . $whatsapp_number . "&text=" . urlencode($whatsapp_message);

        // Redirect ke WhatsApp
        header("Location: " . $whatsapp_url);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Fungsi untuk mendapatkan nama destinasi berdasarkan ID
function getDestinationName($destination_id, $conn) {
    $sql = "SELECT name_destination FROM destinations WHERE destination_id = $destination_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name_destination'];
    } else {
        return "Destinasi tidak ditemukan";
    }
}

$conn->close();
?>