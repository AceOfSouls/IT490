/* 
 ** 
 ** Filename: Axis2FreigthPickupClient.java 
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
import java.util.Calendar;
import java.util.Properties;

import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.PickupErrorMessage;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.AddressType;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.ExistingShipmentIDType;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.FreightPickupRequest;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.FreightPickupResponse;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.PhoneType;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.RequestType;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.RequesterType;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.ServiceAccessToken_type0;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.ShipFromType;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.UPSSecurity;
import com.ups.www.wsdl.xoltws.freightpickup.v1_1.FreightPickupServiceStub.UsernameToken_type0;

public class Axis2FreigthPickupClient {
	
	private static String url;
	private static String accesskey;
	private static String username;
	private static String password;
	private static String out_file_location = "out_file_location";
	private static String tool_or_webservice_name = "tool_or_webservice_name";
	static Properties props = null;
	
	static{
        try{
        	props = new Properties();
        	props.load(new FileInputStream("./build.properties"));
	  		url = props.getProperty("url");
	  		accesskey = props.getProperty("accesskey");
	  		username = props.getProperty("username");
	  		password = props.getProperty("password");
        }
        catch(Exception e){
        	e.printStackTrace();
        }
	}
	
	public static void main(String[] args) {
		String statusCode = null;
		String description = null;
		try {
			FreightPickupServiceStub freightPickupServiceStub =
                new FreightPickupServiceStub(url);
			FreightPickupRequest freightPickupRequest = new FreightPickupRequest();
			RequestType request = new RequestType();
			String[] requestOption = { "1" };
			request.setRequestOption(requestOption);
			freightPickupRequest.setRequest(request);

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
			freightPickupRequest.setRequester(requester);
			/** *****************Requestor***************************** */

			/** ****************ShipFrom******************************* */
			ShipFromType shipFrom = new ShipFromType();
			AddressType shipFromAddress = new AddressType();
			String[] shipFromAddressLines = { "AddressLine1" };
			shipFromAddress.setAddressLine(shipFromAddressLines);
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
			freightPickupRequest.setShipFrom(shipFrom);
			/** ****************ShipFrom******************************* */

			/** ***************Existing Shipment ID********************* */
			ExistingShipmentIDType existingShipIDType = new ExistingShipmentIDType();
			existingShipIDType.setBOLID("1740266");
			existingShipIDType.setShipmentNumber("1Z2220170294706558");
			freightPickupRequest.setExistingShipmentID(existingShipIDType);
						
			/** ***************Existing Shipment ID********************* */

			/** ****************PickupDate and Delivery Instr************* */
			freightPickupRequest.setPickupDate("20100629");
			freightPickupRequest.setEarliestTimeReady("0800");
			freightPickupRequest.setLatestTimeReady("1800");
			freightPickupRequest
					.setDeliveryInstructions("Leave the shipment at delivery dock and obtain signature from the security");
			freightPickupRequest
					.setPickupInstructions("Pickup the shipment at the delivery dock. Call before arriving at the pickup location");
			freightPickupRequest.setDestinationCountryCode("US");
			/** ****************PickupDate and Delivery Instr************* */

			/**************UPSSE***************************/
			UPSSecurity upss = new UPSSecurity();
			ServiceAccessToken_type0 upsSvcToken = new ServiceAccessToken_type0();
			upsSvcToken.setAccessLicenseNumber(accesskey);
			upss.setServiceAccessToken(upsSvcToken);
			UsernameToken_type0 upsSecUsrnameToken = new UsernameToken_type0();
			upsSecUsrnameToken.setUsername(username);
			upsSecUsrnameToken.setPassword(password);
			upss.setUsernameToken(upsSecUsrnameToken);
			/**************UPSSE******************************/

			FreightPickupResponse freightPickupResponse = freightPickupServiceStub
					.ProcessFreightPickup(freightPickupRequest, upss);
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
				 statusCode = err.getFaultMessage().getErrorDetail()[0].getPrimaryErrorCode().getCode();
				 description = err.getFaultMessage().getErrorDetail()[0].getPrimaryErrorCode().getDescription();
				 updateResultsToFile(statusCode, description);
			 }else{
				 description=e.getMessage();
				 statusCode=e.toString();
				 updateResultsToFile(statusCode, description);
			 }
			e.printStackTrace();
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
