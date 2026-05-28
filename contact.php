<?php
// Used PHPMailer in GitHub API to send emails
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$sanitized_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Validate and Sanitize Data
    $required_fields = ['name', 'email', 'subject', 'message'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $errors[] = "The " . ucfirst($field) . " field is required.";
        } else {
            $sanitized_data[$field] = htmlspecialchars(trim($_POST[$field]), ENT_QUOTES, 'UTF-8');
        }
    }

    // 2. If validation passes, attempt transmission over SMTP
    if (empty($errors)) {
        $mail = new PHPMailer(true);

        try {
            // Server Settings Configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '424004144@ntc.edu.ph';           // SENDER's email
            $mail->Password   = 'wcwt wpae fenf rewo';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients Configuration
            $mail->setFrom('424004144@gmail.com', 'Message From Website'); 
            $mail->addAddress('shikuretoo@gmail.com'); // RECEIVER's email
            $mail->addReplyTo($sanitized_data['email'], $sanitized_data['name']);

            // Message that is going to be sent to the receiver
            $mail->isHTML(true);
            $mail->Subject = 'Message logged from website (FinalsWebDev): ' . $sanitized_data['subject'];
            
            $mail->Body    = "
                <h3>Message Logged From Ultimate Gaming Hub Website</h3>
                <p><strong>Sender Name:</strong> {$sanitized_data['name']}</p>
                <p><strong>Sender Email:</strong> {$sanitized_data['email']}</p>
                <p><strong>Subject Segment:</strong> {$sanitized_data['subject']}</p>
                <p><strong>Message Content:</strong></p>
                <div style='background: #f7fafc; padding: 15px; border-left: 4px solid #ff007f;'>
                    " . nl2br($sanitized_data['message']) . "
                </div>
            ";

            $mail->send();
        
        // Error handling
        } catch (Exception $e) {
            $errors[] = "Message transmission failed. Mailer Error: {$mail->ErrorInfo}";
        }
    }
} else {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact System - Ultimate Gaming Hub</title>
    <link rel="stylesheet" href="design.css">
    <style>
        .response-container { display: block; max-width: 700px; margin: 80px auto; }
        .back-link { display: inline-block; margin-top: 15px; color: #ff007f; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="game-tab response-container">
        <?php if (!empty($errors)): ?>
            <h2>Transmission Interrupted</h2>
            <div style="color: #ff3131; border-left: 4px solid #ff3131; padding: 15px; background: rgba(40,15,30,0.6);">
                <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
            </div>
            <a href="index.html" class="back-link">&larr; Return to Form</a>
        <?php else: ?>
            <h2>Message Forwarded</h2>
            <p>Thank you, <strong><?php echo $sanitized_data['name']; ?></strong>. Your message has been sent straight to our team's inbox!</p>
            <a href="index.html" class="back-link">&larr; Return to Mainframe</a>
        <?php endif; ?>
    </div>
</body>
</html>