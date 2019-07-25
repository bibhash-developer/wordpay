<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;


use App\Models\Payout;


class PayoutController extends Controller
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
    public function index()
    {
        $userData = Auth::user();

        //print_r($userData); exit;
        if(!empty($userData->company_id)){
            $data = Payout::where('company_id', $userData->company_id)->orderBy('payout_date' , 'DESC')->get();
        
            if(count($data)) {
                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response($data, $this->successText, 'Payout List.');
            }
            else {
                $this->responseStatusCode = $this->statusCodes->not_found;
                $response = api_create_response(2, $this->failureText, 'No Payout Found.');
            }
        } else {
            $this->responseStatusCode = $this->statusCodes->unprocessable;
            $response = api_create_response(2, $this->failureText, 'No Payout Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyPayoutList($companyId)
    {
        $userData = Auth::user();

        if($userData->company_id != $companyId && $userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $data = Payout::where('company_id', $companyId)->orderBy('payout_date' , 'DESC')->get();
    
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Payout List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Payout Found.');
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
            'company_id'            =>'required',
            'description'           => 'required',
            'no_of_articles'        => 'required',
            'paid_amount'           => 'required',
            'total_paid_coins'      => 'required',
        ]);
    
        if($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $userData = Auth::user();
        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            // return response()->json($response, $this->statusCodes->bad_request);
        }


        $postedData = $request->all();
        $postedData['payout_date'] = date('Y-m-d H:i:s');
        $recordId   = Payout::insertGetId($postedData);

        if(!empty($recordId)) {

            $postedData['payout_id'] = $recordId;
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($postedData, $this->successText, 'Payout Done Successfully.');
            
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
        //
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
