<?php

namespace App\Traits;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;

trait ElectricityServices 
{
    // public function validation(Request $request, $rules){
    //     return Validator::make($request->all(), $rules);
    // }

    public function meterBody($billersCode, $serviceID, $type) {
        return json_encode([
            'billersCode' => $billersCode,
            'serviceID' => $serviceID,
            'type' => $type
        ]);
    }

    public function verifyMeterVTPass($body){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://sandbox.vtpass.com/api/merchant-verify",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        //CURLOPT_POSTFIELDS => "{\n\t\"billersCode\":\"1111111111111\",\n\t\"serviceID\":\"eko-electric\",\n\t\"type\":\"prepaid \"\n}",
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Authorization: Basic aW1hbnVlbGRleW9uQGdtYWlsLmNvbTpqYW51YXJ5MTA=",
            "Content-Type: application/json",
            "Postman-Token: 1dfc37d2-dc1c-4a7d-8f63-1709e17a6b11",
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //echo "cURL Error #:" . $err;
            false;
        } else {
            // echo $response;
            return json_decode($response, true);
        }
    
    }


    public function paymentBody($requestID, $serviceID, $billersCode, $meterType, $amount, $phoneNumber) {

        return json_encode([
            'request_id' => $requestID,
            'serviceID' => $serviceID,
            'billersCode' => $billersCode,
            'variation_code' => $meterType,
            'amount' => $amount,
            'phone' => $phoneNumber
        ]);
        
    }

    public function payElectricBill($body){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://sandbox.vtpass.com/api/pay",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        //CURLOPT_POSTFIELDS => "{\n\t\"request_id\": \"21\",\n\t\"serviceID\":\"eko-electric\",\n\t\"billersCode\": \"1111111111111\",\n\t\"variation_code\":\"prepaid\",\n\t\"amount\":\"1000\",\n\t\"phone\":\"08125262428\"\n}",
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Authorization: Basic aW1hbnVlbGRleW9uQGdtYWlsLmNvbTpqYW51YXJ5MTA=",
            "Content-Type: application/json",
            "Postman-Token: d2a4d384-e705-4687-b27a-cc6ec39be3d0",
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // echo "cURL Error #:" . $err;
            false;
        } else {
            // echo $response;
            return json_decode($response, true);
        }
    }


    public function transactionBody($billersCode) {
        return json_encode([
            'request_id' => $billersCode
        ]);
    }

    public function transactionStatus($status){
 
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://sandbox.vtpass.com/api/requery",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        // CURLOPT_POSTFIELDS => "{\n\t\"request_id\":\"21\"\n}",
        CURLOPT_POSTFIELDS => $status,
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Authorization: Basic aW1hbnVlbGRleW9uQGdtYWlsLmNvbTpqYW51YXJ5MTA=",
            "Content-Type: application/json",
            "Postman-Token: e8e39266-2495-4370-9907-b7df1e877e5e",
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            // echo "cURL Error #:" . $err;
            false;
        } else {
            // echo $response;
            return json_decode($response, true);
        }
    }

}



// return json_encode([
//     'request_id' => $object['billersCode'],
//     'serviceID' => $object['serviceID'],
//     'billersCode' => $object['meterNumber'],
//     'variation_code' => $object['meterType'],
//     'amount' => $object['type'],
//     'phone' => $object['phoneNumber']
// ]);

?>


