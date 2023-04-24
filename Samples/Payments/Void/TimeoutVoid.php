<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../../vendor/autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../../Resources/ExternalConfiguration.php';

function TimeoutVoid()
{
    include __DIR__ . DIRECTORY_SEPARATOR . '../Payments/AuthorizationCaptureForTimeoutVoidFlow.php';
    
    $clientReferenceInformationArr = [
            "code" => "TC50171_3",
            "transactionId" => $timeoutVoidTransactionId
    ];
    $clientReferenceInformation = new CyberSource\Model\Ptsv2paymentsClientReferenceInformation($clientReferenceInformationArr);

    $requestObjArr = [
            "clientReferenceInformation" => $clientReferenceInformation
    ];
    $requestObj = new CyberSource\Model\MitVoidRequest($requestObjArr);


    $commonElement = new CyberSource\ExternalConfiguration();
    $config = $commonElement->ConnectionHost();
    $merchantConfig = $commonElement->merchantConfigObject();

    $api_client = new CyberSource\ApiClient($config, $merchantConfig);
    $api_instance = new CyberSource\Api\VoidApi($api_client);

    try {
        $apiResponse = $api_instance->mitVoid($requestObj);
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
    echo "\nTimeoutVoid Sample Code is Running..." . PHP_EOL;
    TimeoutVoid();
}
?>
