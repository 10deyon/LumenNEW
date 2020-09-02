<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\ElectricityServices;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\JWTAuth;
use App\Electricity;
use Illuminate\Support\Facades\DB;


class ElectricityController extends Controller
{
    use ElectricityServices;

    protected $user;

    public function __construct(JWTAuth $jwt, AuthController $token)
    {
        $this->middleware('auth');
        $this->jwt = $jwt;
        $this->token = $token;
    }

    public function buyElectricity(Request $request)
    {
        $validatedData = [
            'billersCode' => 'required|min:13',
            'serviceID' => 'required|string',
            'meterType' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $validatedData);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $body = $this->meterBody($request->billersCode, $request->serviceID, $request->meterType);
        $responseAPI = $this->verifyMeterVTPass($body);

        if ($responseAPI) {
            if ($responseAPI["code"] == "000") {
                $data = new Electricity();
                $data->customer_name = $responseAPI["content"]["Customer_Name"];
                $data->meter_number =  $responseAPI["content"]["Meter_Number"];
                $data->customer_address =  $responseAPI["content"]["Address"];
                $data->meterType =  $request->input('meterType');
                //$data->transaction_id =  date_create();
                $data->transaction_id =  time();
                $data->user_id = $request->user()->id;
                //$data->transaction_id =  intval( "0" . rand(1,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) );
                // $request->user()->electricity()->save($data);
                $data->save();

                return response()->json([
                    "status" => "00",
                    "message" => "Details Correct, Enter payment details",
                    "customer_name" => $responseAPI["content"]["Customer_Name"],
                    "meter_number" =>  $responseAPI["content"]["Meter_Number"],
                    "customer_address" =>  $responseAPI["content"]["Address"],
                    "transactionID" => $data->transaction_id
                ], 200);
            } else {
                return response()->json(["message" => "Error occured"], 404);
            }
        }

        // return response()->json(["message"=>"Login first"], 404);
    }

    public function purchaseProduct(Request $request)
    {
        $body = [
            'transaction_id' => 'required',
            'serviceID' => 'required|string',
            'meterNumber' => 'required|string|min:13',
            'meterType' => 'required|string',
            'amount' => 'required|string',
            'phone' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $body);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // $transaction = DB::table('electricity_transactions')->get('transaction_id');
        $transaction = Electricity::where([
            'transaction_id' => $request->transaction_id
        ])->first();
            
        // if ($transaction == "prepaid") {
        if ($transaction == null) {
            return response()->json(["message" => "Invalid Transaction ID"]);
        } else if($transaction->meterType == $request->input('meterType')) {
            $body = $this->paymentBody($request->transaction_id, $request->serviceID, $request->meterNumber, $request->meterType, $request->amount, $request->phone);

            $responseAPI = $this->payElectricBill($body);

            // return response()->json($responseAPI);

            if ($responseAPI) {
                if ($responseAPI["code"] == "000") {
                    $responseToUser = [
                        "status" => $responseAPI["content"]["transactions"]["status"],
                        "transactionType" => $responseAPI["content"]["transactions"]["type"],
                        "amount" => $responseAPI["content"]["transactions"]["amount"],
                        "productName" => $responseAPI["content"]["transactions"]["product_name"],
                        "transactionDate" => $responseAPI["transaction_date"]["date"],
                        "transaction" => $responseAPI["requestId"],
                        "token" =>  $responseAPI["mainToken"]
                    ];

                    $transaction->amount = $responseAPI["content"]["transactions"]["amount"];
                    $transaction->status =  $responseAPI["content"]["transactions"]["status"];
                    $transaction->phone =  $responseAPI["content"]["transactions"]["phone"];
                    $transaction->token =  $responseAPI["mainToken"];
                    $transaction->transaction_date =  $responseAPI["transaction_date"]["date"];
                    $transaction->update();

                    return response()->json(["message" => "Payment successful", 'data' => $responseToUser], 200);
                }
                return response()->json(["message" => "Request already processed"], 404);
            }
            return response()->json(["message" => "Complete all fields"], 404);
        } else{

            echo "work on postpaid";
        }
        
    }

    public function transactionQuery(Request $request)
    {
        $rules = [
            'request_id' => 'required',
        ];

        // $validator = $this->validation($request, $rules);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $body = $this->transactionBody($request->request_id);
        //$res = false;
        $res = $this->transactionStatus($body);

        if ($res) {
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
                return response()->json(["message" => "Transaction occurred successfully", 'data' => $responseToUser], 200);
            } else {
                return response()->json(["message" => "Transaction was not successful"], 404);
            }
        }
        return response()->json(["message" => "Transaction failed"], 404);
    }


    // $object = [
    //     'billersCode' => $request->request_id,
    //     'serviceID' => $request->serviceID,
    //     'meterNumber' => $request->meterNumber,
    //     'meterType' => $request->meterType,
    //     'amount' => $request->amount,
    //     'phoneNumber' => $request->phone
    // ];

}