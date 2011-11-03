<?php
include("params.php");
 $to = "recipient@example.com";
 $subject = "Hi!";
 $body = "Hi,\n\nHow are you?";
 $headers = "From: {$from_email}\r\n" .
     "X-Mailer: php";
 if (mail($to, $subject, $body, $headers)) {
   echo("<p>Message sent!</p>");
  } else {
   echo("<p>Message delivery failed...</p>");
  }
 ?>
