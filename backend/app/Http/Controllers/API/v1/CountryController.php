<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CountryModel;
use Validator;

class CountryController extends Controller
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
        $data = CountryModel::get();
       
        if($data) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Country List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Country Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }
    
    
    public function wordpressCountryList(Request $request) {

        $vatData = \App\Models\ApikeyModel::find($request->cpd_id);
        if(empty($vatData)) {
            $response = api_create_response(2, $this->failureText, 'Invalid CPD Id.');
            return response()->json($response, $this->statusCodes->not_found);
        }

        $payload = array();
        $payload['vat_apply'] = $vatData->apply_vat;
        $payload['country_list'] = CountryModel::get();
       
        if($payload) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($payload, $this->successText, 'Country List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Country Found.');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = CountryModel::where('country_id', $id)->get()->first();
       
        if($data) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Country Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Country Found.');
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
        $validator = Validator::make($request->all(), [
                    'vat'           => 'required',
                    'currancy'       => 'required',
                    'currancy_symbol' => 'required',
         ]);
        
        if ($validator->fails()) {
           $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
           return response()->json($response, $this->statusCodes->bad_request);
        }

        $countryData = CountryModel::find($id);
        //pr($countryData);die;

        if(empty($countryData)) {
            // NOT FOUND
            $response = api_create_response(2, $this->failureText, 'Country Not Found.');
            return response()->json($response, $this->statusCodes->not_found);
        }

        $postedData = $request->all();
        // pr($postedData);die;
                
        $data = CountryModel::where('country_id', $id)->update($postedData);

        $this->responseStatusCode = $this->statusCodes->success;
        $response = api_create_response(2, $this->successText, 'Country Updated Successfully.');

        
        return response()->json($response, $this->responseStatusCode);
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
