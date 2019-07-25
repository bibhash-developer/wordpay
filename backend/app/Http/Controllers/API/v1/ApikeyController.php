<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ApikeyModel;
use Validator;

class ApikeyController extends Controller
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

            $data = ApikeyModel::where('company_id', $userData->company_id)->get();
        
            if($data) {
                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response($data, $this->successText, 'Api Key Details.');
            }
            else {
                $this->responseStatusCode = $this->statusCodes->not_found;
                $response = api_create_response(2, $this->failureText, 'No Api Key Found.');
            }
        } else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Invalid User.');
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
                    'company_id'=>'required',
                    'domain'    => 'required|min:15|url',
                    'name'      => 'required',
                    'apply_vat' => 'required',
        ]);

        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $postedData = $request->all();
        $helperData = Get_Api_secret_key();
       
        $postedData['key']        = $helperData['key'];
        $postedData['secret_key'] = $helperData['secret_key'];
        $postedData['status']     = $helperData['status'];
        $postedData['created_at'] = date('Y-m-d H:i:s');

        $data = ApikeyModel::where(['name' => $postedData['name'], 'domain' => $postedData['domain']])->first();

        if(!empty($data['cpd_id'])){

              unset($postedData['created_at']);
              $postedData['updated_at'] = date('Y-m-d H:i:s');
              ApikeyModel::where('cpd_id', $data['cpd_id'])->update($postedData);
             
              $this->responseStatusCode = $this->statusCodes->success;
              $response = api_create_response(2, $this->successText, 'Api Key Updated Successfully.');  
        }else{

            $apikeyId = ApikeyModel::insertGetId($postedData);
            if (!empty($apikeyId)) {

                 $postedData['cpd_id'] = $apikeyId;
                 $this->responseStatusCode = $this->statusCodes->success;
                 $response = api_create_response($postedData, $this->successText, 'Api Key Added Successfully.');
            
            } else {
                
                $this->responseStatusCode = $this->statusCodes->bad_request;
                $response = api_create_response(2, $this->failureText, 'Something went wrong.');
            }      
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
        $userData = Auth::user();

        $data = ApikeyModel::where('cpd_id', $id)->first();
       
        if($data) {

            if(($data->company_id == $userData->company_id) || ($userData->user_type == 'admin')) {
                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response($data, $this->successText, 'Api Key Details.');
            }
            else {
                $this->responseStatusCode = $this->statusCodes->not_found;
                $response = api_create_response(2, $this->failureText, 'No Api Key Found.');
            }

        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Api Key Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Show the form for validate the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function apikey_validate(Request $request)
    {
         $validator = Validator::make($request->all(), [
                    'domain'     => 'required',
                    'key'        => 'required',
                    'secret_key' => 'required',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
		$validateKeys = array(
			'key' => $request->key,
			'secret_key' => $request->secret_key,
			'domain' => $request->domain,
		);
        $data = ApikeyModel::where($validateKeys)->first();

        if(isset($data)) {
            
            // Update used_at
            $data->used_at = date('Y-m-d H:i:s');
            $data->save();

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Api Key Details.');
            
        }else{

            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Api Key Found.');
        }
               
        return response()->json($response, $this->responseStatusCode);
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