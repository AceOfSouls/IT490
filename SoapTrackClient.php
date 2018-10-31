<?php

  //Configuration
  $access = "DD51945E636B0D08";
  $userid = "APOLLO42 M";
  $passwd = "Fight9091479";
  $wsdl = "Track.wsdl";
  $operation = "ProcessTrack";
  $endpointurl = '';
  $outputFileName = "XOLTResult.xml";

  function processTrack()
  {
      //create soap request
    $req['RequestOption'] = '15';
    $tref['CustomerContext'] = 'Add description here';
    $req['TransactionReference'] = $tref;
    $request['Request'] = $req;
    $request['InquiryNumber'] = '1ZW7267W0200151730';
    $request['TrackingOption'] = '02';

 	echo "Request.......\n";
	print_r($request);
    echo "\n\n";
    return $request;
  }

  try
  {

    $mode = array
    (
         'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
         'trace' => 1
    );

    // initialize soap client
  	$client = new SoapClient($wsdl , $mode);

  	//set endpoint url
  	$client->__setLocation($endpointurl);


    //create soap header
    $usernameToken['Username'] = $userid;
    $usernameToken['Password'] = $passwd;
    $serviceAccessLicense['AccessLicenseNumber'] = $access;
    $upss['UsernameToken'] = $usernameToken;
    $upss['ServiceAccessToken'] = $serviceAccessLicense;

    $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
    $client->__setSoapHeaders($header);


    //get response
  	$resp = $client->__soapCall($operation ,array(processTrack()));

    //get status
    echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";

    //save soap request and response to file
    $fw = fopen($outputFileName , 'w');
    fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
    fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
    fclose($fw);

    //print_r($resp);
    $a = get_object_vars($resp);
    $array = json_decode(json_encode($resp, true));
    echo $resp->Response;
  }
  catch(Exception $ex)
  {
  	print_r ($ex);
  }

?>
