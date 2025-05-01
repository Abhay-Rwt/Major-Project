<?php
// Include initialization file
require_once __DIR__ . '/../inc/init.php';

// Set content type to HTML
header("Content-Type: text/html");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Authentication Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #2563eb; }
        .box { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        code { background: #f5f5f5; padding: 2px 4px; border-radius: 3px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: #2563eb; }
        button { padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Authentication Test Page</h1>
    
    <div class="box">
        <h2>Session Information</h2>
        <?php
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
            echo "<p class='success'>Session is active</p>";
            echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
            echo "<p>User Type: " . $_SESSION['user_type'] . "</p>";
            
            if (isset($_SESSION['session_id'])) {
                echo "<p>Session ID: " . substr($_SESSION['session_id'], 0, 10) . "...</p>";
            }
        } else {
            echo "<p class='error'>No active session found</p>";
        }
        ?>
    </div>
    
    <div class="box">
        <h2>Test Login</h2>
        <p>Use this form to test logging in:</p>
        <form id="login-form">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" required>
            </div>
            <div style="margin-top: 10px;">
                <label for="password">Password:</label>
                <input type="password" id="password" required>
            </div>
            <div style="margin-top: 15px;">
                <button type="submit">Login</button>
            </div>
        </form>
        <div id="login-result" style="margin-top: 15px;"></div>
    </div>
    
    <div class="box">
        <h2>Check Current Authentication</h2>
        <button id="check-auth">Check Authentication</button>
        <div id="auth-result" style="margin-top: 15px;"></div>
    </div>
    
    <div class="box">
        <h2>Logout</h2>
        <button id="logout">Logout</button>
        <div id="logout-result" style="margin-top: 15px;"></div>
    </div>
    
    <script>
        // Handle login form submission
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const resultDiv = document.getElementById('login-result');
            
            resultDiv.innerHTML = '<p class="info">Logging in...</p>';
            
            fetch('../api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                }),
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <p class="success">Login successful!</p>
                        <p>User: ${data.user.name}</p>
                        <p>Email: ${data.user.email}</p>
                        <p>User Type: ${data.user.user_type}</p>
                        <p>Session ID: ${data.session_id ? data.session_id.substring(0, 10) + '...' : 'None'}</p>
                    `;
                    
                    // Store session ID
                    localStorage.setItem('session_id', data.session_id);
                    
                    // Refresh page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    resultDiv.innerHTML = `<p class="error">Login failed: ${data.error || 'Unknown error'}</p>`;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<p class="error">Error: ${error.message}</p>`;
            });
        });
        
        // Check current authentication
        document.getElementById('check-auth').addEventListener('click', function() {
            const resultDiv = document.getElementById('auth-result');
            
            resultDiv.innerHTML = '<p class="info">Checking authentication...</p>';
            
            // Get session ID from localStorage
            const sessionId = localStorage.getItem('session_id');
            
            fetch('../api/check-auth.php', {
                method: 'GET',
                headers: {
                    'Authorization': sessionId ? `Bearer ${sessionId}` : '',
                    'Content-Type': 'application/json'
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.authenticated) {
                    resultDiv.innerHTML = `
                        <p class="success">User is authenticated!</p>
                        <pre>${JSON.stringify(data.user, null, 2)}</pre>
                    `;
                } else {
                    resultDiv.innerHTML = `<p class="error">Not authenticated: ${data.message || 'Unknown reason'}</p>`;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<p class="error">Error: ${error.message}</p>`;
            });
        });
        
        // Logout
        document.getElementById('logout').addEventListener('click', function() {
            const resultDiv = document.getElementById('logout-result');
            
            resultDiv.innerHTML = '<p class="info">Logging out...</p>';
            
            fetch('../api/logout.php', {
                method: 'POST',
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<p class="success">${data.message || 'Logout successful'}</p>`;
                    
                    // Clear session ID
                    localStorage.removeItem('session_id');
                    sessionStorage.removeItem('session_id');
                    
                    // Refresh page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    resultDiv.innerHTML = `<p class="error">Logout failed: ${data.error || 'Unknown error'}</p>`;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<p class="error">Error: ${error.message}</p>`;
            });
        });
    </script>
</body>
</html> 