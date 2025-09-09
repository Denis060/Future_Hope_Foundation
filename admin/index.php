<?php
// Start session
session_start();

// Include database configuration and functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Redirect if user is already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Process login form
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Check if user exists
        $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to dashboard
                redirect('dashboard.php');
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "Invalid username. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Future Hope</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin-style.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo img {
            max-height: 80px;
        }
        
        .login-title {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-form .form-control {
            height: 50px;
            padding: 10px 15px;
        }
        
        .btn-login {
            height: 50px;
            font-weight: 500;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <?php $settings = getSettings($conn); ?>
                <?php if (!empty($settings['site_logo'])): ?>
                <img src="../<?php echo $settings['site_logo']; ?>" alt="<?php echo $settings['site_name']; ?>">
                <?php else: ?>
                <h2><?php echo $settings['site_name'] ?? 'Future Hope'; ?></h2>
                <?php endif; ?>
            </div>
            
            <div class="login-title">
                <h4>Admin Login</h4>
                <p>Please enter your credentials to log in.</p>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post" action="" class="login-form">
                <div class="mb-4">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-login">Login</button>
                </div>
            </form>
            
            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $settings['site_name'] ?? 'Future Hope'; ?>. All Rights Reserved.</p>
                <p><a href="../index.php">Return to Website</a></p>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
