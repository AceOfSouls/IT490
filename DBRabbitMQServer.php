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

function createSession ($user, $skey)
{
    global $userdb;
    $s = "SELECT id from UserLogin where username = \"$user\"";
    $t = mysqli_query($userdb, $s);
    if(mysqli_num_rows($t) > 0)
    {
        $result = mysqli_fetch_row($t);
	$r = $result[0];
	$a = "INSERT INTO Session(sessionKey, userID) VALUES (\"$skey\",\"$r\")";
	mysqli_query($userdb, $a);
	echo "Session Created!".PHP_EOL;
    }
    else
    {
	echo "No Session Created!".PHP_EOL;
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

function doValidate($seskey)
{
    global $userdb;
    $s = "Select * from Session where sessionKey = \"$seskey\"";
    $t = mysqli_query($userdb, $s);
    if (mysqli_num_rows($t) >= 1)
    {
	echo "Session found!".PHP_EOL;
	return true;
    }
    else
    {
	echo "Session not found!".PHP_EOL;
	return false;
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
    case "create_session":
      createSession($request['username'], $request['sessionkey']);
    case "validate_session":
      return doValidate($request['sessionID']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("db.ini","testServer");

echo "DBRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "DBRabbitMQServer END".PHP_EOL;
exit();
?>

