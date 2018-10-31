#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function lookUp($trackNum)
{
$accessLicenseNumber = "DD51945E636B0D08";
$userId = "APOLLO42 M";
$password = "Fight9091479";

$endpointurl = 'https://wwwcie.ups.com/ups.app/xml/Track';
$outputFileName = "XOLTResult.xml";

try {
	
	// Create AccessRequest XMl
	$accessRequestXML = new SimpleXMLElement ( "<AccessRequest></AccessRequest>" );
	$accessRequestXML->addChild ( "AccessLicenseNumber", $accessLicenseNumber );
	$accessRequestXML->addChild ( "UserId", $userId );
	$accessRequestXML->addChild ( "Password", $password );
	
	// Create TrackRequest XMl
	$trackRequestXML = new SimpleXMLElement ( "<TrackRequest></TrackRequest  >" );
	$request = $trackRequestXML->addChild ( 'Request' );
	$request->addChild ( "RequestAction", "Track" );
	$request->addChild ( "RequestOption", "activity" );
	
	$trackRequestXML->addChild ( "TrackingNumber", $trackNum );
	
	$requestXML = $accessRequestXML->asXML () . $trackRequestXML->asXML ();
	
	$form = array (
			'http' => array (
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content' => "$requestXML" 
			) 
	);
	
	// get request
	$request = stream_context_create ( $form );
	$browser = fopen ( $endpointurl, 'rb', false, $request );
	if (! $browser) {
		throw new Exception ( "Connection failed." );
	}
	
	// get response
	$response = stream_get_contents ( $browser );
	fclose ( $browser );
	
	if ($response == false) {
		throw new Exception ( "Bad data." );
	} else {
		// save request and response to file
		$fw = fopen ( $outputFileName, 'w' );
		fwrite ( $fw, "Request: \n" . $requestXML . "\n" );
		fwrite ( $fw, "Response: \n" . $response . "\n" );
		fclose ( $fw );
		
		// get response status
		$resp = new SimpleXMLElement ( $response );
		echo $resp->Response->ResponseStatusDescription . "\n";
		//echo $response;
		//echo $response;
		$xml=simplexml_load_string($response) or die("Error: Cannot create object");
		print_r($xml);
		$zipCode = $xml->Shipment->ShipTo->Address->PostalCode . "\n";
		echo $zipCode;
		$pickUpDate = $xml->Shipment->PickupDate . "\n";
		echo $pickUpDate;
		$deliveryDate = $xml->Shipment->Package->DeliveryDate . "\n";
		echo $deliveryDate;
		$status = $xml->Shipment->Package->Activity[0]->Status->StatusType->Description;
		echo $status;

	}
	Header ( 'Content-type: text/xml' );
} catch ( Exception $ex ) {
	echo $ex;
}

function lookUpWeather($zip, $date)
{
$zipCode = $zip;
    $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
    $yql_query = "select * from weather.forecast where woeid in (select woeid from geo.places(1) where text={$zipCode})";
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";

    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $json = curl_exec($session);

    // Convert JSON to PHP object
     $phpObj =  json_decode($json);
    var_dump($phpObj);

    $originalDate = "$date";
    $Date = date("d M Y", strtotime($originalDate));

    $result = "";
    for ($x = 0; $x <= 9; $x++)
    {
        $s = $phpObj->query->results->channel->item->forecast[$x]->date;
        if ($Date == $s)
        {
	    $s = $phpObj->query->results->channel->item->forecast[$x]->text;
            $result = $s;
        }
    }
    if ($result != "")
    {
	return $result;
    }
    else
    {
        $result = "No weather at this time";
	return $result;
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
      return doLogin($request['username'],$request['password']);
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

