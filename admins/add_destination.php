<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Proses ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name_destination = $_POST['name_destination'];
    $location = $_POST['location'];
    $phone_number = $_POST['phone_number'];
    $price = $_POST['price'];
    $video_url = $_POST['video_url'];
    
    // Proses upload gambar
    $image = $_FILES['image']['name'];
    $target_dir = "../assets/images/uploads/";
    $target_file = $target_dir . basename($image);
    
    // Validasi gambar
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $valid_extensions = array("jpg", "jpeg", "png", "gif");

    if (in_array($imageFileType, $valid_extensions)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Simpan data ke database
            $sql = "INSERT INTO destinations (name_destination, location, phone_number, price, image) 
                    VALUES ('$name_destination', '$location', '$phone_number', '$price', '$image')";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Destination added successfully!'); window.location.href = 'destinations.php';</script>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Error uploading image');</script>";
        }
    } else {
        echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination</title>
    <link rel="stylesheet" href="../assets/css/admins/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Add New Destination</h1>
        </header>

        <main class="main-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="name_destination">Destination Name:</label>
                <input type="text" id="name_destination" name="name_destination" required>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>

                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required>

                <label for="image">Image:</label>
                <input type="file" id="image" name="image" required>

                <label for="video_url">Video URL:</label>
                <input type="text" id="video_url" name="video_url" required>

                <button type="submit">Add Destination</button>
            </form>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>