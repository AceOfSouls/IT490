<?php 
Require_once("resource/header.html");

?>
	<div id="loginbox">
        

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

if ($response == true){

$key = SHA1($_GET["username"].time());
$sessionkey = $key;

$cookie_name = "sessionkey";
$cookie_value = $sessionkey;
setcookie($cookie_name, $sessionkey); // 86400 = 1 day

echo $sessionkey."<br>";
echo $_COOKIE["sessionkey"];

$request = array();
$request['type'] = "create_session";
$request['username'] = $_GET["username"];
$request['sessionkey'] = $sessionkey;
//$client->send_request($request);
$response = $client->publish($request);

echo '<p style="font-size:30px; color: green" align="center">Logged In Successfully.';
header("Location: index.php");
}
else{
echo '<p style="font-size:30px; color: red" align=center>Login Declined</p>';
}

echo "\n\n";
?>
</div>
<?php 

Require_once("resource/footer.html");
?>

