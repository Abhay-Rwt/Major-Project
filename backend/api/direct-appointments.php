<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../inc/functions.php';

// Set content type to HTML for readable output
header("Content-Type: text/html");

echo "<h1>Debug: Direct Appointments Listing</h1>";

// Check if database connection is working
if ($conn) {
    echo "<p style='color: green;'>Database connection successful!</p>";
    
    // Get the provider ID from GET parameter
    $providerId = isset($_GET['providerId']) ? sanitize_input($_GET['providerId']) : 'provider-123';
    
    echo "<p>Querying appointments for Provider ID: <strong>{$providerId}</strong></p>";
    
    // Check if appointments table exists
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'appointments'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>Appointments table exists.</p>";
        
        // Check if there are any records in the appointments table
        $countQuery = "SELECT COUNT(*) as count FROM appointments";
        $countResult = mysqli_query($conn, $countQuery);
        $row = mysqli_fetch_assoc($countResult);
        echo "<p>There are " . $row['count'] . " total appointments in the database.</p>";
        
        // Display provider's appointments
        $query = "SELECT * FROM appointments WHERE provider_id = '$providerId'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<h2>Appointments for Provider ID: {$providerId}</h2>";
            echo "<p style='color: green;'>Found " . mysqli_num_rows($result) . " appointments for this provider.</p>";
            
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
            echo "<p style='color: red;'>No appointments found for provider ID: {$providerId}</p>";
            echo "<p>SQL Query: {$query}</p>";
            if (mysqli_error($conn)) {
                echo "<p>MySQL Error: " . mysqli_error($conn) . "</p>";
            }
        }
        
        // Show sample of appointments (if any)
        $allQuery = "SELECT * FROM appointments LIMIT 3";
        $allResult = mysqli_query($conn, $allQuery);
        
        if ($allResult && mysqli_num_rows($allResult) > 0) {
            echo "<h2>Sample of Appointments in Database (up to 3)</h2>";
            
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
            
            while ($row = mysqli_fetch_assoc($allResult)) {
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
        }
    } else {
        echo "<p style='color: red;'>Appointments table does not exist! Please run create-appointments-table.php first.</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed: " . mysqli_connect_error() . "</p>";
}

echo "<hr>";
echo "<h2>Test Links</h2>";
echo "<ul>";
echo "<li><a href='create-appointments-table.php'>Create/reset the appointments table</a></li>";
echo "<li><a href='direct-appointments.php?providerId=provider-123'>View appointments for provider-123</a></li>";
echo "<li><a href='test-db.php'>Run database test</a></li>";
echo "<li><a href='../appointments.php?providerId=provider-123'>Test the API response</a></li>";
echo "</ul>";

// Add a form to create a test appointment
echo "<hr>";
echo "<h2>Create Test Appointment</h2>";
echo "<form method='post' action='create-test-appointment.php'>";
echo "Provider ID: <input type='text' name='providerId' value='provider-123'><br>";
echo "Provider Name: <input type='text' name='providerName' value='Dr. Jane Smith'><br>";
echo "Client ID: <input type='text' name='clientId' value='client-456'><br>";
echo "Client Name: <input type='text' name='clientName' value='John Davis'><br>";
echo "Date (YYYY-MM-DD): <input type='date' name='date' value='" . date('Y-m-d') . "'><br>";
echo "Time (HH:MM): <input type='time' name='time' value='14:30'><br>";
echo "Type: <select name='type'>";
echo "<option value='Initial Consultation'>Initial Consultation</option>";
echo "<option value='Follow-up Meeting'>Follow-up Meeting</option>";
echo "<option value='Urgent Consultation'>Urgent Consultation</option>";
echo "</select><br>";
echo "Description: <textarea name='description'>Need legal assistance</textarea><br>";
echo "Status: <select name='status'>";
echo "<option value='pending'>Pending</option>";
echo "<option value='approved'>Approved</option>";
echo "<option value='completed'>Completed</option>";
echo "<option value='cancelled'>Cancelled</option>";
echo "</select><br>";
echo "<input type='submit' value='Create Test Appointment'>";
echo "</form>";
?> 