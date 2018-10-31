using System;
using System.Collections.Generic;
using System.Text;
using FreightPickupWSSample.FreightPickupWebReference;
using System.ServiceModel;

namespace FreightPickupWSSample
{
    class FreightPickupClient
    {
        static void Main()
        {
            try
            {
                FreightPickupService freightPickupService = new FreightPickupService();
                FreightPickupRequest freightPickupRequest = new FreightPickupRequest();
                RequestType request = new RequestType();
                String[] requestOption = { "1" };
                request.RequestOption = requestOption;
                freightPickupRequest.Request = request;

                /** *****************Requestor***************************** */
                RequesterType requester = new RequesterType();
                requester.Name = "ABC Associates";
                requester.AttentionName = "Mr. ABC";
                PhoneType requesterPhone = new PhoneType();
                requesterPhone.Number = "123456789";
                requesterPhone.Extension = "345";
                requester.Phone = requesterPhone;
                requester.ThirdPartyIndicator = "1";
                requester.EMailAddress = "fyq9wpg@ups.com";
                freightPickupRequest.Requester = requester;
                /** *****************Requestor***************************** */

                /** ****************ShipFrom******************************** */
                ShipFromType shipFrom = new ShipFromType();
                AddressType shipFromAddress = new AddressType();
                String[] shipFromAddressLines = { "AddressLine1" };
                shipFromAddress.AddressLine = shipFromAddressLines;
                shipFromAddress.City = "Roswell";
                shipFromAddress.StateProvinceCode = "GA";
                shipFromAddress.PostalCode = "30076";
                shipFromAddress.CountryCode = "US";
                shipFrom.Address = shipFromAddress;
                shipFrom.AttentionName = "Mr. ABC";
                shipFrom.Name = "ABC Associates";

                PhoneType shipFromPhone = new PhoneType();
                shipFromPhone.Number = "123456789";
                shipFromPhone.Extension = "345";
                shipFrom.Phone = shipFromPhone;

                freightPickupRequest.ShipFrom = shipFrom;
                /** ****************ShipFrom******************************* */

                /** ***************Existing Shipment Id********************* */
                ExistingShipmentIDType existingShipIDType = new ExistingShipmentIDType();
                existingShipIDType.BOLID = "1740266";
                existingShipIDType.ShipmentNumber = "1Z2220170294706558";
                freightPickupRequest.ExistingShipmentID = existingShipIDType;
                /** ***************Existing Shipment Id********************* */

                /** ****************PickupDate and Delivery Instr************* */
                freightPickupRequest.PickupDate = "20100630";
                freightPickupRequest.EarliestTimeReady = "0800";
                freightPickupRequest.LatestTimeReady = "1800";
                freightPickupRequest.DeliveryInstructions = "Leave the shipment at delivery dock and obtain signature from the security";
                freightPickupRequest.PickupInstructions = "Pickup the shipment at the delivery dock. Call before arriving at the pickup location";
                freightPickupRequest.DestinationCountryCode = "US";
                /** ****************PickupDate and Delivery Instr************* */

                UPSSecurity upss = new UPSSecurity();
                UPSSecurityServiceAccessToken upssSvcAccessToken = new UPSSecurityServiceAccessToken();
                upssSvcAccessToken.AccessLicenseNumber = "0C089332A1CFE094";
                upss.ServiceAccessToken = upssSvcAccessToken;
                UPSSecurityUsernameToken upssUsrNameToken = new UPSSecurityUsernameToken();
                upssUsrNameToken.Username = "ggmike";
                upssUsrNameToken.Password = "password";
                upss.UsernameToken = upssUsrNameToken;
                freightPickupService.UPSSecurityValue = upss;

                System.Net.ServicePointManager.CertificatePolicy = new TrustAllCertificatePolicy();
                Console.WriteLine(freightPickupRequest);
                FreightPickupResponse freightPickupResponse = freightPickupService.ProcessFreightPickup(freightPickupRequest);
                Console.WriteLine("The transaction was a " + freightPickupResponse.Response.ResponseStatus.Description);
                Console.WriteLine("The Pickup Request Confirmation Number is  : " + freightPickupResponse.PickupRequestConfirmationNumber);
                Console.ReadKey();
            }
            catch (System.Web.Services.Protocols.SoapException ex)
            {
                Console.WriteLine("");
                Console.WriteLine("---------Pickup Web Service returns error----------------");
                Console.WriteLine("---------\"Hard\" is user error \"Transient\" is system error----------------");
                Console.WriteLine("SoapException Message= " + ex.Message);
                Console.WriteLine("");
                Console.WriteLine("SoapException Category:Code:Message= " + ex.Detail.LastChild.InnerText);
                Console.WriteLine("");
                Console.WriteLine("SoapException XML String for all= " + ex.Detail.LastChild.OuterXml);
                Console.WriteLine("");
                Console.WriteLine("SoapException StackTrace= " + ex.StackTrace);
                Console.WriteLine("-------------------------");
                Console.WriteLine("");
            }
            catch (System.ServiceModel.CommunicationException ex)
            {
                Console.WriteLine("");
                Console.WriteLine("--------------------");
                Console.WriteLine("CommunicationException= " + ex.Message);
                Console.WriteLine("CommunicationException-StackTrace= " + ex.StackTrace);
                Console.WriteLine("-------------------------");
                Console.WriteLine("");

            }
            catch (Exception ex)
            {
                Console.WriteLine("");
                Console.WriteLine("-------------------------");
                Console.WriteLine(" Generaal Exception= " + ex.Message);
                Console.WriteLine(" Generaal Exception-StackTrace= " + ex.StackTrace);
                Console.WriteLine("-------------------------");

            }
            finally
            {
                Console.ReadKey();
            }

        }
    }
}
