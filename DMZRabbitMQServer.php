#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');




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
    case "getPack":
      return sendToDMZ($request);
}

  return array("returnCode" => '0', 'message'=>"Server received request and processed");

}


$server = new rabbitMQServer("Main2.ini","testServer");

echo "MainRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "MainRabbitMQServer END".PHP_EOL;
exit();
?>

