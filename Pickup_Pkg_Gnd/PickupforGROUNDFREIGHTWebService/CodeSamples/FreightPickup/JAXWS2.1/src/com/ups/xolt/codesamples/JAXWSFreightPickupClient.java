/* 
 ** 
 ** Filename: JAXWSFreightPickupClient.java 
 ** Authors: United Parcel Service of America
 ** 
 ** The use, disclosure, reproduction, modification, transfer, or transmittal 
 ** of this work for any purpose in any form or by any means without the 
 ** written permission of United Parcel Service is strictly prohibited. 
 ** 
 ** Confidential, Unpublished Property of United Parcel Service. 
 ** Use and Distribution Limited Solely to Authorized Personnel. 
 ** 
 ** Copyright 2009 United Parcel Service of America, Inc.  All Rights Reserved. 
 ** 
 */
package com.ups.xolt.codesamples;


import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileWriter;
import java.net.URL;
import java.util.*;

import javax.xml.ws.BindingProvider;
import javax.xml.ws.WebServiceRef;
import com.ups.wsdl.xoltws.freightpickup.v1.FreightPickupPortType;
import com.ups.wsdl.xoltws.freightpickup.v1.FreightPickupService;
import com.ups.wsdl.xoltws.freightpickup.v1.PickupErrorMessage;
import com.ups.xmlschema.xoltws.freightpickup.v1.FreightPickupResponse;
import com.ups.xmlschema.xoltws.freightpickup.v1.ExistingShipmentIDType;
import com.ups.xmlschema.xoltws.upss.v1.UPSSecurity;
import com.ups.xmlschema.xoltws.upss.v1.UPSSecurity.ServiceAccessToken;
import com.ups.xmlschema.xoltws.upss.v1.UPSSecurity.UsernameToken;
import com.ups.xmlschema.xoltws.common.v1.RequestType;
import com.ups.xmlschema.xoltws.common.v1.TransactionReferenceType;
import com.ups.xmlschema.xoltws.freightpickup.v1.AddressType;
import com.ups.xmlschema.xoltws.freightpickup.v1.FreightPickupRequest;
import com.ups.xmlschema.xoltws.freightpickup.v1.RequesterType;
import com.ups.xmlschema.xoltws.freightpickup.v1.PhoneType;
import com.ups.xmlschema.xoltws.freightpickup.v1.ShipFromType;


public class JAXWSFreightPickupClient {
	private static String accesskey;
	private static String username;
	private static String password;
	private static String out_file_location = "out_file_location";
	private static String tool_or_webservice_name = "tool_or_webservice_name";
	private static final String endpoint_url = "url";
	static Properties props = null;

	static{
        try{
        	props = new Properties();
        	props.load(new FileInputStream("./build.properties"));
	  		accesskey = props.getProperty("accesskey");
	  		username = props.getProperty("username");
	  		password = props.getProperty("password");
        }
        catch(Exception e){
        	e.printStackTrace();
        }
	}

	public static void main(String args[])throws Exception {
		String statusCode = null;
		String description = null;
    try {
    		
    	FreightPickupService fpService = new FreightPickupService();
		FreightPickupPortType fpPort = fpService.getFreightPickupPort();
		BindingProvider bp = (BindingProvider)fpPort;
    	bp.getRequestContext().put(BindingProvider.ENDPOINT_ADDRESS_PROPERTY, props.getProperty(endpoint_url));
		FreightPickupRequest fpRequest = new FreightPickupRequest();
		RequestType requestType = new RequestType();
		List<String> requestOption = requestType.getRequestOption();
		requestOption.add("1");
		fpRequest.setRequest(requestType);
		
		/** *****************Requestor***************************** */
		RequesterType requester = new RequesterType();
		requester.setName("ABC Associates");
		requester.setAttentionName("Mr. ABC");
		PhoneType requesterPhone = new PhoneType();
		requesterPhone.setNumber("123456789");
		requesterPhone.setExtension("345");
		requester.setPhone(requesterPhone);
		requester.setThirdPartyIndicator("1");
		requester.setEMailAddress("wbb6tdf@ups.com");
		fpRequest.setRequester(requester);
		/** *****************Requestor***************************** */
		
		/** ****************ShipFrom******************************* */
		ShipFromType shipFrom = new ShipFromType();
		AddressType shipFromAddress = new AddressType();		
		List<String> ShipToAddressLineList = shipFromAddress.getAddressLine();
		ShipToAddressLineList.add("AddressLine1");		
		shipFromAddress.setCity("Roswell");
		shipFromAddress.setStateProvinceCode("GA");
		shipFromAddress.setPostalCode("30076");
		shipFromAddress.setCountryCode("US");
		shipFrom.setAddress(shipFromAddress);
		shipFrom.setAttentionName("Mr. ABC");
		shipFrom.setName("ABC Associates");
		PhoneType shipFromPhone = new PhoneType();
		shipFromPhone.setNumber("123456789");
		shipFromPhone.setExtension("345");
		shipFrom.setPhone(shipFromPhone);
		fpRequest.setShipFrom(shipFrom);
		/** ****************ShipFrom******************************* */
		
		/** ***************Existing Shipment Id********************* */
		ExistingShipmentIDType existingShipIDType = new ExistingShipmentIDType();
		existingShipIDType.setBOLID("1740266");
		existingShipIDType.setShipmentNumber("015409052");
		fpRequest.setExistingShipmentID(existingShipIDType);
		/** ***************Existing Shipment Id********************* */

		/** ****************PickupDate and Delivery Instr************* */
		fpRequest.setPickupDate("20100629");
		fpRequest.setEarliestTimeReady("0800");
		fpRequest.setLatestTimeReady("1800");
		fpRequest.setDeliveryInstructions("Leave the shipment at delivery dock and obtain signature from the security");
		fpRequest.setPickupInstructions("Pickup the shipment at the delivery dock. Call before arriving at the pickup location");
		fpRequest.setDestinationCountryCode("US");
		/** ****************PickupDate and Delivery Instr************* */

		/** ************UPSSE***************************/
		UPSSecurity upss = new UPSSecurity();
		ServiceAccessToken token = new ServiceAccessToken();
		token.setAccessLicenseNumber(accesskey);
		upss.setServiceAccessToken(token);
		UsernameToken usernameToken = new UsernameToken();
		usernameToken.setPassword(password);
		usernameToken.setUsername(username);
		upss.setUsernameToken(usernameToken);
		

		/** ************UPSSE******************************/
	
		
		FreightPickupResponse freightPickupResponse = fpPort.processFreightPickup(fpRequest, upss);
		statusCode = freightPickupResponse.getResponse().getResponseStatus().getCode();
		description = freightPickupResponse.getResponse().getResponseStatus().getDescription();
		updateResultsToFile(statusCode, description);
System.out.println("The transaction was a "
		+ freightPickupResponse.getResponse().getResponseStatus()
				.getDescription());
System.out.println("The Pickup Request Confirmation Number is "
		+ freightPickupResponse
				.getPickupRequestConfirmationNumber());
		
		
	} catch (Exception e) {		
		if (e instanceof PickupErrorMessage){
			 PickupErrorMessage err = (PickupErrorMessage)e;
			 statusCode = (err.getFaultInfo()).getErrorDetail().get(0).getPrimaryErrorCode().getCode();			 
			 description = (err.getFaultInfo()).getErrorDetail().get(0).getPrimaryErrorCode().getDescription();
			 updateResultsToFile(statusCode, description);
		 }else{
			 description=e.getMessage();
			 statusCode=e.toString();
			 updateResultsToFile(statusCode, description);
			 e.printStackTrace();
		 }
	}
	}
	/**
     * This method updates the XOLTResult.xml file with the received status and description
     * @param statusCode
     * @param description
     */
	private static void updateResultsToFile(String statusCode, String description){
    	BufferedWriter bw = null;
    	try{    		
    		
    		File outFile = new File(props.getProperty(out_file_location));
    		System.out.println("Output file deletion status: " + outFile.delete());
    		outFile.createNewFile();
    		System.out.println("Output file location: " + outFile.getCanonicalPath());
    		bw = new BufferedWriter(new FileWriter(outFile));
    		StringBuffer strBuf = new StringBuffer();
    		strBuf.append("<ExecutionAt>");
    		strBuf.append(Calendar.getInstance().getTime());
    		strBuf.append("</ExecutionAt>\n");
    		strBuf.append("<ToolOrWebServiceName>");
    		strBuf.append(props.getProperty(tool_or_webservice_name));
    		strBuf.append("</ToolOrWebServiceName>\n");
    		strBuf.append("\n");
    		strBuf.append("<ResponseStatus>\n");
    		strBuf.append("\t<Code>");
    		strBuf.append(statusCode);
    		strBuf.append("</Code>\n");
    		strBuf.append("\t<Description>");
    		strBuf.append(description);
    		strBuf.append("</Description>\n");
    		strBuf.append("</ResponseStatus>");
    		bw.write(strBuf.toString());
    		bw.close();    		    		
    	}catch (Exception e) {
			e.printStackTrace();
		}finally{
			try{
				if (bw != null){
					bw.close();
					bw = null;
				}
			}catch (Exception e) {
				e.printStackTrace();
			}			
		}		
    }
}
