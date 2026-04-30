<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require __DIR__ . '/../config/mail.php';

function sendWelcomeEmail($toEmail, $toName) {
    global $config;

    $firstName = explode(' ', trim($toName))[0];

    $body = "Hello $firstName,\n\nWelcome to biniru! Your account has been successfully created.\n\nYou can now start exploring our vinyl catalog and discover your next favorite record.\n\nHappy listening!\n\nBest regards,\nThe biniru Team";

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

        $mail->isHTML(false);
        $mail->Subject = 'Welcome to biniru, ' . $firstName . '!';
        $mail->Body    = $body;

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
