<?php

  //Configuration
  $access = " Add License Key Here";
  $userid = " Add User Id Here";
  $passwd = " Add Password Here";
  $wsdl = " Add Wsdl File Here ";
  $operation = "ProcessFreightPickup";
  $endpointurl = " Add URL Here";
  $outputFileName = "XOLTResult.xml";

  function processFreightPickup()
  {

      //create soap request
    $requestoption['RequestOption'] = '1';
    $request['Request'] = $requestoption;

    $request['DestinationCountryCode'] = 'US';
    $requester['Name'] = 'ABC Associates';
    $requester['AttentionName'] = 'Mr. ABC';
    $requester['EMailAddress'] = 'wbb6tdf@ups.com';
    $phone1['Number'] = '123456789';
    $phone1['Extension'] = '345';
    $requester['Phone'] = $phone1;
    $requester['ThirdPartyIndicator'] = '1';
    $request['Requester'] = $requester;

    $shipfrom['Name'] = 'ABC Associates';
    $shipfrom['AttentionName'] = 'Mr. ABC';
    $addressline1 = array
    (
        'AddressLine1'
    );
    $address1['AddressLine'] = $addressline1;
    $address1['City'] = 'Roswell';
    $address1['StateProvinceCode'] = 'GA';
    $address1['CountryCode'] = 'US';
    $address1['PostalCode'] = '30076';
    $shipfrom['Address'] = $address1;
    $phone2['Number'] = '123456789';
    $phone2['Extension'] = '345';
    $shipfrom['Phone'] = $phone2;
    $request['ShipFrom'] = $shipfrom;

    $shipto['AttentionName'] = '';
    $address2['AddressLine'] = '';
    $address2['City'] = '';
    $address2['StateProvinceCode'] = '';
    $address2['CountryCode'] = '';
    $shipto['Address'] = $address2;
    $request['ShipTo'] = $shipto;


    $packagingtype['Code'] = 'BAR';
    $packagingtype['Description'] = 'BARREL';
    $shipmentdetail['PackagingType'] = $packagingtype;
    $shipmentdetail['NumberOfPieces'] = '5';
    $shipmentdetail['DescriptionOfCommodity'] = 'fdqwd';
    $unit['Code'] = 'LBS';
    $unit['Description'] = 'Pounds';
    $weight['UnitOfMeasurement'] = $unit;
    $weight['Value'] = '250.78';
    $shipmentdetail['Weight'] = $weight;
    $request['ShipmentDetail'] = $shipmentdetail;

    $request['PickupDate'] = '20111019';
    $request['EarliestTimeReady'] =  '0800';
    $request['LatestTimeReady'] = '1800';

    echo "Request.......\n";
	print_r($request);
    echo "\n\n";
    return $request;

  }

  function processFreightCancelPickup()
  {

    //create soap request
    $requestoption['RequestOption'] = '1';
    $request['Request'] = $requestoption;
    $request['PickupRequestConfirmationNumber'] = '';

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

    if(strcmp($operation,"ProcessFreightPickup") == 0 )
    {
        //get response
  	$resp = $client->__soapCall('ProcessFreightPickup',array(processFreightPickup()));

         //get status
        echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";

        //save soap request and response to file
        $fw = fopen($outputFileName , 'w');
        fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
        fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
        fclose($fw);

    }
    else
    {
        $resp = $client->__soapCall('ProcessFreightCancelPickup',array(processFreightCancelPickup()));

        //get status
        echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";

  	//save soap request and response to file
  	$fw = fopen($outputFileName ,'w');
  	fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
        fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
        fclose($fw);
    }

  }
  catch(Exception $ex)
  {
  	print_r ($ex);
  }

?>
