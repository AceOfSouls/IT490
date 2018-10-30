#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("resource/RMQ.ini","database");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "login";
$request['username'] = $_GET["username"];
$request['password'] = $_GET["password"];
//$request['username'] = "hi";
//$request['password'] = "heelo";
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
print_r($response);


?>
<?php
$message = "Our New app";
        $to='davidjcschnepf@gmail.com';
        $subject="New Mail";
        $from = 'johnny@rop.com';
        $body='hey';
        $headers = "From: " . strip_tags($from) . "\r\n";
        $headers .= "Reply-To: ". strip_tags($from) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

mail($to,$subject,$body,$headers);
?>
