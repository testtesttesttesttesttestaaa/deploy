<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// should add currently dir reader __DIR__
require_once '/phpmailer/src/PHPMailer.php';
require_once '/phpmailer/src/SMTP.php';

// Create a new PHPMailer instance
$mail = new PHPMailer();

// Set the SMTP settings for the Outlook account
$mail->isSMTP();
$mail->Host = 'smtp-mail.outlook.com';
$mail->SMTPAuth = true;
$mail->Username = '';
$mail->Password = '';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// Set the email content
$mail->setFrom('', 'Bug Reporter Bot');
$mail->addAddress('');
$mail->Subject = 'Bug Report';
$mail->Body = 'Please fix the following issue: ...';

// Send the email
if ($mail->send()) {
    echo 'Email sent successfully!';
} else {
    echo 'Error sending email: ' . $mail->ErrorInfo;
}

?>
