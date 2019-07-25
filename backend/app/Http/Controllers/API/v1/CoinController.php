<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

use App\Models\CoinModel;
use App\Models\User;
use App\Models\PackageModel;

class CoinController extends Controller
{
    private $statusCodes, $responseStatusCode, $successText, $failureText;
    public function __construct() {
        $this->statusCodes = config('api.status_codes');
        $this->tokenName = config('api.TOKEN_NAME');
        $this->successText = config('api.SUCCESS_TEXT');
        $this->failureText = config('api.FAILURE_TEXT');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $userData = Auth::user();
        
        $data = CoinModel::where(['user_id' => $userData->id, 'transaction_mode' => 'CR'])->orderBy('ct_id', 'DESC')->get();
    
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Transaction List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Transaction Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
            //'user_id'       =>'required',
            'package_id'    => 'required',
            'description'   => 'required',
            //'amount'        => 'required',
            //'coins'         => 'required',
            //'balance_coins' => 'required',
        ]);

        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $packageId = $request->package_id;
        $packageDetails = PackageModel::find($packageId);

        if(empty($packageDetails)) {
            // NOT FOUND
            $response = api_create_response(2, $this->failureText, 'Package Not Found.');
            return response()->json($response, $this->statusCodes->not_found);

        }

        $userData = Auth::user();
        $balanceCoin = $userData->balance_coins;
        $newBalanceCoin = $balanceCoin + $packageDetails->coins;

        // Update balance in user table
        User::where('id', $userData->id)->update(['balance_coins' => $newBalanceCoin]);


        $transaction = [
            'user_id' => $userData->id,
            'package_article_id' => $packageDetails->package_id,
            'description' => $request->description,
            'amount' => $packageDetails->price,
            'coins' => $packageDetails->coins,
            'balance_coins' => $newBalanceCoin,
            'transaction_mode' => 'CR',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = CoinModel::insertGetId($transaction);
        
        if (!empty($result)) {

            $transaction['id'] = $result;
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($transaction, $this->successText, 'Coins Added Successfully.');

        } else {

            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Something went wrong.');

        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userData = Auth()->user();
        
        if(($userData->user_id != $id) && ($userData->user_type != 'admin')) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $data = CoinModel::where('user_id', $id)->get()->first();
        
        if($data) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Coins Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Coins Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}