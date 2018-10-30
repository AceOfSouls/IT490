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
            <div id="topbar"><a href="addpackage.php"><div id="addpkg">+</div></a>
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
$request['type'] = "get_user";
$request['sessionID'] = $_COOKIE["sessionkey"];
$response = $client->send_request($request);

                echo "<div id='welcome'>Welcome, $response</div></div>";
                ?>




<div style="position: absolute;top:0px;left:10%; margin-top:75px; height: 80%; width: 80%" id="pkgmanager" >
             <ul>
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
		$request['type'] = "get_list";
		$request['sessionID'] = $_COOKIE["sessionkey"];
		$response = $client->send_request($request);

		//print_r($response);
		//echo sizeof($response);
		for ($x = 1; $x <= sizeof($response); $x++){
		if (substr($response[$x-1]["deliveryTime"],0,2) > 12){
			$hours = (substr($response[$x-1]["deliveryTime"],0,2)-12);
			$time = ($hours.":".substr($response[$x-1]["deliveryTime"],3,2)." P.M.");
		}
		else if ($time = substr($response[$x-1]["deliveryTime"],0,2)<10){
			$time = substr($response[$x-1]["deliveryTime"],1,4)." A.M.";
		}
		else{
			$time = substr($response[$x-1]["deliveryTime"],0,5)." A.M.";
		}
		
		echo "<li>";
                echo '<div id="WeatherIcon"></div>';
                echo '<div id="tracking">Tracking Number: '.$response[$x-1]["trackNum"].'</div>';
                echo '<div id="delivery">Address: '.$response[$x-1]["deliveryAddress"].', '.$response[$x-1]["zipCode"].'</div>';       
                echo '<div id="condition">Expected Condition: '.$response[$x-1]["weatherPredict"].'</div>';   
                echo '<div id="date">Expected Delivery: '.$response[$x-1]["deliveryDate"].', '.$time.'</div>';   
                echo '</li>';    

		}
		?>
                 
                          
                          
            
            </ul>
            </div>

    
    </body>


</html>
