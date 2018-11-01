<?php
Require_once("resource/header.html");
?>

<?php

if(isset($_COOKIE["sessionkey"])) {
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
	$request['type'] = "validate_session";
	$request['sessionID'] = $_COOKIE["sessionkey"];

	$response = $client->send_request($request);

	if ($response == true){

    		header("Location: welcome.php");
	} 
}
?>
	<div id="loginbox">
	<div id="logo"><img width="100%" src="resource/ROP.png"></div>
	<h1 id="header" align="center">Please Log In</h1><br>
        
        <?php
        //echo time();
	//echo date('r', time());

	//print_r($_COOKIE);	
	//echo $_COOKIE["sessionkey"];

?>        
    	<form name="login" action="login.php" method="get">     	
		Enter User:
        	<br><input placeholder="Username" autocomplete="on" type="text" name="username">
        	<br>
        	<br>
       	 	Enter Password: 
       	 	<br><input placeholder="Password" autocomplete="on" type="password" name="password">
        	<br><br>
            <a align="center" style="font-size: 12px;" href="signup">Don't have an account? Sign up Here.</a>
        	<br><br>

        <input align="center" id="submit" name="signin" type="submit" value="Sign In" label="Sign In">
	
        </form>  
        
	</div>

<?php 

Require_once("resource/footer.html");
?>
