<?php

use CyberSource\Model\PtsV2PaymentsPost201Response;
use CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrument;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . DIRECTORY_SEPARATOR . '../../vendor/autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../Resources/ExternalConfiguration.php';

function run(): void
{
    $response = CreateInstrumentIdentifierCard();
    $instrumentIdentifierId = $response[0]->getId();
    $merchantCustomerId = Uuid::uuid4()->toString();
    var_dump("merchantCustomerId - $merchantCustomerId");
    $buyerInformationArr = [
        "merchantCustomerID" => $merchantCustomerId,
        "email" => "test@cybs.com"
    ];
    $buyerInformation = new CyberSource\Model\Tmsv2customersBuyerInformation($buyerInformationArr);

    $clientReferenceInformationArr = [
        "code" => "TC50171_3"
    ];
    $clientReferenceInformation = new CyberSource\Model\Tmsv2customersClientReferenceInformation($clientReferenceInformationArr);

    $merchantDefinedInformation = array();
    $merchantDefinedInformation_0 = [
        "name" => "data1",
        "value" => "Your customer data"
    ];
    $merchantDefinedInformation[0] = new CyberSource\Model\Tmsv2customersMerchantDefinedInformation($merchantDefinedInformation_0);

    $requestObjArr = [
        "buyerInformation" => $buyerInformation,
        "clientReferenceInformation" => $clientReferenceInformation,
        "merchantDefinedInformation" => $merchantDefinedInformation,
    ];
    $requestObj = new CyberSource\Model\PostCustomerRequest($requestObjArr);
    $commonElement = new CyberSource\ExternalConfiguration();
    $config = $commonElement->ConnectionHost();
    $merchantConfig = $commonElement->merchantConfigObject();

    $api_client = new CyberSource\ApiClient($config, $merchantConfig);
    $api_instance = new CyberSource\Api\CustomerApi($api_client);

    try {
        /** @var array{\CyberSource\Model\TmsV2CustomersResponse, string, array<string,string>} $apiResponse */
        $apiResponse = $api_instance->postCustomer($requestObj);
        $customerId = $apiResponse[0]->getId();
        var_dump("Customer id: {$customerId}");
        CreateCustomerDefaultPaymentInstrumentCard($customerId, $instrumentIdentifierId);
        PurchaseWithCustomerTokenId($customerId);
    } catch (Cybersource\ApiException $e) {
        print_r($e->getResponseBody());
        print_r($e->getMessage());
        $errorCode = $e->getCode();
        WriteLogAudit($errorCode);
    }
}

function PurchaseWithCustomerTokenId(string $customerId)
{
    $merchantReferenceCode = Uuid::uuid4()->toString();
    $clientReferenceInformationArr = [
        "code" => $merchantReferenceCode
    ];

    $processingInformationArr = [
        "capture" => true,
    ];
    $processingInformation = new CyberSource\Model\Ptsv2paymentsProcessingInformation($processingInformationArr);


    $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsClientReferenceInformation($clientReferenceInformationArr);

    $paymentInformationCustomerArr = [
        "id" => $customerId,
    ];
    $paymentInformationCustomer = new CyberSource\Model\Ptsv2paymentsPaymentInformationCustomer($paymentInformationCustomerArr);

    $paymentInformationArr = [
        "customer" => $paymentInformationCustomer
    ];
    $paymentInformation = new CyberSource\Model\Ptsv2paymentsPaymentInformation($paymentInformationArr);

    $orderInformationAmountDetailsArr = [
        "totalAmount" => "102.21",
        "currency" => "USD"
    ];
    $orderInformationAmountDetails = new CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails($orderInformationAmountDetailsArr);

    $orderInformationArr = [
        "amountDetails" => $orderInformationAmountDetails
    ];
    $orderInformation = new CyberSource\Model\Ptsv2paymentsOrderInformation($orderInformationArr);

    $requestObjArr = [
        "clientReferenceInformation" => $clientReferenceInformation,
        "processingInformation" => $processingInformation,
        "paymentInformation" => $paymentInformation,
        "orderInformation" => $orderInformation
    ];
    $requestObj = new CyberSource\Model\CreatePaymentRequest($requestObjArr);


    $commonElement = new CyberSource\ExternalConfiguration();
    $config = $commonElement->ConnectionHost();
    $merchantConfig = $commonElement->merchantConfigObject();

    $api_client = new CyberSource\ApiClient($config, $merchantConfig);
    $api_instance = new CyberSource\Api\PaymentsApi($api_client);

    try {
        /** @var array{PtsV2PaymentsPost201Response, string, array<string,string>} $apiResponse */
        $apiResponse = $api_instance->createPayment($requestObj);
        var_dump("Payment ID: {$apiResponse[0]->getId()}");
        return $apiResponse;
    } catch (Cybersource\ApiException $e) {
        print_r($e->getResponseBody());
        print_r($e->getMessage());
        $errorCode = $e->getCode();
        WriteLogAudit($errorCode);
    }
}

/**
 * @param string $customerTokenId
 * @param string $instrumentIdentifierId
 * @return array{Tmsv2customersEmbeddedDefaultPaymentInstrument, string, array<string,string>}
 */
function CreateCustomerDefaultPaymentInstrumentCard(string $customerTokenId, string $instrumentIdentifierId)
{
    $cardArr = [
        "expirationMonth" => "12",
        "expirationYear" => "2031",
        "type" => "001"
    ];
    $card = new CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentCard($cardArr);

    $billToArr = [
        "firstName" => "John",
        "lastName" => "Doe",
        "company" => "CyberSource",
        "address1" => "1 Market St",
        "locality" => "San Francisco",
        "administrativeArea" => "CA",
        "postalCode" => "94105",
        "country" => "US",
        "email" => "test@cybs.com",
        "phoneNumber" => "4158880000"
    ];
    $billTo = new CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentBillTo($billToArr);

    $instrumentIdentifierArr = [
        "id" => $instrumentIdentifierId
    ];
    $instrumentIdentifier = new CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentInstrumentIdentifier($instrumentIdentifierArr);

    $requestObjArr = [
        "_default" => true,
        "card" => $card,
        "billTo" => $billTo,
        "instrumentIdentifier" => $instrumentIdentifier
    ];
    $requestObj = new CyberSource\Model\PostCustomerPaymentInstrumentRequest($requestObjArr);

    $commonElement = new CyberSource\ExternalConfiguration();
    $config = $commonElement->ConnectionHost();
    $merchantConfig = $commonElement->merchantConfigObject();

    $api_client = new CyberSource\ApiClient($config, $merchantConfig);
    $api_instance = new CyberSource\Api\CustomerPaymentInstrumentApi($api_client);

    try {
        /** @var array{Tmsv2customersEmbeddedDefaultPaymentInstrument, string, array<string,string>} $apiResponse */
        $apiResponse = $api_instance->postCustomerPaymentInstrument($customerTokenId, $requestObj, null);
        var_dump("Payment Instrument created: {$apiResponse[0]->getId()}");
        return $apiResponse;
    } catch (Cybersource\ApiException $e) {
        print_r($e->getResponseBody());
        print_r($e->getMessage());
        $errorCode = $e->getCode();
        WriteLogAudit($errorCode);
    }
}


/**
 * @return array{\CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentEmbeddedInstrumentIdentifier, string,array<string,string> }
 */
function CreateInstrumentIdentifierCard()
{
    $cardArr = [
        "number" => "4111111111111111"
    ];
    $card = new CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentEmbeddedInstrumentIdentifierCard($cardArr);

    $requestObjArr = [
        "card" => $card
    ];
    $requestObj = new CyberSource\Model\PostInstrumentIdentifierRequest($requestObjArr);

    $commonElement = new CyberSource\ExternalConfiguration();
    $config = $commonElement->ConnectionHost();
    $merchantConfig = $commonElement->merchantConfigObject();

    $api_client = new CyberSource\ApiClient($config, $merchantConfig);
    $api_instance = new CyberSource\Api\InstrumentIdentifierApi($api_client);

    try {
        /** @var array{\CyberSource\Model\Tmsv2customersEmbeddedDefaultPaymentInstrumentEmbeddedInstrumentIdentifier, string,array<string,string> } $apiResponse */
        $apiResponse = $api_instance->postInstrumentIdentifier($requestObj);
        var_dump("Instrument Identifier created. ID: {$apiResponse[0]->getId()}");
        return $apiResponse;
    } catch (Cybersource\ApiException $e) {
        print_r($e->getResponseBody());
        print_r($e->getMessage());
        $errorCode = $e->getCode();
        WriteLogAudit($errorCode);
    }
}


if (!function_exists('WriteLogAudit')) {
    function WriteLogAudit($status)
    {
        $sampleCode = basename(__FILE__, '.php');
        print_r("\n[Sample Code Testing] [$sampleCode] $status");
    }
}

if (!defined('DO_NOT_RUN_SAMPLES')) {
    echo "\nCreatePaymentInstrumentCard Sample Code is Running..." . PHP_EOL;
    run();
}
?>
