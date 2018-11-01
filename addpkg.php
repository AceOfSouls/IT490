<html>
	<head>
		<style>	
body{
    font-family:Helvetica;                
            }

#loginbox{
	height:100%;
	width: 300px;
	margin-left: auto;
	margin-right: auto;
	display: flex;
	align-items: center;
	margin-top:-75px;
}	
	
input{
	width: 100%;
	font-size:40px;
	text-align:center;
}        
     
#topbar{
	width:100%;
	height: 75px;
	position: absolute;
	top:0px;
	left: 0px;
	right: 0px;
	background-color:#769fb6;
}
#bottomline{
	width:90%;
	height: 1px;
	position: absolute;
	bottom:10%;
	background-color:#000000;
	left:0px;
	right:0px;
	margin-left:5%;
	margin-right:5%;
}
ul{
    padding-left: 0px;
    margin-top:20px;
}
li{
    list-style: none;
    font-size: 20px;
    width:100%;
    color:white;
    background-color: #9dbbae;
    height: 150px;
    margin-bottom: 20px;
    box-shadow: 3px 3px #000000;
    min-width: 800px;
}
            #WeatherIcon{
                position: absolute;
                width: 120px;
                height: 120px;
                background-color: #188fa7;
                margin-top:15px;
                margin-left:15px;
            } 
            #tracking{
                position: relative;
                margin-left:150px;
                top:20px;
                min-width: 800px;
            }
            #delivery{
                position: relative;
                margin-left:150px;
                top:40px;
                min-width: 800px;
            }
            #condition{
                
                position: relative;
                margin-left:150px;
                top:60px;
                float: left;
                min-width: 800px;
                
            }
            #date{
                
                position: relative;
                margin-left:450px;
                top:35px;
                float: left;
                min-width: 800px;
            }
            #addpkg{
                position: absolute;
                color: white;
                font-size:80px;
                height:75px;
                margin-top: -15px;
                left:15px;
            
            }
            #welcome{
                text-align: center;
                color: white;
                margin-top: 20px;
                font-size:30px;
            }
            #title{
                text-align: center;
                margin-top: 20px;
                font-size:60px;
            }
		
		</style>
        
	<script>
	function validateForm() {
	var username = document.forms["login"]["username"].value;
	var password = document.forms["login"]["password"].value;
	
	if (username == "") {
       		alert("Username Cannot be blank");
        	return false;
    	}

        if (password == "") {
        	alert("Password Cannot be blank");
        	return false;
    	}
        if (email == "") {
        	alert("An E-mail is required.");
        	return false;
    	}
    	if (email.includes("@") == false){
    		alert("Please enter a valid email.");
    		return false;
	}
    	if (email.includes(".") == false){
    	alert("Please enter a valid email.");
    	return false;
	}
               
        else{
            return true;
        }
} 
</script>
       
        
</head>
    	<body>
            <div id="topbar"><div id="addpkg">+</div>
            <?php 
                echo "<div id='welcome'>New Package</div></div>";
                ?>




<div style="position: absolute;top:0px;left:10%; margin-top:75px; height: 80%; width: 80%" id="pkgmanager" ><br><br>
 
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
$request['type'] = "add_package";
$request['sessionID'] = $_COOKIE["sessionkey"];
$request['trackNum'] = $_GET["pkgnum"];

$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
//print_r($response);

if ($response == "UserHasPack"){
	echo "Package as already Been Added!";
	header( "Refresh:5; url=/addpackage.php", true, 303);
	//header("Location: addpackage.php");
}
else if ($response == "PackNotFound"){
	echo "Package Not Found.";
	header( "Refresh:5; url=/addpackage.php", true, 303);
	//header("Location: addpackage.php");
}
else if($response == "Success"){
	echo "Package added Successfully!";
	header( "Refresh:5; url=/", true, 303);
	//header("Location: welcome.php");
}

?>
            </div>

    
    </body>


</html>
