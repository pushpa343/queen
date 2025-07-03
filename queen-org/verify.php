<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

include("database.php");

if (!isset($_SESSION['register_data'])) {
    die("Invalid verification request");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['verify'])) {
        // Verification logic
        $email = $_SESSION['register_data']['email'];
        $user_otp = $_POST['otp'];
        $stored_otp = $_SESSION['register_data']['otp'];
        $expiry = $_SESSION['register_data']['otp_expiry'];

        if ($user_otp == $stored_otp && strtotime($expiry) >= time()) {
            // Save to database
            $data = $_SESSION['register_data'];
            $stmt = $conn->prepare("INSERT INTO users (name, username, phone, email, password, gender, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssss", $data['name'], $data['username'], $data['phone'], $data['email'], $data['password'], $data['gender']);
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $data['name'];
                $_SESSION['is_verified'] = true;
                
                unset($_SESSION['register_data']);
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Database error: " . $stmt->error;
            }
        } else {
            $error = "Invalid or expired OTP";
        }
    } elseif (isset($_POST['resend'])) {
        // Resend OTP logic
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        
        $_SESSION['register_data']['otp'] = $otp;
        $_SESSION['register_data']['otp_expiry'] = $expiry;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.mailersend.net';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'MS_y35aWq@test-y7zpl98ny7o45vx6.mlsender.net';
            $mail->Password   = 'mssp.0aUtNQs.3zxk54v02pqgjy6v.TJhtodV';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('steverogers130427@gmail.com', 'New Queen Tailor'); // ⚠️ Replace with verified domain email
            $mail->addAddress($_SESSION['register_data']['email'], $_SESSION['register_data']['name']);
            $mail->isHTML(true);
            $mail->Subject = 'Your New OTP Verification Code';
            $mail->Body    = nl2br("Hi {$_SESSION['register_data']['name']},\n\nYour new OTP is: $otp\nIt is valid for 15 minutes.\n\nThank you!");

            $mail->send();
            $message = "New OTP sent to your email.";
        } catch (Exception $e) {
            $error = "Mail Error: {$mail->ErrorInfo}";
        }
    }
}

$email = $_SESSION['register_data']['email'];
?>
