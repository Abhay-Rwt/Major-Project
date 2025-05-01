<?php
// Enable detailed error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include initialization file
require_once __DIR__ . '/inc/init.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html");

echo "<h1>Appointment Deletion Test</h1>";

// Function to display appointments
function displayAppointments($conn) {
    $query = "SELECT id, provider_id, provider_name, client_name, date, time, status FROM appointments ORDER BY date, time";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<h2>Current Appointments</h2>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Provider ID</th><th>Provider</th><th>Client</th><th>Date/Time</th><th>Status</th><th>Actions</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['provider_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['provider_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['date'] . ' ' . $row['time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td><a href='?action=delete&id=" . htmlspecialchars($row['id']) . "' onclick=\"return confirm('Are you sure you want to delete this appointment?');\">Delete</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No appointments found in database.</p>";
    }
}

// Handle deletion request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $appointmentId = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "DELETE FROM appointments WHERE id = '$appointmentId'";
    
    if (mysqli_query($conn, $query)) {
        $affectedRows = mysqli_affected_rows($conn);
        if ($affectedRows > 0) {
            echo "<div style='padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px;'>";
            echo "Appointment deleted successfully.";
            echo "</div>";
        } else {
            echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px;'>";
            echo "Appointment with ID '$appointmentId' not found.";
            echo "</div>";
        }
    } else {
        echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px;'>";
        echo "Error deleting appointment: " . mysqli_error($conn);
        echo "</div>";
    }
}

// Add a test appointment
if (isset($_GET['action']) && $_GET['action'] === 'add-test') {
    $id = 'app_' . uniqid();
    $providerId = 'provider-123';
    $providerName = 'Dr. Jane Smith';
    $clientId = 'client-test-' . rand(1000, 9999);
    $clientName = 'Test Client';
    $date = date('Y-m-d', strtotime('+1 day'));
    $time = '09:00:00';
    $type = 'Test Consultation';
    $description = 'This is a test appointment to verify deletion.';
    $status = 'pending';
    
    $query = "INSERT INTO appointments (
        id, provider_id, provider_name, client_id, client_name, 
        date, time, type, description, status
      ) VALUES (
        '$id', '$providerId', '$providerName', '$clientId', '$clientName', 
        '$date', '$time', '$type', '$description', '$status'
      )";
    
    if (mysqli_query($conn, $query)) {
        echo "<div style='padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px;'>";
        echo "Test appointment created successfully with ID: $id";
        echo "</div>";
    } else {
        echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 15px;'>";
        echo "Error creating test appointment: " . mysqli_error($conn);
        echo "</div>";
    }
}

// Display current appointments
displayAppointments($conn);

// Add controls
echo "<div style='margin-top: 20px;'>";
echo "<a href='?action=add-test' style='padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Add Test Appointment</a>";
echo "<a href='../HTML/provider-dashboard.html' style='padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Go to Provider Dashboard</a>";
echo "<a href='admin/db-repair.php' style='padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px;'>Database Admin</a>";
echo "</div>";

// Verification steps
echo "<h2>Deletion Verification</h2>";
echo "<ol>";
echo "<li>Click 'Add Test Appointment' to create a test appointment</li>";
echo "<li>Go to the Provider Dashboard and find the appointment</li>";
echo "<li>Click 'Reject' on the appointment</li>";
echo "<li>Return to this page and verify the appointment is no longer listed</li>";
echo "</ol>";

// AJAX request test
echo "<h2>Test DELETE Request Directly</h2>";
echo "<div style='margin-bottom: 20px;'>";
echo "<input type='text' id='appointmentId' placeholder='Enter appointment ID' style='padding: 8px; width: 300px;'>";
echo "<button onclick='testDelete()' style='padding: 8px 15px; background-color: #dc3545; color: white; border: none; border-radius: 5px; margin-left: 10px;'>Test DELETE</button>";
echo "<div id='result' style='margin-top: 10px; padding: 10px; border: 1px solid #ddd; display: none;'></div>";
echo "</div>";

// Add JavaScript to test the DELETE endpoint directly
echo "<script>
function testDelete() {
    const appointmentId = document.getElementById('appointmentId').value;
    if (!appointmentId) {
        alert('Please enter an appointment ID');
        return;
    }
    
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = 'Sending DELETE request...';
    resultDiv.style.display = 'block';
    
    fetch('api/appointments.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: appointmentId
        })
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        if (data.success) {
            resultDiv.style.backgroundColor = '#d4edda';
            // Reload the page after 2 seconds to show updated appointment list
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            resultDiv.style.backgroundColor = '#f8d7da';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = 'Error: ' + error.message;
        resultDiv.style.backgroundColor = '#f8d7da';
    });
}
</script>";

echo "<p><b>Verification completed at " . date('Y-m-d H:i:s') . "</b></p>";
?> 