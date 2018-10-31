 no warnings; # turn off warnings
 
 use XML::Compile::WSDL11;
 use XML::Compile::SOAP11;
 use XML::Compile::Transport::SOAPHTTP;
 use HTTP::Request;
 use HTTP::Response;
 use Data::Dumper;
 
 #Configuration
 $access = " Add License Key Here";
 $userid = " Add User Id Here";
 $passwd = " Add Password Here";
 $operation = "ProcessFreightPickup";
 $endpointurl = " Add URL Here";
 $wsdlfile = " Add Wsdl File Here ";
 $schemadir = "Add Schema Location Here";
 $outputFileName = "XOLTResult.xml";
 
 sub processFreightPickup
 {
 	my $request =
 	{
 		UPSSecurity =>  
	  	{
		   UsernameToken =>
		   {
			   Username => "$userid",
			   Password => "$passwd"
		   },
		   ServiceAccessToken =>
		   {
			   AccessLicenseNumber => "$access"
		   }
	  	},
	  	
	  	Request =>
	  	{
	  		RequestOption => '1'
	  	},
 		
 		DestinationCountryCode =>'US',
 		Requester =>
 		{
 			Name => 'ABC Associates',
 			AttentionName => 'Mr. ABC',
 			EMailAddress => 'wbb6tdf@ups.com',
 			Phone =>
 			{
 				Number => '123456789',
 				Extension => '345'
 			},
 			ThirdPartyIndicator => '1'
 		},
 		
 		ShipFrom =>
 		{
 			Name => 'ABC Associates',
 			AttentionName => 'Mr. ABC',
 			Address => 
 			{
 				AddressLine => ['AddressLine1'],
 				City => 'Roswell',
 				StateProvinceCode => 'GA',
 				CountryCode => 'US',
 				PostalCode => '30076'
 			},
 			Phone =>
 			{
 				Number => '123456789',
 				Extension => '345'
 			}
 		},
 		
 		ShipTo =>
 		{
 			 AttentionName => '',
 			 Address =>
 			 {
 			 	 AddressLine => [''],
 			 	 City => '',
 			 	 StateProvinceCode => '',
 			 	 CountryCode => ''
 			 }
 		},
 		
 		ShipmentDetail =>
 		{
 			PackagingType =>
 			{
 				  Code => 'BAR',
 				  Description => 'BARREL'
 			},
 			NumberOfPieces => '5',
 			DescriptionOfCommodity => 'fdqwd',
 			Weight =>
 			{
 				UnitOfMeasurement =>
 				{
 					Code => 'LBS',
 					Description => 'Pounds'
 				},
 				Value => '250.78'
 			},
 		},
 		
 		PickupDate => '20111019',
 		EarliestTimeReady => '0800',
 		LatestTimeReady => '1800'
 	};
 	
 	return $request;
 }
 
 sub processFreightCancelPickup
 {
 	my $request =
 	{
 		UPSSecurity =>  
	  	{
		   UsernameToken =>
		   {
			   Username => "$userid",
			   Password => "$passwd"
		   },
		   ServiceAccessToken =>
		   {
			   AccessLicenseNumber => "$access"
		   }
	  	},
	  	
 		Request =>
 		{
 			RequestOption => '1'
 		},
 		PickupRequestConfirmationNumber => ''
 	};
 	
 	return $request;
 }
 
 my $wsdl = XML::Compile::WSDL11->new( $wsdlfile );
 my @schemas = glob "$schemadir/*.xsd";
 $wsdl->importDefinitions(\@schemas);
 my $operation = $wsdl->operation($operation);
 my $call = $operation->compileClient(endpoint => $endpointurl);
 
 if($operation->name() eq "ProcessFreightPickup")
 {
 	($answer , $trace) = $call->(processFreightPickup() , 'UTF-8');	
 }
 else
 {
 	($answer , $trace) = $call->(processFreightCancelPickup() , 'UTF-8');
 }
 
 if($answer->{Fault})
 {
	print $answer->{Fault}->{faultstring} ."\n";
	print Dumper($answer);
	print "See XOLTResult.xml for details.\n";
		
	# Save Soap Request and Response Details
	open(fw,">$outputFileName");
	$trace->printRequest(\*fw);
	$trace->printResponse(\*fw);
	close(fw);
 }
 else
 {
	# Get Response Status Description
    print "Description: " . $answer->{Body}->{Response}->{ResponseStatus}->{Description} . "\n"; 
        
    # Print Request and Response
    my $req = $trace->request();
	print "Request: \n" . $req->content() . "\n";
	my $resp = $trace->response();
	print "Response: \n" . $resp->content();
		
	# Save Soap Request and Response Details
	open(fw,">$outputFileName");
	$trace->printRequest(\*fw);
	$trace->printResponse(\*fw);
	close(fw);
}
 