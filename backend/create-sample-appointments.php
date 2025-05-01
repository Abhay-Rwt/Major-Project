<?php
// Enable detailed error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include initialization file
require_once __DIR__ . '/inc/init.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html");

echo "<h1>Sample Appointments Creation</h1>";

// Check if the appointments table exists
$tableCheckQuery = "SHOW TABLES LIKE 'appointments'";
$tableExists = mysqli_query($conn, $tableCheckQuery);

if (!$tableExists || mysqli_num_rows($tableExists) === 0) {
    echo "<p>Creating appointments table...</p>";
    
    // Create appointments table with the correct column names used by the database
    $createTableQuery = "CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        provider_id VARCHAR(50) NOT NULL,
        provider_name VARCHAR(100) NOT NULL,
        client_id VARCHAR(50) NOT NULL,
        client_name VARCHAR(100) NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        appointment_type VARCHAR(100) NOT NULL,
        description TEXT,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (provider_id),
        INDEX (client_id),
        INDEX (status),
        INDEX (appointment_date)
    )";
    
    if (!mysqli_query($conn, $createTableQuery)) {
        echo "<p>Error creating appointments table: " . mysqli_error($conn) . "</p>";
        exit;
    }
    
    echo "<p>Appointments table created successfully.</p>";
}

// Sample data with string-based provider ID
$sampleAppointments = [
    [
        'id' => 'app_' . uniqid(),
        'provider_id' => 'provider-123',
        'provider_name' => 'John Lawyer',
        'client_id' => 'client-789',
        'client_name' => 'John Doe',
        'date' => date('Y-m-d', strtotime('+2 days')),
        'time' => '14:30:00',
        'type' => 'Initial Consultation',
        'description' => 'Need legal advice for a corporate matter.',
        'status' => 'pending'
    ],
    [
        'id' => 'app_' . uniqid(),
        'provider_id' => 'provider-123',
        'provider_name' => 'John Lawyer',
        'client_id' => 'client-456',
        'client_name' => 'Sarah Johnson',
        'date' => date('Y-m-d', strtotime('+1 day')),
        'time' => '10:15:00',
        'type' => 'Follow-up Meeting',
        'description' => 'Discussing progress on my family law case.',
        'status' => 'approved'
    ],
    [
        'id' => 'app_' . uniqid(),
        'provider_id' => 'provider-123',
        'provider_name' => 'John Lawyer',
        'client_id' => 'client-123',
        'client_name' => 'Michael Wilson',
        'date' => date('Y-m-d', strtotime('-1 day')),
        'time' => '15:00:00',
        'type' => 'Urgent Consultation',
        'description' => 'Need immediate help with an immigration issue.',
        'status' => 'completed'
    ]
];

// Insert sample appointments
$insertCount = 0;
foreach ($sampleAppointments as $appointment) {
    // Map date and time to the correct column names
    $query = "INSERT INTO appointments (
        id, provider_id, provider_name, client_id, client_name, 
        appointment_date, appointment_time, appointment_type, description, status
      ) VALUES (
        '{$appointment['id']}', 
        '{$appointment['provider_id']}', 
        '{$appointment['provider_name']}', 
        '{$appointment['client_id']}', 
        '{$appointment['client_name']}', 
        '{$appointment['date']}', 
        '{$appointment['time']}', 
        '{$appointment['type']}', 
        '{$appointment['description']}', 
        '{$appointment['status']}'
      )";
    
    if (mysqli_query($conn, $query)) {
        $insertCount++;
        echo "<p>Created appointment with ID: {$appointment['id']}</p>";
    } else {
        echo "<p>Error inserting appointment: " . mysqli_error($conn) . "</p>";
    }
}

echo "<p>Added $insertCount sample appointments.</p>";

// Display current appointments
$query = "SELECT id, provider_id, provider_name, client_name, appointment_date, appointment_time, status FROM appointments ORDER BY appointment_date, appointment_time";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h2>Current Appointments</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Provider ID</th><th>Provider</th><th>Client</th><th>Date/Time</th><th>Status</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . substr($row['id'], 0, 10) . "...</td>";
        echo "<td>" . htmlspecialchars($row['provider_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['provider_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['appointment_date'] . ' ' . $row['appointment_time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No appointments found in database.</p>";
}

// Link back to provider dashboard
echo "<p><a href='../HTML/provider-dashboard.html' style='padding: 10px 15px; background-color: #3366cc; color: white; text-decoration: none; border-radius: 5px;'>Go to Provider Dashboard</a></p>";

echo "<p><b>Sample data creation completed at " . date('Y-m-d H:i:s') . "</b></p>";
?> 