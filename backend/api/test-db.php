<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Set content type to HTML for readable output
header("Content-Type: text/html");

echo "<h1>Database Connection Test</h1>";

// Check if database connection is working
if ($conn) {
    echo "<p style='color: green;'>Database connection successful!</p>";
    
    // Check if appointments table exists
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'appointments'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>Appointments table exists.</p>";
        
        // Check if there are any records in the appointments table
        $countQuery = "SELECT COUNT(*) as count FROM appointments";
        $countResult = mysqli_query($conn, $countQuery);
        $row = mysqli_fetch_assoc($countResult);
        echo "<p>There are " . $row['count'] . " appointments in the database.</p>";
        
        // Display all appointments
        $query = "SELECT * FROM appointments";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<h2>All Appointments</h2>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Provider ID</th>";
            echo "<th>Provider Name</th>";
            echo "<th>Client ID</th>";
            echo "<th>Client Name</th>";
            echo "<th>Date</th>";
            echo "<th>Time</th>";
            echo "<th>Status</th>";
            echo "</tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['provider_id'] . "</td>";
                echo "<td>" . $row['provider_name'] . "</td>";
                echo "<td>" . $row['client_id'] . "</td>";
                echo "<td>" . $row['client_name'] . "</td>";
                echo "<td>" . $row['appointment_date'] . "</td>";
                echo "<td>" . $row['appointment_time'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No appointments found.</p>";
        }
    } else {
        echo "<p style='color: red;'>Appointments table does not exist! Please run create-appointments-table.php first.</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed: " . mysqli_connect_error() . "</p>";
}

echo "<hr>";
echo "<p><a href='create-appointments-table.php'>Click here to create/reset the appointments table</a></p>";
?> 