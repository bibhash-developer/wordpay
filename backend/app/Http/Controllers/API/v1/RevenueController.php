<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ArticleModel;
use Validator;
use DB;

class RevenueController extends Controller
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

        if(in_array($userData->user_type, ['media'])) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            //return response()->json($response, $this->statusCodes->bad_request);
        }

        $companyId = $userData->company_id;
        $cpdIds = DB::table('api_keys')->select('cpd_id')->where(['company_id' => $companyId])->get();
        $arr = [];
        foreach($cpdIds as $cpdId) {
            $arr[] = $cpdId->cpd_id;
        }

        $data = ArticleModel::whereIN('cpd_id', $arr)->get();
       
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Revenue Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Revenue Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyRevenueList($companyId)
    {
        $userData = Auth::user();

        if($userData->company_id != $companyId && $userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $cpdIds = DB::table('api_keys')->select('cpd_id')->where(['company_id' => $companyId])->get();
        $arr = [];
        foreach($cpdIds as $cpdId) {
            $arr[] = $cpdId->cpd_id;
        }

        $data = ArticleModel::whereIN('cpd_id', $arr)->get();
       
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Revenue Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Revenue Found.');
        }
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Revenue list from wordpress
     */
    public function wordpressCompanyRevenueList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'=>'required'
        ]);

        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $companyId = $request->company_id;
        
        $cpdIds = DB::table('api_keys')->select('cpd_id')->where(['company_id' => $companyId])->get();
        $arr = [];
        foreach($cpdIds as $cpdId) {
            $arr[] = $cpdId->cpd_id;
        }

        $data = ArticleModel::whereIN('cpd_id', $arr)->get();
       
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Revenue Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Revenue Found.');
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
        /*$validator = Validator::make($request->all(), [
                    'cpd_id'                  =>'required',
                    'wordpress_article_type'  => 'required',
                    'wordpress_title'         => 'required',
                    'wordpress_post_id'       => 'required',
                    'wordpress_post_url'      =>'required|min:15',
                    'wordpress_max_price'     => 'required',
                    'wordpress_min_price'     => 'required',
                    'wordpress_fixed_coins'   => 'required',
        ]);

        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        $postedData               = $request->all();
        $postedData['created_at'] = date('Y-m-d H:i:s');
        $postedData['updated_at'] = date('Y-m-d H:i:s');

        $revenuid   = ArticleModel::insertGetId($postedData);

        if (!empty($revenuid)) {
            $postedData['id'] = $revenuid;
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($postedData, $this->successText, 'Revenue Successfully.');
            
        }else{
            
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Something went wrong.');
        }
               
        return response()->json($response, $this->responseStatusCode);*/
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ArticleModel::where('article_id', $id)->get()->first();
       
        if($data) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Revenue Details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Revenue Found.');
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
    }
}
