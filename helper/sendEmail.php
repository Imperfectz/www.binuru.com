<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require __DIR__ . '/../config/mail.php';

function sendWelcomeEmail($toEmail, $toName) {
    global $config;

    $firstName = htmlspecialchars(explode(' ', trim($toName))[0] ?? '');

    $plainBody = "Hello $firstName,\n\nWelcome to biniru! Your account has been successfully created.\n\nYou can now start exploring our vinyl catalog and discover your next favorite record.\n\nHappy listening!\n\nBest regards,\nThe biniru Team";

    $htmlBody = <<<HTML
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body{background:#0d0d0d;margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;color:#e9e9e9}
        .email-wrap{max-width:600px;margin:36px auto;background:#0f0f0f;padding:28px;border-radius:8px;text-align:center;border:1px solid #222}
        .logo{width:84px;height:auto;margin:0 auto 12px;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.6))}
        h1{font-size:22px;margin:8px 0;color:#ffffff}
        p{color:#cfcfcf;line-height:1.5;margin:8px 0}
        .cta{display:inline-block;margin-top:18px;padding:10px 18px;background:#f5e642;color:#111;text-decoration:none;border-radius:4px;font-weight:700}
        .footer{font-size:12px;color:#8f8f8f;margin-top:22px}
        a{color:inherit}
    </style>
</head>
<body>
  <div class="email-wrap">
    <img src="cid:biniru_logo" alt="biniru" class="logo">
    <h1>Welcome to biniru, $firstName!</h1>
    <p>Thanks for signing up — your account is ready. Explore our curated vinyl selection and find something that moves you.</p>
    <a class="cta" href="#">Browse Records</a>
    <p class="footer">© 2025 biniru · Vinyl Records</p>
  </div>
</body>
</html>
HTML;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port       = $config['port'];

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($toEmail, $toName);

        $logoPath = __DIR__ . '/../img/biniru_logo.png';
        if (file_exists($logoPath)) {
            try {
                $mail->addEmbeddedImage($logoPath, 'biniru_logo');
            } catch (Exception $e) {
            }
        }

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to biniru, ' . $firstName . '!';
        $mail->Body    = $htmlBody;
        $mail->AltBody = $plainBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendWelcomeEmail error: ' . $mail->ErrorInfo);
        return false;
    }
}

function sendEmail($toEmail, $toName, $subject, $body) {
    global $config;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port       = $config['port'];

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('sendEmail error: ' . $mail->ErrorInfo);
        return false;
    }
}
