<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Logika untuk menghapus destination
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Check if any bookings exist for this destination
    $check_bookings_sql = "SELECT COUNT(*) as booking_count FROM bookings WHERE destination_id = $delete_id";
    $check_result = $conn->query($check_bookings_sql);
    $row = $check_result->fetch_assoc();
    $booking_count = $row['booking_count'];

    if ($booking_count > 0) {
        echo "<script>alert('Cannot delete destination. There are $booking_count bookings associated with it. Please delete the bookings first.'); window.location.href = 'destinations.php';</script>";
    } else {
        $delete_sql = "DELETE FROM destinations WHERE destination_id = $delete_id";
        if ($conn->query($delete_sql) === TRUE) {
            echo "<script>alert('Destination deleted successfully!'); window.location.href = 'destinations.php';</script>";
        } else {
            echo "<script>alert('Error deleting destination: " . $conn->error . "');</script>";
        }
    }
}

// Query untuk mengambil data destinations
$sql = "SELECT * FROM destinations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Destinations</title>
    <link rel="stylesheet" href="../assets/css/admins/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: blue;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .main-content h2 {
            margin-bottom: 20px;
        }

        img {
            width: 100px;
            height: 70px;
            object-fit: cover;
        }
        
        .action-buttons a {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            line-height: 40px;
        }

        .edit-button {
            background-color: blue;
        }

        .delete-button {
            background-color: red;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <div class="profile-section">
                <img src="../assets/images/profile.jpg" alt="Profile Avatar" class="avatar">
                <span><?php echo $_SESSION['full_name']; ?></span>
            </div>
        </header>

        <aside class="sidebar">
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="bookings.php">Bookings</a></li>
                <li><a href="destinations.php">Destinations</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>Destinations List</h2>

            <a href="add_destination.php" class="add-button">Add New Destination</a>

            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Destination Name</th>
                            <th>Location</th>
                            <th>Phone Number</th>
                            <th>Image</th>
                            <th>Video URL</th>
                            <th>Price</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['destination_id']; ?></td>
                                <td><?php echo $row['name_destination']; ?></td>
                                <td><?php echo $row['location']; ?></td>
                                <td><?php echo $row['phone_number']; ?></td>
                                <td>
                                    <?php if ($row['image']): ?>
                                        <img src="../assets/images/uploads/<?php echo $row['image']; ?>" alt="Image">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['video_url']; ?></td>
                                <td><?php echo $row['price']; ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td><?php echo $row['updated_at']; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_destination.php?id=<?php echo $row['destination_id']; ?>" class="edit-button">Edit</a>
                                        <a href="destinations.php?delete_id=<?php echo $row['destination_id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this destination?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No destinations found.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>