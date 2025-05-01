<?php
// Include initialization file
require_once __DIR__ . '/../inc/init.php';

// Sample providers to add
$providers = [
    [
        'first_name' => 'Rahul',
        'last_name' => 'Sharma',
        'email' => 'rahul.sharma@example.com',
        'phone' => '9876543210',
        'password' => 'password123',
        'provider_type' => 'Criminal Lawyer',
        'license_number' => 'CR98765432',
        'bio' => 'Dedicated criminal defense lawyer with over 8 years of experience in handling complex criminal cases. Strong track record of successful verdicts.',
        'services_offered' => 'Criminal Defense, DUI Cases, Drug Offenses, Assault Cases'
    ],
    [
        'first_name' => 'Priya',
        'last_name' => 'Patel',
        'email' => 'priya.patel@example.com',
        'phone' => '8765432109',
        'password' => 'password123',
        'provider_type' => 'Family Lawyer',
        'license_number' => 'FL87654321',
        'bio' => 'Compassionate family lawyer helping clients navigate difficult family situations with sensitivity and legal expertise.',
        'services_offered' => 'Divorce, Child Custody, Adoption, Family Disputes'
    ],
    [
        'first_name' => 'Amit',
        'last_name' => 'Verma',
        'email' => 'amit.verma@example.com',
        'phone' => '7654321098',
        'password' => 'password123',
        'provider_type' => 'Corporate Lawyer',
        'license_number' => 'CL76543210',
        'bio' => 'Experienced corporate lawyer specializing in business law, mergers & acquisitions, and contract negotiations for companies of all sizes.',
        'services_offered' => 'Business Formation, Contract Review, Mergers & Acquisitions, Corporate Compliance'
    ]
];

// Add each provider to the database
$success_count = 0;
$errors = [];

foreach ($providers as $provider) {
    // Check if email already exists
    $email = $provider['email'];
    $check_query = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $errors[] = "Provider with email {$email} already exists.";
        continue;
    }
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    // Hash password
    $hashed_password = hash_password($provider['password']);
    
    // Insert user record
    $user_query = "INSERT INTO users (first_name, last_name, email, phone, password, user_type) 
                   VALUES ('{$provider['first_name']}', '{$provider['last_name']}', '{$provider['email']}', 
                   '{$provider['phone']}', '$hashed_password', 'provider')";
    
    if (mysqli_query($conn, $user_query)) {
        $user_id = mysqli_insert_id($conn);
        
        // Insert provider details
        $details_query = "INSERT INTO provider_details (user_id, provider_type, license_number, bio, services_offered) 
                         VALUES ('$user_id', '{$provider['provider_type']}', '{$provider['license_number']}', 
                         '{$provider['bio']}', '{$provider['services_offered']}')";
        
        if (mysqli_query($conn, $details_query)) {
            // Commit transaction
            mysqli_commit($conn);
            $success_count++;
        } else {
            // Rollback on error
            mysqli_rollback($conn);
            $errors[] = "Error adding provider {$provider['email']}: " . mysqli_error($conn);
        }
    } else {
        // Rollback on error
        mysqli_rollback($conn);
        $errors[] = "Error adding provider {$provider['email']}: " . mysqli_error($conn);
    }
}

// Return response
echo "<h2>Provider Addition Results</h2>";
echo "<p>Successfully added $success_count providers.</p>";

if (!empty($errors)) {
    echo "<h3>Errors:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
} 