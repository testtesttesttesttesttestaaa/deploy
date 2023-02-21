<?php
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\SMTP; 
use PHPMailer\PHPMailer\Exception; 
 

// should add currently dir reader __DIR__
require './phpmailer/src/Exception.php'; 
require './phpmailer/src/PHPMailer.php'; 
require './phpmailer/src/SMTP.php'; 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    

    if(isset($_POST['title']) && isset($_POST["description"])){
        $title = $_POST['title'];
        $description = $_POST["description"];
        
        // Set the response content type to JSON
        header('Content-Type: application/json');
      
        // Create a new PHPMailer instance
        $mail = new PHPMailer();
        
        // Set the SMTP settings for the Outlook account
        $mail->isSMTP();
        $mail->Host = 'smtp-mail.outlook.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv("MAIL_USERNAME");
        $mail->Password = getenv("MAIL_PASSWORD");
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        // Set the email content
        $mail->setFrom('bug.reporter.bot@outlook.com', 'Bug Reporter Bot');
        $mail->addAddress('alirezaaraby5@gmail.com');
        $mail->Subject = $title;
        $mail->Body = $description;
        
        
        // Send the email
        if ($mail->send()) {
             echo json_encode(array('message' => "E-mail has been sent successfully"));
        } else {
                  echo json_encode(array('message' => "An error occured while sending E-mail"));
        }

    }
    
    else{
         echo json_encode(array('message' => "Error: you have to send title and description as POST request" ));
    }
     

 
} 

else{
    echo json_encode(array('message' => "Invalid Request Method."));
}


?>
