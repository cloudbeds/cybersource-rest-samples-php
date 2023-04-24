<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../vendor/autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../Resources/ExternalConfiguration.php';

function EnrollWithPendingAuthentication()
{
    $clientReferenceInformationArr = [
            "code" => "cybs_test"
    ];
    $clientReferenceInformation = new CyberSource\Model\Riskv1decisionsClientReferenceInformation($clientReferenceInformationArr);

    $orderInformationAmountDetailsArr = [
            "currency" => "USD",
            "totalAmount" => "10.99"
    ];
    $orderInformationAmountDetails = new CyberSource\Model\Riskv1authenticationsOrderInformationAmountDetails($orderInformationAmountDetailsArr);

    $orderInformationBillToArr = [
            "address1" => "1 Market St",
            "address2" => "Address 2",
            "administrativeArea" => "CA",
            "country" => "US",
            "locality" => "san francisco",
            "firstName" => "John",
            "lastName" => "Doe",
            "phoneNumber" => "4158880000",
            "email" => "test@cybs.com",
            "postalCode" => "94105"
    ];
    $orderInformationBillTo = new CyberSource\Model\Riskv1authenticationsOrderInformationBillTo($orderInformationBillToArr);

    $orderInformationArr = [
            "amountDetails" => $orderInformationAmountDetails,
            "billTo" => $orderInformationBillTo
    ];
    $orderInformation = new CyberSource\Model\Riskv1authenticationsOrderInformation($orderInformationArr);

    $paymentInformationCardArr = [
            "type" => "001",
            "expirationMonth" => "12",
            "expirationYear" => "2025",
            "number" => "4000000000000101"
    ];
    $paymentInformationCard = new CyberSource\Model\Riskv1authenticationsPaymentInformationCard($paymentInformationCardArr);

    $paymentInformationArr = [
            "card" => $paymentInformationCard
    ];
    $paymentInformation = new CyberSource\Model\Riskv1authenticationsPaymentInformation($paymentInformationArr);

    $buyerInformationArr = [
            "mobilePhone" => 1245789632
    ];
    $buyerInformation = new CyberSource\Model\Riskv1authenticationsBuyerInformation($buyerInformationArr);

    $consumerAuthenticationInformationArr = [
            "transactionMode" => "MOTO"
    ];
    $consumerAuthenticationInformation = new CyberSource\Model\Riskv1decisionsConsumerAuthenticationInformation($consumerAuthenticationInformationArr);

    $requestObjArr = [
            "clientReferenceInformation" => $clientReferenceInformation,
            "orderInformation" => $orderInformation,
            "paymentInformation" => $paymentInformation,
            "buyerInformation" => $buyerInformation,
            "consumerAuthenticationInformation" => $consumerAuthenticationInformation
    ];
    $requestObj = new CyberSource\Model\CheckPayerAuthEnrollmentRequest($requestObjArr);


    $commonElement = new CyberSource\ExternalConfiguration();
    $config = $commonElement->ConnectionHost();
    $merchantConfig = $commonElement->merchantConfigObject();

    $api_client = new CyberSource\ApiClient($config, $merchantConfig);
    $api_instance = new CyberSource\Api\PayerAuthenticationApi($api_client);

    try {
        $apiResponse = $api_instance->checkPayerAuthEnrollment($requestObj);
        print_r(PHP_EOL);
        print_r($apiResponse);

        WriteLogAudit($apiResponse[1]);
        return $apiResponse;
    } catch (Cybersource\ApiException $e) {
        print_r($e->getResponseBody());
        print_r($e->getMessage());
        $errorCode = $e->getCode();
        WriteLogAudit($errorCode);
    }
}

if (!function_exists('WriteLogAudit')){
    function WriteLogAudit($status){
        $sampleCode = basename(__FILE__, '.php');
        print_r("\n[Sample Code Testing] [$sampleCode] $status");
    }
}

if(!defined('DO_NOT_RUN_SAMPLES')){
    echo "\nEnrollWithPendingAuthentication Sample Code is Running..." . PHP_EOL;
    EnrollWithPendingAuthentication();
}
?>
