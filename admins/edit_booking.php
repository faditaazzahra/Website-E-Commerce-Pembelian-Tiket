<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Ambil data booking berdasarkan ID
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    $sql = "SELECT * FROM bookings WHERE booking_id = $booking_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
    } else {
        echo "<script>alert('Booking not found!'); window.location.href = 'bookings.php';</script>";
    }
}

// Update booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_date = $_POST['booking_date'];
    $num_adults = $_POST['num_adults'];
    $num_children = $_POST['num_children'];
    $total_price = $_POST['total_price'];
    $customer_name = $_POST['customer_name'];
    $customer_contact = $_POST['customer_contact'];
    $customer_id_number = $_POST['customer_id_number'];
    $payment_status = $_POST['payment_status'];

    $update_sql = "UPDATE bookings SET booking_date='$booking_date', num_adults='$num_adults', num_children='$num_children', 
                   total_price='$total_price', customer_name='$customer_name', customer_contact='$customer_contact', 
                   customer_id_number='$customer_id_number', payment_status='$payment_status' WHERE booking_id=$booking_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Booking updated successfully!'); window.location.href = 'bookings.php';</script>";
    } else {
        echo "<script>alert('Error updating booking: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="../assets/css/admins/style.css">
    <style>
        form input, form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form button {
            padding: 10px 15px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Edit Booking</h1>
        </header>

        <main class="main-content">
            <form action="" method="POST">
                <label for="booking_date">Booking Date:</label>
                <input type="date" id="booking_date" name="booking_date" value="<?php echo $booking['booking_date']; ?>" required>

                <label for="num_adults">Number of Adults:</label>
                <input type="number" id="num_adults" name="num_adults" value="<?php echo $booking['num_adults']; ?>" required>

                <label for="num_children">Number of Children:</label>
                <input type="number" id="num_children" name="num_children" value="<?php echo $booking['num_children']; ?>" required>

                <label for="total_price">Total Price:</label>
                <input type="number" id="total_price" name="total_price" value="<?php echo $booking['total_price']; ?>" required>

                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" value="<?php echo $booking['customer_name']; ?>" required>

                <label for="customer_contact">Customer Contact:</label>
                <input type="text" id="customer_contact" name="customer_contact" value="<?php echo $booking['customer_contact']; ?>" required>

                <label for="customer_id_number">Customer ID Number:</label>
                <input type="text" id="customer_id_number" name="customer_id_number" value="<?php echo $booking['customer_id_number']; ?>" required>

                <label for="payment_status">Payment Status:</label>
                <select id="payment_status" name="payment_status" required>
                    <option value="pending" <?php echo ($booking['payment_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="paid" <?php echo ($booking['payment_status'] === 'paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="cancelled" <?php echo ($booking['payment_status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>

                <button type="submit">Update Booking</button>
            </form>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>