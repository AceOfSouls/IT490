#!/usr/bin/php

<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("resource/RMQ.ini","database");

$request = array();
$request['type'] = "get_email";
$response = $client->send_request($request);
//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;

for ($x = 1; $x <= sizeof($response); $x++){
		
$message = "Rain On Package Notifier";
	echo "E-mail Sending to...".strtolower($response[$x-1]["email"]);
        $to=strtolower($response[$x-1]["email"]);
        
	$subject="Oh No! Your Package! - From ROP.com";
        $from = 'TheHolyUmbrella@rop.com';
        $body='Hey, this is a heads up from the Package Umbrella, Giving you a warning about your package<br><br>Your Package in tracking number is: <b>'.$response[$x-1]["trackNum"].'</b><br><br>Please Either prepare for this Event or reschedule for Pickup at your nearest UPS Store/Location.<br><a href="https://www.ups.com/track?loc=en_US&tracknum='.$response[$x-1]["trackNum"].'&requester=WT/trackdetails">Please Click This Link and reschedule your Package through UPS.</a>';
        $headers = "From: " . strip_tags($from) . "\r\n";
        $headers .= "Reply-To: ". strip_tags($from) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

if (mail($to,$subject,$body,$headers)==true){

echo "\nEmail Sent";

}
else {

$statement = "Failed to send email to ".$response[$x-1]["email"]." ";
$request = array();
$request['type'] = "error";
$request['LogMessage'] = $statement;
//$response = $client->send_request($request);
$response = $client->publish($request);


}    }



?>


