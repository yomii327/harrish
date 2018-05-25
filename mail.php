<?php
$to = "gautam.manish@fxbytes.com";
$subject = "Test mail";
$message = "Hello! This is a simple email message.";
$from = "someonelse@gmail.com";
$headers = "From:" . $from;
$mail=mail('manish4377@gmail.com','Test','Hello Dost');
if($mail)
{
	echo 'Sent';
}
else
{
	echo 'Not sent';
	}
?> 