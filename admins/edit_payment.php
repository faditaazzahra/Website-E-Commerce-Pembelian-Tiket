<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Ambil booking_id dari URL
$booking_id = $_GET['booking_id'];

// Query untuk mengambil detail booking berdasarkan booking_id
$sql = "SELECT * FROM bookings WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    echo "Booking not found!";
    exit();
}

// Update status pembayaran jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_status = $_POST['payment_status'];

    $update_sql = "UPDATE bookings SET payment_status = ? WHERE booking_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('si', $new_status, $booking_id);

    if ($update_stmt->execute()) {
        header('Location: payments.php'); // Redirect ke halaman payments setelah update
        exit();
    } else {
        echo "Error updating payment status!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment Status</title>
    <link rel="stylesheet" href="../assets/css/admins/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Edit Payment Status</h1>
        </header>

        <main class="main-content">
            <h2>Edit Payment Status for Booking ID: <?php echo $booking['booking_id']; ?></h2>

            <form action="edit_payment.php?booking_id=<?php echo $booking['booking_id']; ?>" method="POST">
                <div class="form-group">
                    <label for="payment_status">Payment Status:</label>
                    <select id="payment_status" name="payment_status" required>
                        <option value="paid" <?php if ($booking['payment_status'] == 'paid') echo 'selected'; ?>>Paid</option>
                        <option value="pending" <?php if ($booking['payment_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="cancelled" <?php if ($booking['payment_status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                </div>

                <input type="submit" value="Update Status">
                <a href="payments.php">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>