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
#logout{
	position: absolute;
	top:20px;
	right: 10px;
	width:120px;

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
    min-width: 1000px;
}
	        #WeatherIcon{
                	position: absolute;
                	width: 120px;
                	height: 120px;
                	margin-top:15px;
                	margin-left:15px;
		} 
		#textBox{
			position: relative;
                	margin-left:150px;
                	top:10px;
                	min-width: 800px;
		}
            		#tracking{
                		position: relative;
				float:left;
				width:60%;
				min-width:60%;
            		}
            		#weather{
               			position: relative;
                		float:left;
				width:45%;
				
				max-width:40%;
			}
			#status{
				position: relative;
                		float:left;
				width:50%;
                		top:15px;
				min-width:50%;
			}
			#zip{
			position: relative;
                	float:left;
			width:50%;
                	top:15px;
			min-width:33%;
			}
            		#deliv{
			position: relative;
                	float:left;
			width:40%;
                	top:20px;
			min-width:40%;
            			}
			#pickup{
			position: relative;
                	float:left;
			width:60%;
                	top:25px;
			min-width:60%;
            			}
a{
color:white;}
            #date{
                
                position: relative;
                margin-left:650px;
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


$request = array();
$request['type'] = "get_user";
$request['sessionID'] = $_COOKIE["sessionkey"];
$response = $client->send_request($request);

                echo "<div id='welcome'>Welcome, $response</div>";
                ?>
	</div>


<button type="button" id="logout" onclick="myFunction()">Log Out</button>

<p id="demo"></p>

<script>
function myFunction() {
	document.cookie = "sessionkey=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
	document.location.href = "/";
}
</script>


<div style="position: absolute;top:0px;left:10%; margin-top:75px; height: 80%; width: 80%" id="pkgmanager" >
		<p align="center">Please Click on Tracking Number to Reschedule Your Package.</p>
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
		if (sizeof($response) == 0){
		echo '<p align="center" style="font-size: 50px;">Please Add a Package by clicking the "+" in the top left corner.</p>';
		}
		for ($x = 1; $x <= sizeof($response); $x++){
		/*if (substr($response[$x-1]["deliveryTime"],0,2) > 12){
			$hours = (substr($response[$x-1]["deliveryTime"],0,2)-12);
			$time = ($hours.":".substr($response[$x-1]["deliveryTime"],3,2)." P.M.");
		}
		else if ($time = substr($response[$x-1]["deliveryTime"],0,2)<10){
			$time = substr($response[$x-1]["deliveryTime"],1,4)." A.M.";
		}
		else{
			$time = substr($response[$x-1]["deliveryTime"],0,5)." A.M.";
		}*/
		
		echo '<li id='.($x-1).'>';
                echo '<div id="WeatherIcon"><img height="100%" src="resource/ROP.png"></div>';
///Row 1
		echo '<div id="textBox">';
                echo '<div id="tracking"><a href="https://www.ups.com/track?loc=en_US&tracknum='.$response[$x-1]["trackNum"].'&requester=WT/trackdetails">Tracking Number: '.$response[$x-1]["trackNum"].'</a></div>';
///Row 2
		echo '<div id="weather">Expected Weather: '.$response[$x-1]["weatherPredict"].'</div>';
                echo '<div id="status">Delivery Status: '.$response[$x-1]["status"].'</div>';
                echo '<div id="zip">Zipcode: '.$response[$x-1]["zipCode"].'</div>';
       
///Row 3 
                echo '<div id="deliv">Delivery Date: '.$response[$x-1]["deliveryDate"].'</div>';   
                echo '<div id="pickup">Expected Pickup: '.$response[$x-1]["pickUpDate"].'</div>';   
		echo '</div>';
                echo '</li>';    
		echo '<script>';
		echo 'if ("'.$response[$x-1]["weatherPredict"].'" == "rain"){';		
		echo 'document.getElementById("'.($x-1).'").style.backgroundColor = "red";}';
		echo '</script>';
		}
		?>
                 
                          
                          
            
            </ul>
            </div>

    
    </body>


</html>

