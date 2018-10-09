<!DOCTYPE html>

<html>
	<head>

		<style>	
body{
font-family: monospace;                
            }
#loginbox{
    position: relative;
height: 100%;
width: 100%;
margin-left: auto;
margin-right: auto;
margin-top:20%;
top: 0px;
}	
            input{
                width: 100%;

            }
#topbar{
width:100%;
height: 75px;
position: absolute;
top:0px;
left: 0px;
right: 0px;
background-color:#004499;
}
#bottomline{
width:90%;
height: 1px;
position: absolute;
bottom:80px;
background-color:#000000;
left:0px;
right:0px;
margin-left:5%;
margin-right:5%;
}

</style>
        
</head>
    	<body>
	<div id="topbar"></div>
	<div id="loginbox">
	<h1 align="center">Welcome to R.O.P.</h1>
        
    	<?php
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$client = new rabbitMQClient("../RMQ.ini","database");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}


$request = array();
$request['type'] = "sign_up";
$request['username'] = $_GET["username"];
$request['password'] = $_GET["password"];
$request['email'] = $_GET["email"];
$request['message'] = $msg;
$response = $client->send_request($request);

if ($response == true){
echo '<p style="font-size:30px; color: green" align="center">Account Created.</p>';}
else{
echo '<p style="font-size:30px; color: red" align="center">Username or E-mail is invalid!</p>';
}

echo '<p style="font-size:20px; color: blue" align=center>Redirecting to Login Page...</p>';
header('Refresh: 5; URL=../index.html');


?>

	</div>
        <div id="bottomline"></div>
    
    </body>


</html>
