<?php
include '../config/config.php';

// Query untuk mengambil data destinasi 
$sql = "SELECT * FROM destinations";
$result = $conn->query($sql);

// Handle jika query gagal
if (!$result) {
    die("Error pada query: " . $conn->error);
}

$destinations = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $destinations[] = $row; 
    }
} 

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinasi Wisata</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user/style.css">
    <style>
        .destinations-list {
            padding: 40px 0;
        }

        .destinations-list h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .destination-card {
            display: block;
            text-align: center;
            line-height: 40px;
        }

        .destination-card-item {
            display: flex;
            gap: 50px;
        }

        .destination-card img {
            width: 500px;
        }

        .video-container {
            margin-bottom: 15px;
        }

        .video-container iframe {
            width: 900px;
            height: 400px;

        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0D1B2A;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .btn:hover {
            background-color: #13273bd0;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <p>Jelajah Wisata Cirebon</p>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="destinations.php" class="active">Destinasi Wisata</a></li>
                <li><a href="bookings.php">Pesan Tiket</a></li>
            </ul>
        </nav>
    </header>

    <div class="destinations-list">
        <h2>Daftar Destinasi Wisata</h2>
        <?php foreach ($destinations as $destination): ?>
            <div class="destination-card">
                <div class="destination-card-item">
                    <img src="../assets/images/uploads/<?php echo $destination['image']; ?>" alt="<?php echo $destination['name_destination']; ?>">

                    <?php 
                    if (!empty($destination['video_url'])): 
                    ?>
                        <div class="video-container">
                            <iframe width="100%" height="200" src="<?php echo $destination['video_url']; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>
                </div>
                <h3><?php echo $destination['name_destination']; ?></h3>
                <p><?php echo $destination['location']; ?></p>
                <p>Harga Tiket: Rp <?php echo number_format($destination['price'], 2, '.', ','); ?> <br>
                    *Harga anak-anak mendapatkan diskon 50%
                </p>

                <a href="bookings.php?destination_id=<?php echo $destination['destination_id']; ?>" class="btn">Pesan Sekarang</a>
            </div>
            <hr>
        <?php endforeach; ?>
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
</body>
</html>