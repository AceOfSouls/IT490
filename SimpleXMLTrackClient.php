<?php
ob_start();
// Configuration
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
	
	$trackRequestXML->addChild ( "TrackingNumber", "1ZW7267W0200151730" );
	
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
		$xml=simplexml_load_string($response) or die("Error: Cannot create object");

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
?>

