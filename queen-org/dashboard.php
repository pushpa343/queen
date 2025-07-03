<?php
session_start();
include("database.php");

// Check if user is logged in and verified
if (!isset($_SESSION['user_id']) || !$_SESSION['is_verified']) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include('index.html'); ?>
    
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-purple-700 mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p class="text-gray-700">You're successfully logged in to your account.</p>
            
            <div class="mt-6">
                <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    Logout
                </a>
            </div>
        </div>
    </div>
</body>
</html>