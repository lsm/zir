<?php

error_reporting(E_ALL | E_STRICT);

$name = 'To - LSM';
$email = 'senmiaoliu@ethos.com.cn';
$to = "$name <$email>";
$from = "From-Senmiao-Liu<senmiaoliu@ethos.com.cn>";
$subject = "Testing - Creation issue from email with attachment";
$message = 'Send mail with attachement.';

$fileatt = "tmp/16.jpeg";
$fileatttype = "image/jpeg";
$fileattname = "16.jpg";


$headers = "From: $from";
        $data = file_get_contents($fileatt); 
    
        $semi_rand = md5( time() ); 
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
    
        $headers .= "\nMIME-Version: 1.0\n" . 
                    "Content-Type: multipart/mixed;\n" . 
                    " boundary=\"{$mime_boundary}\"";
    
        $message = "This is a multi-part message in MIME format.\n\n" . 
                "--{$mime_boundary}\n" . 
                "Content-Type: text/plain; charset=\"utf-8\"\n" . 
                "Content-Transfer-Encoding: 7bit\n\n" . 
                $message . "\n\n";
    
        $data = chunk_split( base64_encode( $data ) );
                 
        $message .= "--{$mime_boundary}\n" . 
                 "Content-Type: {$fileatttype};\n" . 
                 " name=\"{$fileattname}\"\n" . 
                 "Content-Disposition: attachment;\n" . 
                 " filename=\"{$fileattname}\"\n" . 
                 "Content-Transfer-Encoding: base64\n\n" . 
                 $data . "\n\n" . 
                 "--{$mime_boundary}--\n"; 
        
        try { 
            mail( $to, $subject, $message, $headers );
            echo "<p>The email was sent.</p>"; 
        }
        catch(Exception $e) { 
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
?>