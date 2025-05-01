<?php
// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../inc/functions.php';

// Set content type to HTML for readable output
header("Content-Type: text/html");

echo "<h1>Create Test Appointment</h1>";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $providerId = isset($_POST['providerId']) ? sanitize_input($_POST['providerId']) : 'provider-123';
    $providerName = isset($_POST['providerName']) ? sanitize_input($_POST['providerName']) : 'Dr. Jane Smith';
    $clientId = isset($_POST['clientId']) ? sanitize_input($_POST['clientId']) : 'client-456';
    $clientName = isset($_POST['clientName']) ? sanitize_input($_POST['clientName']) : 'John Davis';
    $date = isset($_POST['date']) ? sanitize_input($_POST['date']) : date('Y-m-d');
    $time = isset($_POST['time']) ? sanitize_input($_POST['time']) : '14:30';
    $type = isset($_POST['type']) ? sanitize_input($_POST['type']) : 'Initial Consultation';
    $description = isset($_POST['description']) ? sanitize_input($_POST['description']) : 'Need legal assistance';
    $status = isset($_POST['status']) ? sanitize_input($_POST['status']) : 'pending';
    
    // Generate a unique ID
    $id = 'app_' . time() . '_' . mt_rand(1000, 9999);
    
    // Check if database connection is working
    if ($conn) {
        echo "<p style='color: green;'>Database connection successful!</p>";
        
        // Check if appointments table exists
        $result = mysqli_query($conn, "SHOW TABLES LIKE 'appointments'");
        if (mysqli_num_rows($result) > 0) {
            echo "<p style='color: green;'>Appointments table exists.</p>";
            
            // Insert appointment into database
            $query = "INSERT INTO appointments (
                        id, provider_id, provider_name, client_id, client_name, 
                        appointment_date, appointment_time, appointment_type, description, status
                      ) VALUES (
                        '$id', '$providerId', '$providerName', '$clientId', '$clientName', 
                        '$date', '$time', '$type', '$description', '$status'
                      )";
            
            $result = mysqli_query($conn, $query);
            
            if ($result) {
                echo "<p style='color: green;'>Test appointment created successfully!</p>";
                echo "<h2>Appointment Details:</h2>";
                echo "<ul>";
                echo "<li><strong>ID:</strong> $id</li>";
                echo "<li><strong>Provider ID:</strong> $providerId</li>";
                echo "<li><strong>Provider Name:</strong> $providerName</li>";
                echo "<li><strong>Client ID:</strong> $clientId</li>";
                echo "<li><strong>Client Name:</strong> $clientName</li>";
                echo "<li><strong>Date:</strong> $date</li>";
                echo "<li><strong>Time:</strong> $time</li>";
                echo "<li><strong>Type:</strong> $type</li>";
                echo "<li><strong>Description:</strong> $description</li>";
                echo "<li><strong>Status:</strong> $status</li>";
                echo "</ul>";
            } else {
                echo "<p style='color: red;'>Failed to create test appointment: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>Appointments table does not exist! Please run create-appointments-table.php first.</p>";
        }
    } else {
        echo "<p style='color: red;'>Database connection failed: " . mysqli_connect_error() . "</p>";
    }
} else {
    echo "<p>No form data submitted. Please <a href='direct-appointments.php'>go back</a> and use the form.</p>";
}

echo "<hr>";
echo "<p><a href='direct-appointments.php'>Back to Appointments Debug</a></p>";
?> 