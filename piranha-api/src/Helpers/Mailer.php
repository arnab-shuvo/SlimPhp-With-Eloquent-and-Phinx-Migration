<?php

namespace App\Helpers;
use PHPMailer\PHPMailer\PHPMailer;

date_default_timezone_set('Etc/UTC');

class Mailer
{
    public function send_mail($body,$subject, $mailto)
    {

        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 1;
        //Set the hostname of the mail server
        $mail->Host = 'mail.gandi.net';
        //Set the SMTP port number - likely to be 25, 465 or 587
        $mail->Port = 465;
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        // Enable SSL
        $mail->SMTPSecure = "ssl";
        $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        //Username to use for SMTP authentication
        $mail->Username = 'platform@netstax.io';
        //Password to use for SMTP authentication
        $mail->Password = 'Platform6522!';
        //Set who the message is to be sent from
//        $mail->setFrom($adminmail, 'admin');
        $mail->From = 'platform@netstax.io';
        $mail->FromName = "Admin";


        //Set an alternative reply-to address
//        $mail->addReplyTo('replyto@example.com', 'First Last');


        //Set who the message is to be sent to
        $mail->addAddress('arnabshuvo430@gmail.com');
        //Set the subject line
        $mail->Subject = 'Reset password';
        //Read an HTML message body from an external file, convert referenced images to embedded,



        $mail->msgHTML($body);
//        $mail->Body = $body;
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
//        return $mail;

        if (!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'Message sent!';
        }
    }
}