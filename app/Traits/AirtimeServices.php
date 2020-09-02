<?php

namespace App\Traits;

trait AirtimeServices {

  public function airtimeBody($requestId, $serviceId, $amount, $number) {
    return json_encode([
      "request_id" => $requestId,
      "serviceID" => $serviceId,
        "amount" => $amount,
        "phone" => $number
    ]);
  }

  public function callVTPass($body){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://sandbox.vtpass.com/api/pay",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $body,
      // CURLOPT_POSTFIELDS => "{\r\n\t\"request_id\":\"{$requestId}\",\r\n\t\"serviceID\":\"{$serviceId}\",\r\n    \"amount\":\"{$amount}\",\r\n    \"phone\":\"{$number}\"\r\n}",
      CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Authorization: Basic aW1hbnVlbGRleW9uQGdtYWlsLmNvbTpqYW51YXJ5MTA=",
        "Content-Type: application/json",
        "Postman-Token: c9c4e540-5d5d-4501-a35c-5fdbe6577cb5",
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      false;
    } else {
      return json_decode($response, true);
    }
  }

  public function airtimeQuery($requestID){

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://sandbox.vtpass.com/api/requery",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      //CURLOPT_POSTFIELDS =>"{\n\t\"request_id\":\"1\"\n}",
      CURLOPT_POSTFIELDS => $requestID,
      CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Content-Type: application/json",
        "Authorization: Basic aW1hbnVlbGRleW9uQGdtYWlsLmNvbTpqYW51YXJ5MTA=",
        "Cookie: laravel_session=eyJpdiI6ImVmOHlqMzVwbXlodGsyTEEyenZDSVE9PSIsInZhbHVlIjoiV1k1SXN5OHUzbkdHM2xpemo5VlRHTmgrVmlwNlwvbE9SQ05pS0FuZjZCblM4ZW9MTTB6T3NRdEdoeTdLOCsyd0xxMGNNWWlOWnV6bE5VRVFTdU9YMHFRPT0iLCJtYWMiOiJjY2ZlODU5MTA5YWEwMmZiMDMyZmVlM2ZlYmFjMDA1MzE4NWQyNDVhMzM0YzAxOWY4NTYzMGM2OTA5ZTYyYTZjIn0%3D; vtpass_s=eyJpdiI6Ik9mV3JsVDRNS1JJYXBZdWRoanBYTlE9PSIsInZhbHVlIjoiUFVweUhUZjlTdVdCNVcrak5kTkNDQT09IiwibWFjIjoiNjI0MWE4MjM0MDE1M2ZlNWZkYjE1ZDY5OGQ4ODIwZDcwN2UyNjE1ZjMxZjg3MzkxOTJiNzk1ODY3NzQ1OTkwZiJ9"
      ),
    ));

    // $response = curl_exec($curl);
    // $err = curl_error($curl);

    // curl_close($curl);

    // if ($err) {
    //   false;
    // } else {
    //   return json_decode($response, true);
    // }

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
    return json_decode($response, true);

  }
}