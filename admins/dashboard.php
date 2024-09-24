<?php
session_start();

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

include '../config/config.php';

// Menghitung total bookings
$sql_total_bookings = "SELECT COUNT(*) AS total_bookings FROM bookings";
$result_total_bookings = $conn->query($sql_total_bookings);
$total_bookings = $result_total_bookings->fetch_assoc()['total_bookings'];

// Menghitung total destinations
$sql_total_destinations = "SELECT COUNT(*) AS total_destinations FROM destinations";
$result_total_destinations = $conn->query($sql_total_destinations);
$total_destinations = $result_total_destinations->fetch_assoc()['total_destinations'];

// Mengambil jumlah aktivitas terbaru (misalnya, booking terbaru dalam 7 hari terakhir)
$sql_recent_activities = "SELECT COUNT(*) AS recent_activities FROM bookings WHERE booking_date >= (CURDATE() - INTERVAL 7 DAY)";
$result_recent_activities = $conn->query($sql_recent_activities);
$recent_activities = $result_recent_activities->fetch_assoc()['recent_activities'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admins/style.css">
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
            <section class="dashboard-metrics">
                <div class="metric-card">
                    <h3>Total Bookings</h3>
                    <p><?php echo $total_bookings; ?></p>
                </div>
                <div class="metric-card">
                    <h3>Total Destinations</h3>
                    <p><?php echo $total_destinations; ?></p>
                </div>
                <div class="metric-card">
                    <h3>Recent Activities</h3>
                    <p><?php echo $recent_activities; ?> New Bookings (Last 7 days)</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>