#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

include ("account.php");

$userdb = mysqli_connect($hostname, $username, $password, $project);
global $userdb;

if (mysqli_connect_errno())
  {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      exit();
  }
echo "Successfully connected to MySQL.".PHP_EOL;

function auth ($user , $pass){

    global $userdb;
    $s = "SELECT * from UserLogin where username = \"$user\" && password = \"$pass\"";
    $t = mysqli_query($userdb, $s);

    //echo "<br><br>SQL Statement: ".$s;

    if (mysqli_num_rows($t) == 0 )
        {
            echo "User and Password combination not found.".PHP_EOL;
            return false;
        }

    else {
        echo "Successfully Authenticated.".PHP_EOL;
        return true;
    }
}

function signup ($user, $pass, $email){
    global $userdb;
    $s = "SELECT * from UserLogin where username = \"$user\" || email = \"$email\"";
    $t = mysqli_query($userdb, $s);

    if (mysqli_num_rows($t) >= 1)
    {
	echo "User/email is already on database.".PHP_EOL;
	return false;
    }
    else
    {
	$a = "INSERT INTO UserLogin(username,password,email) VALUES (\"$user\",\"$pass\",\"$email\")";
	mysqli_query($userdb, $a);
	echo "Successfully added User.".PHP_EOL;
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
    case "sign_up":
      return signup($request['username'],$request['password'],$request['email']);
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

