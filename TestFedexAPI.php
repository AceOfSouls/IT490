#!/usr/bin/php
<?php
require_once('fedex-common.php5');

// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 6.0.0

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = "TrackService_v16.wsdl";
ini_set("soap.wsdl_cache_enabled", "0");
$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
$request['WebAuthenticationDetail'] = array('UserCredential' =>array('Key' => 'UgYvVwdDRFHPf5LO', 'Password' => 'NOurG0hnZTY93FkD1WNsqVdXE'));

$request['ClientDetail'] = array('AccountNumber' => '510087780',
'MeterNumber' => '119097737');

$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Track Request v16 using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'trck',
	'Major' => '16',
	'Intermediate' => '0',
	'Minor' => '0'
);
$request['SelectionDetails'] = array(
'CarrierCode'=> 'FDXE',
'PackageIdentifier' => array(
	'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
	'Value' => '123456789012')); // Replace 'XXX' with a valid tracking identifier
$request['IncludeDetailedScans'] = true;
try
{
	if(setEndpoint('changeEndpoint'))
	{
		$newLocation = $client->__setLocation(setEndpoint('endpoint'));
	}
	$response = $client->track($request);
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
    {
	
    	echo '<table border="1">';
    	echo '<tr><th>Tracking Details</th><th>&nbsp;</th></tr>'.PHP_EOL;
	
	var_dump($response);
	$zipCode = "10001";
	$deliveryDate = $response->CompletedTrackDetails->TrackDetails[0]->DatesOrTimes[0]->DateOrTimestamp;
	$status = $response->CompletedTrackDetails->TrackDetails[0]->LastUpdatedDestinationAddress->City;
        //trackDetails($response->TrackDetails, '');
		echo '</table>'.PHP_EOL;
        //printSuccess($client, $response);
    }
    else
    {
        printError($client, $response);
    }
    writeToLog($client);    // Write to log file
} 
	catch (SoapFault $exception) {
  		printFault($exception, $client);
	}
?>
