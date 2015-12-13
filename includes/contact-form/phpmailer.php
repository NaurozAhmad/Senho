<?php 
    
    require_once 'phpmailer/PHPMailerAutoload.php';

    $phpmailer = new PHPMailer();

    $sender = $_POST['email'];
    $message = $_POST['message'];

    $phpmailer->IsSMTP();
    $phpmailer->Host = "mail.consiliumleadership.com";
    $phpmailer->SMTPAuth = true;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Port = 26;
    $phpmailer->Username = "hello@consiliumleadership.com";
    $phpmailer->Password = "EJ(nDmpKncs)";

    $phpmailer->From = $sender;
    $phpmailer->FromName = 'Portfolio - From: ' . $sender;
    $phpmailer->addAddress('hassan@consiliumleadership.com', 'Consilium Contacted');
    $phpmailer->addReplyTo($sender, 'Reply Info');

    $phpmailer->Subject = 'Portfolio Contact Form Message';
    $phpmailer->Body = $message;


    if(!$phpmailer->Send()) {
        echo "Mailer Error: " . $phpmailer->ErrorInfo;

    }
    else {
        header("Location: contact-thanks.php");
    }


?>