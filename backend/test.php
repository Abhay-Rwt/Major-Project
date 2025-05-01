<?php
// Enable detailed error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include initialization file if it exists
if (file_exists(__DIR__ . '/inc/init.php')) {
    require_once __DIR__ . '/inc/init.php';
} else {
    echo "Init file not found!<br>";
}

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html");

echo "<h1>Backend Test Page</h1>";

// Check PHP version
echo "<h2>PHP Environment</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Check database connection
echo "<h2>Database Connection</h2>";
if (isset($conn)) {
    echo "Database connection variable exists.<br>";
    
    // Try to query the database
    try {
        $result = mysqli_query($conn, "SHOW TABLES");
        
        if ($result) {
            echo "Connection is working. Tables in database:<br>";
            echo "<ul>";
            while ($row = mysqli_fetch_row($result)) {
                echo "<li>{$row[0]}</li>";
            }
            echo "</ul>";
        } else {
            echo "Query failed: " . mysqli_error($conn) . "<br>";
        }
    } catch (Exception $e) {
        echo "Database error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "Database connection variable does not exist.<br>";
}

// Try to access configuration
echo "<h2>Configuration Files</h2>";
$configFiles = [
    'Database Config' => __DIR__ . '/config/database.php',
    'Initialization' => __DIR__ . '/inc/init.php',
    'Utilities' => __DIR__ . '/inc/utils.php',
];

foreach ($configFiles as $name => $path) {
    echo "$name: " . (file_exists($path) ? "Exists" : "Missing") . "<br>";
}

// Check appointments table
echo "<h2>Appointments Table</h2>";
if (isset($conn)) {
    try {
        $tableCheckQuery = "SHOW TABLES LIKE 'appointments'";
        $tableExists = mysqli_query($conn, $tableCheckQuery);
        
        if ($tableExists && mysqli_num_rows($tableExists) > 0) {
            echo "Appointments table exists.<br>";
            
            // Get appointment count
            $countQuery = "SELECT COUNT(*) as count FROM appointments";
            $countResult = mysqli_query($conn, $countQuery);
            
            if ($countResult) {
                $countRow = mysqli_fetch_assoc($countResult);
                echo "Number of appointments in database: " . $countRow['count'] . "<br>";
                
                // Get some sample appointments
                if ($countRow['count'] > 0) {
                    $sampleQuery = "SELECT id, provider_name, client_name, date, status FROM appointments LIMIT 5";
                    $sampleResult = mysqli_query($conn, $sampleQuery);
                    
                    if ($sampleResult) {
                        echo "Sample appointments:<br>";
                        echo "<table border='1' cellpadding='5'>";
                        echo "<tr><th>ID</th><th>Provider</th><th>Client</th><th>Date</th><th>Status</th></tr>";
                        
                        while ($row = mysqli_fetch_assoc($sampleResult)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['provider_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "</tr>";
                        }
                        
                        echo "</table>";
                    }
                }
            }
        } else {
            echo "Appointments table does not exist.<br>";
            
            // Try to create the table
            echo "Attempting to create the appointments table...<br>";
            
            $createTableQuery = "CREATE TABLE IF NOT EXISTS appointments (
                id VARCHAR(255) PRIMARY KEY,
                provider_id VARCHAR(255) NOT NULL,
                provider_name VARCHAR(255) NOT NULL,
                client_id VARCHAR(255) NOT NULL,
                client_name VARCHAR(255) NOT NULL,
                date DATE NOT NULL,
                time TIME NOT NULL,
                type VARCHAR(100) NOT NULL,
                description TEXT,
                status VARCHAR(50) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if (mysqli_query($conn, $createTableQuery)) {
                echo "Appointments table created successfully.<br>";
            } else {
                echo "Failed to create appointments table: " . mysqli_error($conn) . "<br>";
            }
        }
    } catch (Exception $e) {
        echo "Error checking appointments table: " . $e->getMessage() . "<br>";
    }
}

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>";
?> 