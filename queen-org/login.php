<?php
session_start();
include("database.php");

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare SQL to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name, password, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            if ($user['is_verified']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['is_verified'] = true;
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Please verify your email first. <a href='verify.php?email=".urlencode($email)."' class='text-purple-600 hover:underline'>Verify Now</a>";
            }
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .login-container {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        input:focus {
            transition: all 0.3s;
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.45);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="login-container bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-6 text-purple-700">Sign In</h2>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-purple-500 transition">
            </div>
            
            <div>
                <label class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-purple-500 transition">
                <a href="forgot-password.php" class="text-xs text-purple-600 hover:underline">Forgot password?</a>
            </div>
            
            <button type="submit" 
                    class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition duration-300 transform hover:scale-105">
                Sign In
            </button>
            
            <p class="text-center text-gray-600">
                Don't have an account? <a href="register.html" class="text-purple-600 hover:underline">Sign up</a>
            </p>
        </form>
    </div>
</body>
</html>