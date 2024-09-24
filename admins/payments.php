<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Query untuk mengambil data bookings dan status pembayaran
$sql = "SELECT bookings.booking_id, bookings.booking_date, bookings.customer_name, bookings.total_price, 
               bookings.payment_status, bookings.customer_id_number, destinations.name_destination 
        FROM bookings 
        JOIN destinations ON bookings.destination_id = destinations.destination_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payments</title>
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

        .status-paid {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .status-cancelled {
            color: red;
            font-weight: bold;
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
            <h2>Payments Status</h2>

            <?php if ($result->num_rows > 0): ?>
                <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Booking Date</th>
                        <th>Customer Name</th>
                        <th>Customer ID Number</th>
                        <th>Destination</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                        <th>Action</th> <!-- Tambah kolom action -->
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['booking_id']; ?></td>
                            <td><?php echo $row['booking_date']; ?></td>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td><?php echo $row['customer_id_number']; ?></td>
                            <td><?php echo $row['name_destination']; ?></td>
                            <td><?php echo $row['total_price']; ?></td>
                            <td>
                                <?php if ($row['payment_status'] == 'paid'): ?>
                                    <span class="status-paid">Paid</span>
                                <?php elseif ($row['payment_status'] == 'pending'): ?>
                                    <span class="status-pending">Pending</span>
                                <?php else: ?>
                                    <span class="status-cancelled">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_payment.php?booking_id=<?php echo $row['booking_id']; ?>">Edit</a> <!-- Link ke halaman edit -->
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
</body>
</html>

<?php
$conn->close();
?>