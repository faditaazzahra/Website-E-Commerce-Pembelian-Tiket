<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Query untuk mengambil data bookings
$sql = "SELECT bookings.booking_id, bookings.booking_date, bookings.num_adults, bookings.num_children, 
               bookings.total_price, bookings.customer_name, bookings.customer_contact, bookings.payment_status, 
               bookings.customer_id_number, destinations.name_destination 
        FROM bookings 
        JOIN destinations ON bookings.destination_id = destinations.destination_id";

$result = $conn->query($sql);

// Logika untuk menghapus booking
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM bookings WHERE booking_id = $delete_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Booking deleted successfully!'); window.location.href = 'bookings.php';</script>";
    } else {
        echo "<script>alert('Error deleting booking: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bookings</title>
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

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons a, .action-buttons button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
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
            <h2>Bookings List</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Booking Date</th>
                            <th>Destination</th>
                            <th>Customer Name</th>
                            <th>Customer ID Number</th>
                            <th>Contact</th>
                            <th>Adults</th>
                            <th>Children</th>
                            <th>Total Price</th>
                            <th>Payment Status</th>
                            <th>Actions</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['booking_id']; ?></td>
                                <td><?php echo $row['booking_date']; ?></td>
                                <td><?php echo $row['name_destination']; ?></td>
                                <td><?php echo $row['customer_name']; ?></td>
                                <td><?php echo $row['customer_id_number']; ?></td>
                                <td><?php echo $row['customer_contact']; ?></td>
                                <td><?php echo $row['num_adults']; ?></td>
                                <td><?php echo $row['num_children']; ?></td>
                                <td><?php echo $row['total_price']; ?></td>
                                <td><?php echo ucfirst($row['payment_status']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_booking.php?id=<?php echo $row['booking_id']; ?>" class="edit-button">Edit</a>
                                        <button onclick="confirmDelete(<?php echo $row['booking_id']; ?>)" class="delete-button">Delete</button>
                                    </div>
                                </td> 
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bookings found.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function confirmDelete(bookingId) {
            if (confirm("Are you sure you want to delete this booking?")) {
                window.location.href = "bookings.php?delete_id=" + bookingId;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>