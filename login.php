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
        
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("RMQ.ini","database");
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
$request['username'] = $_GET["user"];
$request['password'] = $_GET["pass"];
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
//print_r($response);

if ($response == "1"){
echo '<p style="font-size:30px; color: green" align="center">Logged In Successfully.';}
else{
echo '<p style="font-size:30px; color: red" align=center>Login Declined</p>';
}

echo "\n\n";
?>
</div>
        <div id="bottomline"></div>
    
    </body>


</html>

