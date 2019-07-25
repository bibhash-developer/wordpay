<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PackageModel;
use Validator;


class PackageController extends Controller
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

        if($userData->user_type == 'admin') {
            $data = PackageModel::withTrashed()->orderby('package_id', 'DESC')->get();
        }
        else {
            $data = PackageModel::orderby('package_id', 'DESC')->get();
        }
       
        if($data) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Packages Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Packages Found.');
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
            'package_name'  =>'required',
            'package_type'  => 'required',
            'country_id'   => 'required',
            'coins'         => 'required',
            'price'         =>'required',
            //'discount'      => 'required',
            //'discount_schedule' => 'required',
            'color_code'    => 'required',
        ]);

    
        if($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $userData = Auth::user();
        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }


        $postedData = $request->all();
        $postedData['created_at'] = date('Y-m-d H:i:s');
        $date = $request->discount_schedule;
	    $publishedDate = $request->published_at;
        if (!empty($date)) {
	        $postedData['discount_schedule'] = date("Y-m-d H:i:s", strtotime($date));
        }
        if (!empty($publishedDate)) {
	        $postedData['published_at'] = date("Y-m-d H:i:s", strtotime($publishedDate));
        }
        //print_r($postedData);die;
        $recordid   = PackageModel::insertGetId($postedData);

        if(!empty($recordid)) {

            $postedData['package_id'] = $recordid;
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($postedData, $this->successText, 'Packages Added Successfully.');
            
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
        $data = PackageModel::where('package_id', $id)->get()->first();
       
        if($data) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Packages Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Packages Found.');
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
            'package_name'  =>'required',
            'package_type'  => 'required',
            'country_id'   => 'required',
            'coins'         => 'required',
            'price'         =>'required',
            //'discount'      => 'required',
            //'discount_schedule' => 'required',
            'color_code'    => 'required',
        ]);
    
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $userData = Auth::user();
        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        

        $package = PackageModel::find($id);
        if(empty($package)){
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Packages Found.');
        }
        else {

            $postedData = $request->all();
            $postedData['updated_at'] = date('Y-m-d H:i:s');
            $data = PackageModel::where('package_id', $id)->update($postedData);
            
            if(!empty($data)){

                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response(2, $this->successText, 'Packages Updated Successfully.');

            } else {

                $this->responseStatusCode = $this->statusCodes->not_found;
                $response = api_create_response(2, $this->failureText, 'No Packages Found.');

           }
        }
        
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

        $userData = Auth::user();
        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $record = PackageModel::find($id);
 
        if(!empty($record)){

            $data = $record->delete();
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(2, $this->successText, 'Packages Deleted Successfully.');

        } else {

            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Packages Found.');

        }

        return response()->json($response, $this->responseStatusCode);
    }
}