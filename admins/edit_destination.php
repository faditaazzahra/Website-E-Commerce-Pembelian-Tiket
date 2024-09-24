<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Cek apakah ID destinasi ada di URL
if (isset($_GET['id'])) {
    $destination_id = $_GET['id'];
    
    // Ambil data destinasi berdasarkan ID
    $sql = "SELECT * FROM destinations WHERE destination_id = $destination_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $destination = $result->fetch_assoc();
    } else {
        echo "<script>alert('Destination not found!'); window.location.href = 'destinations.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No destination ID provided!'); window.location.href = 'destinations.php';</script>";
    exit();
}

// Proses pengeditan destinasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_destination = $_POST['name_destination'];
    $location = $_POST['location'];
    $phone_number = $_POST['phone_number'];
    $price = $_POST['price'];
    $image = $destination['image'];
    $video_url = $_POST['video_url'];

    // Cek apakah ada file gambar yang diupload
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $valid_extensions = array("jpg", "jpeg", "png", "gif");

        // Validasi file gambar
        if (in_array($imageFileType, $valid_extensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Gambar berhasil diupload
            } else {
                echo "<script>alert('Error uploading image');</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            exit();
        }
    }

    // Update data destinasi di database
    $sql = "UPDATE destinations 
            SET name_destination = '$name_destination', location = '$location', phone_number = '$phone_number', 
                price = '$price', image = '$image', video_url= '$video_url'
            WHERE destination_id = $destination_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Destination updated successfully!'); window.location.href = 'destinations.php';</script>";
    } else {
        echo "<script>alert('Error updating destination: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Destination</title>
    <link rel="stylesheet" href="../assets/css/admins/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Edit Destination</h1>
        </header>

        <main class="main-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="name_destination">Destination Name:</label>
                <input type="text" id="name_destination" name="name_destination" value="<?php echo $destination['name_destination']; ?>" required>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo $destination['location']; ?>" required>

                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo $destination['phone_number']; ?>" required>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" value="<?php echo $destination['price']; ?>" required>

                <label for="video_url">Video URL:</label>
                <input type="text" id="video_url" name="video_url" value="<?php echo $destination['video_url']; ?>" required>

                <label for="image">Image (optional):</label>
                <input type="file" id="image" name="image">
                <?php if ($destination['image']): ?>
                    <p>Current image: <img src="uploads/<?php echo $destination['image']; ?>" alt="Destination Image" style="width:100px;"></p>
                <?php endif; ?>

                <button type="submit">Update Destination</button>
            </form>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>