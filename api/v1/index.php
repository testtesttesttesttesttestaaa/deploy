<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

//Load Composer's autoloader
require '../../vendor/autoload.php';
 

function message($msg, $http_code){
    http_response_code($http_code);
    header('Content-Type: application/json');
    echo json_encode(array('message' => $msg));
} 

// load envs
$dotenv = Dotenv::createImmutable('../../');
$dotenv->load();
$EMAIL = $_ENV["EMAIL"];

session_start();

// Set the time limit to 60 seconds
$time_limit = 60;

// Set the maximum number of requests per time limit
$max_requests = 5;

// Get the client's IP address
$client_ip = $_SERVER['REMOTE_ADDR'];

// Load the requests data from file
$requests_file = 'requests.json';
if (file_exists($requests_file)) {
    $requests = json_decode(file_get_contents($requests_file), true);
} else {
    $requests = array();
}

// Check if the IP address has exceeded the limit
$request_data = isset($requests[$client_ip]) ? $requests[$client_ip] : array('count' => 0, 'last_request' => null);
$request_count = $request_data['count'];
$last_request = $request_data['last_request'];
$now = time();

if ($last_request && $now - $last_request < $time_limit) {
    if ($request_count >= $max_requests) {
        message("Too many requests from your IP address. Please try again later.", 429);
        exit;
    } 
    
    else {
        $request_count++;
        $requests[$client_ip]['count'] = $request_count;
        file_put_contents($requests_file, json_encode($requests));
    }
} else {
    $requests[$client_ip] = array(
        'count' => 1,
        'last_request' => $now
    );
    file_put_contents($requests_file, json_encode($requests));
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

   $expected_keys = array('title', 'description','is_private', 'sysinfo');

   if(array_diff($expected_keys, array_keys($_POST)) === array()){
       
        
        $title = $_POST['title'];
        $description = $_POST["description"];
        $name = $_POST['name'];
        $is_private = $_POST['is_private'];
        $sysinfo = $_POST['sysinfo'];
        
        if(isset($_POST['email']))
            $reporter_email = $_POST['email'];
          
        if(isset($_POST['name']))
            $name = $_POST['name'];
      
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
        $mail->isHtml(true);
       
        
        // Set the email content
        $mail->setFrom('bug.reporter.bot@outlook.com', 'Bug Reporter Bot');
        $mail->addAddress($EMAIL);

        if(isset($name))
          $mail->Subject = $name.": ".$title;
        else
          $mail->Subject = $title;

        $mail->Body = $description . "<br><br>" .  "-------------------- <br>" . "Email Private Status: " . $is_private . "<br> System Info: ". $sysinfo;
        
        
   
        // Send the email
        if ($mail->send()) {
            message("E-mail has been sent successfully.", 200);
            
                 
        } else {
            message("An error occurred while sending E-mail." , 500);
     
        }

        $mail->ClearAddresses();

        if(isset($reporter_email)){
            $mail->addAddress($reporter_email);
            $mail->Subject = 'Thanks ! You have successfully reported the bug.';
            $mail->Body = file_get_contents('email-template.html') ;
          
            
            if ($mail->send() == false) {
               http_response_code(503);
               message("An error occurred while sending E-mail.", 500);
            } 
            
        }

    }
    
    else{
         message("Error: you have to send required variables as POST request.", 500);
    }
} 

else{
    message("Invalid Request Method.", 400);
}

?>
