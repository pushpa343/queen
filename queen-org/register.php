<?php
session_start();
include("database.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $gender = $_POST['gender'];

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        die("Email already registered. Please use a different email.");
    }
    $check->close();

    // Check if username exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        die("Username already taken. Please choose another.");
    }
    $check->close();

    // Generate OTP and hash password
    $otp = rand(100000, 999999);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

    // Store in session for verification
    $_SESSION['register_data'] = [
        'name' => $name,
        'username' => $username,
        'phone' => $phone,
        'email' => $email,
        'password' => $hashed_password,
        'gender' => $gender,
        'otp' => $otp,
        'otp_expiry' => $expiry
    ];

    // Send OTP email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'newqueentailorshop@gmail.com';
        $mail->Password   = 'duriasflzswuldoc';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('newqueentailorshop@gmail.com', 'New Queen Tailor');
        $mail->addAddress($email, $name);
        $mail->Subject = 'Your OTP Verification Code';
        $mail->Body    = "Hi $name,\n\nYour OTP is: $otp\nIt is valid for 15 minutes.\n\nThank you!";

        $mail->send();
        header("Location: verify.php?email=" . urlencode($email));
        exit();
    } catch (Exception $e) {
        die("Mail Error: {$mail->ErrorInfo}");
    }
}
?>