#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

include ("account.php");

$userdb = mysqli_connect($hostname, $username, $password, $project);
global $userdb;

//Send Log statement to Tom to log
function doLog($statement)
{
    $logClient = new rabbitMQClient("main.ini","testServer");
    $request = array();
    $request['type'] = "DBLog";
    $request['LogMessage'] = $statement;
    $response = $logClient->publish($request);
}

if (mysqli_connect_errno())
  {
      $error = "Failed to connect to MySQL: " . mysqli_connect_error();
      echo $error;
      doLog($error);
      exit();
  }
echo "Successfully connected to MySQL.".PHP_EOL;

//Check if user is allowed to login
function auth ($user , $pass)
{
    global $userdb;
    $s = "SELECT * from UserLogin where username = \"$user\" && password = \"$pass\"";
    $t = mysqli_query($userdb, $s);

    if (mysqli_num_rows($t) == 0 )
    {
        $error = "User and Password combination not found.".PHP_EOL;
	echo $error;
	doLog($error);
        return false;
    }
    else 
    {
        echo "Successfully Authenticated.".PHP_EOL;
        return true;
    }
}

//Adds a new session Key into db
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
	$error = "No Session Created for user \"$user\"".PHP_EOL;
	echo $error;
	doLog($error);
    }
}

//adds a new user into the UserLogin db
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

//checks if session key is in the db
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

//A function to quickly retrieve the user Id using the Session key
function getUserID($seskey)
{
    global $userdb;
    $s = "Select userID from Session where sessionKey = \"$seskey\"";
    $t = mysqli_query($userdb, $s);
    if (mysqli_num_rows($t) >= 1)
    {
	$result = mysqli_fetch_row($t);
	$r = $result[0];
	return $r;
    }
    else
    {
	$error = "Session key not found in session table, unable to find user ID.".PHP_EOL;
	echo $error;
	return false;
    }
}

//Get info for new package
//If no response nothing is added
function getTrackInfo($trackNumber, $ID)
{
    $RClient = new rabbitMQClient("main.ini","testServer");
    $request = array();
    $request['type'] = "getPack";
    $request['trackNum'] = $trackNumber;
    $request['Message'] = "Need info on track number.";
    $response = $RClient->send_request($request);

    if(!empty($response))
    {
	$dDate = $response['deliveryDate'];
	$dTime = $response['deliveryTime'];
	$zip = $response['zipCode'];
	$dAddr = $response['deliveryAddress'];
	$WP = $response['weatherPredict'];
	
	global $userdb;
	$s = "INSERT INTO Tracker(trackNum,deliveryDate,deliveryTime,zipCode,deliveryAddress,weatherPredict,userID) VALUES (\"$trackNumber\",\"$dDate\",\"$zip\",\"$dAddr\",\"$WP\",\"$ID\")";
	$t = mysqli_query($userdb, $s);
	echo "Successfully added package for user \"$ID\"".PHP_EOL;
	return true;
    }
    else
    {
	$error = "Unable to add package to Tracker due to it not being found.".PHP_EOL;
	echo $error;	
	doLog($error);
	return false;
    }
}

//Used to add a new Package to be tracked
//uses Session Key to find the user and checks if the User already has package
//If the user doesnt have it yet it sends a request to retrieve it from DMZ
function addTracker($trackNum, $seskey)
{
    $userID = getUserID($seskey);
    if($userID == false)
    {
	$error = "Cannot check Tracker table due to userID not being found.".PHP_EOL;
	doLog($error);
	echo $error;
	exit();
    }
    
    global $userdb;
    $s = "Select * from Tracker where userID = \"$userID\" && trackNum = \"$trackNum\"";
    $t = mysqli_query($userdb, $s);
    
    if(mysqli_num_rows($t) == 0)
    {
	echo "Package not found in DB for User, now sending a request to find package.".PHP_EOL;
	$result = getTrackInfo($trackNum, $userID);
	if($result == true)
	{
	    echo "Package successfully added.".PHP_EOL;
	    return "Success";
	}
	else
	{
	    return "PackNotFound";
	}
    }
    else
    {
	$error = "Package already added for user with userID \"$userID\"".PHP_EOL;
	echo $error;
	return "UserHasPack";
    }
}

//Retrieves a full list of all tracked packages for a user
//uses session key to find user and sends back an array that each cell contains an array with the values
function getList($sesKey)
{
    $userID = getUserID($seskey);
    if($userID == false)
    {
	$error = "Cannot get list due to userID not being found.".PHP_EOL;
	echo $error;
	exit();
    }

    global $userdb;
    $s = "Select trackNum, deliveryDate, deliveryTime, zipCode, deliveryAddress, weatherPredict from Tracker where userID = \"$userID\"";
    $t = mysqli_query($userdb, $s);

    if(mysqli_num_rows($t) == 0)
    {
	echo "No Packages found for user \"$userID\".".PHP_EOL;	
	$result = array();
	return $result;
    }
    else
    {
	$result = mysqli_fetch_all($t, MYSQLI_ASSOC);
	return $result;
    }
}

//Retrieves Username from a certian user using session key
function getUser($seskey)
{
    global $userdb;
    $userID = getUserID($seskey);
    if($userID == false)
    {
	$error = "Cannot get user due to userID not being found.".PHP_EOL;
	echo $error;
	exit();
    }
    
    $s = "Select username from UserLogin where id = \"$userID\"";
    $t = mysqli_query($userdb, $s);
    
    if(mysqli_num_rows($t) != 0)
    {
	$result = mysqli_fetch_row($t);
	$r = $result[0];
	return $r;
    }
}

//Retrieves email from a certain user using a Session Key
function getEmail($seskey)
{
    global $userdb;
    $userID = getUserID($seskey);
    if($userID == false)
    {
	$error = "Cannot get email due to userID not being found.".PHP_EOL;
	echo $error;
	exit();
    }
    
    $s = "Select email from UserLogin where id = \"$userID\"";
    $t = mysqli_query($userdb, $s);
    
    if(mysqli_num_rows($t) != 0)
    {
	$result = mysqli_fetch_row($t);
	$r = $result[0];
	return $r;
    }
}

//Remove User
function rmUser($seskey)
{
    global $userdb;
    $userID = getUserID($seskey);
    if($userID == false)
    {
	$error = "Cannot remove user due to userID not being found.".PHP_EOL;
	echo $error;
	exit();
    }
    $s = "Delete from UserLogin where id = \"$userID\"";
    $t = mysqli_query($userdb, $s);
    $s = "Delete from Tracker where userID = \"$userID\"";
    $t = mysqli_query($userdb, $s);
    $s = "Delete from Session where userID = \"$userID\"";
    $t = mysqli_query($userdb, $s);
}

//Remove Session
function rmSession($seskey) 
{
    global $userdb;
    $s = "Delete from Session where sessionKey = \"$seskey\"";
    $t = mysqli_query($userdb, $s);
}

//Remove Package
function rmPackage($trackNum)
{
    global $userdb;
    $s = "Delete from Tracker where trackNum = \"$trackNum\"";
    $t = mysqli_query($userdb, $s);
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
    case "get_user":
      return getUser($request['sessionID']);
    case "get_list":
      return getList($request['sessionID']);
    case "add_package":
      return addTracker($request['trackNum'], $request['sessionID']);
    case "get_email":
      return getUser($request['sessionID']);
    case "remove_user":
      rmUser($request['sessionID']);
    case "remove_package":
      rmPackage($request['trackNum']);
    case "remove_session":
      rmSession($request['sessionID']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("db.ini","testServer");

echo "DBRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "DBRabbitMQServer END".PHP_EOL;
exit();
?>

