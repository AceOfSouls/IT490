#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

include ("account.php");

$db = mysqli_connect($hostname, $username, $password, $project);
global $db;

if (mysqli_connect_errno())
  {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      exit();
  }
echo "Successfully connected to MySQL.<br>";

function auth ($user , $pass){

    global $db;
    $s = "SELECT * from UserLogin where username = \"$user\" && password = \"$pass\"";
    $t = mysqli_query($db, $s);

    //echo "<br><br>SQL Statement: ".$s;

    if (mysqli_num_rows($t) == 0 )
        {
            echo "<br>User and Password combination not found.<br";
            return false;
        }

    else {
        //echo "<br>Successfully Authenticated.";
        return true;
    }
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
      return auth($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

