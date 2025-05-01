<?php
// Enable detailed error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include initialization file
require_once __DIR__ . '/inc/init.php';

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html");

echo "<h1>Sample Data Creation</h1>";

// Create a sample appointment with string-based provider ID (for backward compatibility)
echo "<h2>Creating Sample Appointments (String Provider ID)</h2>";

$sampleAppointmentsString = [
    [
        'id' => 'app_' . uniqid(),
        'provider_id' => 'provider-123', // String-based ID for backward compatibility
        'provider_name' => 'Dr. Jane Smith',
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
        'provider_name' => 'Dr. Jane Smith',
        'client_id' => 'client-456',
        'client_name' => 'Sarah Johnson',
        'date' => date('Y-m-d', strtotime('+1 day')),
        'time' => '10:15:00',
        'type' => 'Follow-up Meeting',
        'description' => 'Discussing progress on my family law case.',
        'status' => 'approved'
    ]
];

// Insert string-based sample appointments
$stringInsertCount = 0;
foreach ($sampleAppointmentsString as $appointment) {
    $query = "INSERT INTO appointments (
        id, provider_id, provider_name, client_id, client_name, 
        date, time, type, description, status
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
        $stringInsertCount++;
        echo "<p>Created appointment with ID: {$appointment['id']}</p>";
    } else {
        echo "<p>Error creating appointment: " . mysqli_error($conn) . "</p>";
    }
}

echo "<p>Added $stringInsertCount sample appointments with string-based provider ID.</p>";

// Display current appointments to verify
$query = "SELECT id, provider_id, provider_name, client_name, date, time, status FROM appointments 
          WHERE provider_id = 'provider-123'
          ORDER BY date, time";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h2>Appointments for provider-123</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Provider ID</th><th>Provider</th><th>Client</th><th>Date/Time</th><th>Status</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . substr($row['id'], 0, 10) . "...</td>";
        echo "<td>" . htmlspecialchars($row['provider_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['provider_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date'] . ' ' . $row['time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No appointments found for provider-123.</p>";
}

echo "<p><b>Sample data creation completed at " . date('Y-m-d H:i:s') . "</b></p>";
?> 