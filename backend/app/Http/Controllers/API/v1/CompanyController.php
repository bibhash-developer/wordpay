<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyModel;
use App\Models\User;

use DB;
use Validator;

class CompanyController extends Controller
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
        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $list = DB::table('companies')
                -> select(
                    '*',
                    DB::raw('(SELECT IFNULL(SUM(visitors), 0) FROM articles WHERE cpd_id IN (SELECT cpd_id FROM api_keys WHERE company_id=companies.company_id)) AS visitors'),
                    DB::raw('(SELECT IFNULL(SUM(purchased), 0) FROM articles WHERE cpd_id IN (SELECT cpd_id FROM api_keys WHERE company_id=companies.company_id)) AS purchased'),
                    DB::raw('(SELECT IFNULL(SUM(coins_used), 0) FROM article_transactions WHERE cpd_id IN (SELECT cpd_id FROM api_keys WHERE company_id=companies.company_id)) AS revenue')
                )
                -> whereNull('deleted_at')
                ->get();

        if($list) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($list, $this->successText, 'Company List.');
        }else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Company Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }


    public function changeStatus(Request $request) {
        
        $userData = Auth::user();
        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $list = CompanyModel::where(['company_id' => $request->company_id])->update(['status' => $request->status]);
        
        $this->responseStatusCode = $this->statusCodes->success;
        $response = api_create_response($list, $this->successText, 'Company status updated successfully.');
        return response()->json($response, $this->responseStatusCode);
    }


    public function updatePayoutDetails(Request $request) {
        
        $validator = Validator::make($request->all(), [
                    'company_id'  => 'required',
                    'revenue_per_coin'     => 'required',
        ]);

        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
        $userData = Auth::user();
        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            //return response()->json($response, $this->statusCodes->bad_request);
        }

        $list = CompanyModel::where(['company_id' => $request->company_id])->update(['revenue_per_coin' => $request->revenue_per_coin]);
        
        $this->responseStatusCode = $this->statusCodes->success;
        $response = api_create_response($list, $this->successText, 'Company payout details updated successfully.');
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
                    'name'  => 'required',
                    'email'   => 'required|email',
                    'address' => 'required',
                    'city'  => 'required',
                    'state'     => 'required',
                    'postal_code'     => 'required',
                    'country_id'     => 'required',
                    'phone'     => 'required|numeric|digits_between:8,11',
                    'vat_number' => 'required',
        ],
        [
            'country_id.required' => 'The country field is required.',
        ]);
        
        if ($validator->fails()) {

           $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
           return response()->json($response, $this->statusCodes->bad_request);

        } else {
            $postedData = $request->all();
            $postedData['created_at'] = date('Y-m-d H:i:s');
            $companyId = CompanyModel::insertGetId($postedData);
            $postedData['company_id'] = $companyId;
            //dd($companyId);die;

            $userData = Auth::user();
            $userData['company_id'] = $companyId;
            $userData->save();

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($postedData, $this->successText, 'Company added successfully.');

            return response()->json($response, $this->responseStatusCode);
        }
        
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
        // pr($userData);die;

        if(($userData->company_id == $id) || ($userData->user_type == 'admin')) {
            
            $data = CompanyModel::find($id);
            
        }

        if(!empty($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Company Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Company Found.');
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
                    'name'  => 'required',
                    'email'   => 'required|email',
                    'address' => 'required',
                    'city'  => 'required',
                    'state'     => 'required',
                    'postal_code'     => 'required|numeric',
                    'country_id'     => 'required',
                    'phone'     => 'required|numeric|digits_between:8,11',
                    'vat_number' => 'required',
        ],
        [
            'country_id.required' => 'The country field is required.',
        ]);
        
        if ($validator->fails()) {

           $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
           return response()->json($response, $this->statusCodes->bad_request);

        } else {
            
            $userData = Auth::user();
            $companyId = $userData->company_id;

            if(($userData->user_type != 'admin') && ($id != $companyId)) {
                $response = api_create_response(2, $this->failureText, 'Invalid Company.');
                return response()->json($response, $this->statusCodes->not_found);
            }

            $postedData = $request->all();
            $postedData['updated_at'] = date('Y-m-d H:i:s');

            CompanyModel::where('company_id', $id)->update($postedData);

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(2, $this->successText, 'Company updated successfully.');

            return response()->json($response, $this->responseStatusCode);
            }
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