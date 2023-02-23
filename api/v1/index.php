<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

//Load Composer's autoloader
require '../../vendor/autoload.php';
 
// load envs
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();
$EMAIL = $_ENV["email"];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(isset($_POST['title']) && isset($_POST["description"]) && isset($_POST["name"]) ){
        $title = $_POST['title'];
        $description = $_POST["description"];
        $name = $_POST['name'];
        
        if(isset($_POST['email']))
            $reporter_email = $_POST['email'];

        // Set the response content type to JSON
        header('Content-Type: application/json');
      
        // Create a new PHPMailer instance
        $mail = new PHPMailer();
        
        // Set the SMTP settings for the Outlook account
        $mail->isSMTP();
        $mail->Host = 'smtp-mail.outlook.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        // Set the email content
        $mail->setFrom('bug.reporter.bot@outlook.com', 'Bug Reporter Bot');
        $mail->addAddress($EMAIL);
        $mail->Subject = $title;
        $mail->Body = $description;
        
        
        // Send the email
        if ($mail->send()) {
             echo json_encode(array('message' => "E-mail has been sent successfully"));
        } else {
                  echo json_encode(array('message' => "An error occurred while sending E-mail"));
        }

        $mail->ClearAddresses();
        $mail->ClearAttachments();

        if(isset($reporter_email)){
            $mail->addAddress($reporter_email);
            $mail->Subject = 'Thanks ! You have successfully reported the bug';
            $mail->Body = 'Dear '.$name.', \n You have successfully reported the bug. We will get in touch with you. \n Summand Team,';
            if ($mail->send()) {
             echo json_encode(array('message' => "E-mail has been sent successfully"));
            } 
            else {
                  echo json_encode(array('message' => "An error occurred while sending E-mail"));
            }
        }


    }
    
    else{
         echo json_encode(array('message' => "Error: you have to send required variables as POST request" ));
    }
     

 
} 

else{
    echo json_encode(array('message' => "Invalid Request Method."));
}


?>
