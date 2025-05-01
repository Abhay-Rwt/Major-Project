<?php
// Include initialization file
require_once __DIR__ . '/../inc/init.php';

// Check the users table
$users_query = "SELECT * FROM users LIMIT 10";
$users_result = mysqli_query($conn, $users_query);

echo "<h2>Users in Database</h2>";

if (!$users_result) {
    echo "<p>Error querying users: " . mysqli_error($conn) . "</p>";
} else if (mysqli_num_rows($users_result) === 0) {
    echo "<p>No users found in the database.</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>User Type</th></tr>";
    
    while ($row = mysqli_fetch_assoc($users_result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['user_type'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// Check the sessions table
$sessions_query = "SELECT s.*, u.first_name, u.last_name, u.email 
                  FROM sessions s 
                  JOIN users u ON s.user_id = u.id 
                  LIMIT 10";
$sessions_result = mysqli_query($conn, $sessions_query);

echo "<h2>Active Sessions</h2>";

if (!$sessions_result) {
    echo "<p>Error querying sessions: " . mysqli_error($conn) . "</p>";
} else if (mysqli_num_rows($sessions_result) === 0) {
    echo "<p>No active sessions found in the database.</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Session ID</th><th>User</th><th>Email</th><th>Expires</th></tr>";
    
    while ($row = mysqli_fetch_assoc($sessions_result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['expires_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} 