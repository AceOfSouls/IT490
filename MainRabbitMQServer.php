#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function sendToDB($request)
{
	$client = new rabbitMQClient("db.ini","testServer");
	if (isset($argv[1]))
	{
		$msg = $argv[1];
	}
	else
	{
		$msg = "test message";
	}
	
	$response = $client->send_request($request);
	if($response == true)
	{
		echo "yes";
	}
	else
	{
		echo "no";
	}
	return $response;
}

function sendOneWayDB ($request)
{
	$client = new rabbitMQClient("db.ini","testServer");
	if (isset($argv[1]))
	{
		$msg = $argv[1];
	}
	else
	{
		$msg = "test message";
	}
	$response = $client->publish($request);
}

function sendToDMZ ($request)
{
	$client = new rabbitMQClient("dmz.ini","testServer");
	if (isset($argv[1]))
	{
		$msg = $argv[1];
	}
	else
	{
		$msg = "test message";
	}
	$response = $client->send_request($request);
	return $response;
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return sendToDB($request);
    case "sign_up":
      return sendToDB($request);
    case "create_session":
      sendOneWayDB($request);
    case "validate_session":
      return sendToDB($request);
    case "get_user":
      return sendToDB($request);
    case "get_list":
      return sendToDB($request);
    case "add_package":
      return sendToDB($request);
    case "get_email":
      return sendToDB($request);
    case "remove_user":
      sendOneWayDB($request);
    case "remove_package":
      sendOneWayDB($request);
    case "remove_session":
      sendOneWayDB($request);
    case "getPack":
      return sendToDMZ($request);
}

  return array("returnCode" => '0', 'message'=>"Server received request and processed");

}


$server = new rabbitMQServer("Main.ini","testServer");

echo "MainRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "MainRabbitMQServer END".PHP_EOL;
exit();
?>

