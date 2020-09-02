<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Services;
use Illuminate\Support\Facades\Validator;

class AirtimeController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */

    use Services;

    public function buyAirtime(Request $request)
    {
        $rules = [
            'request_id' => 'required|string',
            'serviceID' => 'required|string',
            'amount' => 'required|integer',
            'phone' => 'required|string',
        ];


        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $body = $this->airtimeBody($request->request_id, $request->serviceID, $request->amount, $request->phone);

        $res = $this->callVTPass($body);
        if ($res){
            if ($res["code"] == "000") {
                $responseToUser = [
                    "name" => $res["content"]["transactions"]["product_name"],
                    "phoneNumber" => $res["content"]["transactions"]["unique_element"],
                    "airtimeAmount" => $res["content"]["transactions"]["amount"]
                ];
                // return response()->json(["message"=>"Transaction successful"], 200);
                return response()->json(["message"=>"Transaction successful", 'data' => $responseToUser], 200);
            } else {
                return response()->json(["message"=>"Error occured during Transaction"], 404);
            }
        }
        return response()->json(["message"=>"Transaction failed"], 404);
        // $airtime = $url::create($request->all());
        // return response()->json($book, 201);
    }

    public function airtimeQuery(Request $request)
    {
        $rules = [
            'request_id' => 'required',
        ];
        
        // $validator = $this->validation($request, $rules);

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        //$body = $this->transactionBody($request->request_id);
        //$res = false;
        $res = $this->airtimeQuery($request->requestedID);
        
        if ($res){
            if ($res["code"] == "000") {
                $responseToUser = [
                    "transactionStatus" => $res["content"]["transactions"]["status"],
                    "productName" => $res["content"]["transactions"]["product_name"],
                    "amount" => $res["content"]["transactions"]["amount"],
                    "unit" => $res["content"]["transactions"]["unit_price"],
                    "transactionType" => $res["content"]["transactions"]["type"],
                    "phoneNumber" => $res["content"]["transactions"]["phone"],
                ];
                // return response()->json(["message"=>"Transaction successful"], 200);
                return response()->json(["message"=>"Transaction occurred successfully", 'data' => $responseToUser], 200);
            } else {
                return response()->json(["message"=>"Transaction was not successful"], 404);
            }
        }
        return response()->json(["message"=>"Transaction failed"], 404);
    }

}