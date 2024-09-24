<?php
include '../config/config.php';

// Mengambil data destinasi untuk form pemesanan
try {
    $sql_destinations = "SELECT * FROM destinations";
    $result_destinations = $conn->query($sql_destinations);

    if (!$result_destinations) {
        throw new Exception("Error pada query: " . $conn->error);
    }

    $destinations = [];
    if ($result_destinations->num_rows > 0) {
        while($row = $result_destinations->fetch_assoc()) {
            $destinations[$row['destination_id']] = [
                'name_destination' => $row['name_destination'],
                'location' => $row['location'],
                'image' => $row['image'],
                'price' => $row['price']
            ];
        }
    } 
} catch (Exception $e) {
    // Tangani error koneksi database atau query
    die("Terjadi kesalahan: " . $e->getMessage());
} finally {
    $conn->close();
}

// Encode data destinasi ke dalam format JSON untuk digunakan di JavaScript
$destinations_json = json_encode($destinations, JSON_UNESCAPED_UNICODE);

// Jika form pemesanan disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination_id = $_POST['destination_id'];
    $booking_date = $_POST['booking_date'];
    $num_adults = $_POST['num_adults'];
    $num_children = $_POST['num_children'];
    $customer_name = $_POST['customer_name'];
    $customer_contact = $_POST['customer_contact'];
    $customer_id_number =$_POST['customer_id_number'];
    $total_price = $_POST['total_price'];
    $payment_method = $_POST['payment_method'];

    // Query untuk menyimpan data pemesanan ke database
    $sql = "INSERT INTO bookings (destination_id, booking_date, num_adults, num_children, total_price, customer_name, customer_contact, payment_method, payment_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isiiisss', $destination_id, $booking_date, $num_adults, $num_children, $total_price, $customer_name, $customer_contact, $payment_method);
    
    if ($stmt->execute()) {
        echo "Booking successful! Your booking is pending payment.";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Destinations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <p>Jelajah Wisata Cirebon</p>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="destinations.php">Destinasi Wisata</a></li>
                <li><a href="bookings.php" class="active">Pesan Tiket</a></li> 
            </ul>
        </nav>
    </header>

    <div class="booking-container">
        <h2>Book Your Destination</h2>
        <form id="bookingForm" action="proses-pemesanan.php" method="POST">
            <label for="customer_name">Nama Lengkap:</label>
            <input type="text" id="customer_name" name="customer_name" required>
            
            <label for="customer_id_number">Nomor Identitas:</label>
            <input type="text" id="customer_id_number" name="customer_id_number" required maxlength="16">

            <label for="customer_contact">No. HP:</label>
            <input type="text" id="customer_contact" name="customer_contact" required>

            <label for="destination">Destinasi Wisata:</label>
            <select id="destination" name="destination_id" required onchange="updatePriceAndCalculateTotal()">
                <option value="" disabled selected>Pilih Destinasi</option> 
                <?php foreach ($destinations as $id => $destination): ?>
                    <option value="<?php echo $id; ?>" data-price="<?php echo $destination['price']; ?>">
                        <?php echo $destination['name_destination']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="booking_date">Tanggal Kunjungan:</label>
            <input type="date" id="booking_date" name="booking_date" required>

            <label for="num_adults">Pengunjung Dewasa:</label>
            <input type="number" id="num_adults" name="num_adults" min="0" value="0" required onchange="calculateTotalPrice()">

            <label for="num_children">Pengunjung Anak-Anak:</label>
            <p>Usia di bawah 12 tahun</p>
            <input type="number" id="num_children" name="num_children" min="0" value="0" required onchange="calculateTotalPrice()">


            <label for="ticket_price">Harga Tiket per Orang (IDR):</label>
            <input type="number" id="ticket_price" name="ticket_price" readonly>

            <label for="total_price">Total Bayar (IDR):</label>
            <input type="number" id="total_price" name="total_price" required readonly>

            <label for="payment_method">Pembayaran Melalui:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="whatsapp">WhatsApp Payment</option>
                <option value="other_app">Other Payment Apps</option>
            </select>

            <button type="button" onclick="calculateTotalPrice()">Hitung Total Bayar</button> 
            <button type="submit">Submit Booking</button>
        </form>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about">
                    <h3>Tentang Kami</h3>
                    <p>Jelajah Wisata Cirebon adalah platform terpercaya untuk menjelajahi keindahan dan keunikan Kota Wali. Kami berkomitmen untuk memberikan pengalaman wisata yang tak terlupakan bagi setiap pengunjung.</p>
                </div>
                <div class="footer-section contact">
                    <h3>Kontak Kami</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Alamat: Jl. Kesunean Utara No. 123, Cirebon</li>
                        <li><i class="fas fa-phone"></i> Telepon: (0231) 123456</li>
                        <li><i class="fas fa-envelope"></i> Email: info@jelajahwisatacirebon.com</li>
                    </ul>
                </div>
                <div class="footer-section social">
                    <h3>Ikuti Kami</h3>
                    <ul>
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Jelajah Wisata Cirebon. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Data destinasi dan harga tiket 
        const destinations = <?php echo $destinations_json; ?>; 

        function calculateTotalPrice() {
            const destinationSelect = document.getElementById('destination');
            const adultTicketsInput = document.getElementById('num_adults');
            const childTicketsInput = document.getElementById('num_children');
            const ticketPriceDisplay = document.getElementById('ticket_price');
            const totalPriceDisplay = document.getElementById('total_price');

            const selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
            const basePrice = parseFloat(selectedOption.dataset.price) || 0;
            const numAdults = parseInt(adultTicketsInput.value) || 0;
            const numChildren = parseInt(childTicketsInput.value) || 0;

            const totalPrice = (numAdults * basePrice) + (numChildren * basePrice / 2); 

            ticketPriceDisplay.value = basePrice;
            totalPriceDisplay.value = totalPrice;
        }

        // Panggil fungsi saat halaman dimuat untuk inisialisasi
        calculateTotalPrice();
    </script>
</body>
</html>