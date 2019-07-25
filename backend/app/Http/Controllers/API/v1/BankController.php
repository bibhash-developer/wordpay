<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BankModel;
use Validator;

class BankController extends Controller
{

    private $statusCodes, $responseStatusCode, $successText, $failureText;
    public function __construct() {

        $this->middleware('admin_user', ['only' => ['companyBankList']]);

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
            $data = BankModel::where('company_id', $userData->company_id)->orderBy('is_default' , 'DESC')->get();
        
            if(count($data)) {
                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response($data, $this->successText, 'Bank List.');
            }
            else {
                $this->responseStatusCode = $this->statusCodes->not_found;
                $response = api_create_response(2, $this->failureText, 'No Bank Found.');
            }
        } else {
            $this->responseStatusCode = $this->statusCodes->unprocessable;
            $response = api_create_response(2, $this->failureText, 'Please add your company details first.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function companyBankList($companyId)
    {
        $userData = Auth::user();

        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $data = BankModel::where('company_id', $companyId)->orderBy('is_default' , 'DESC')->get();
    
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Bank List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Bank Found.');
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
                    'company_id'  => 'required',
                    //'user_id'     => 'required',
                    'bank_name'   => 'required',
                    'iban_number' => 'required|alpha_num|min:12|max:16',
                    'swift_code'  => 'required|alpha_num|min:12|max:16',
                    'is_default'     => 'required',
        ]);
        
        if ($validator->fails()) {
           $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
           return response()->json($response, $this->statusCodes->bad_request);
        }

        // USER TYPE VALIDATION
        $userData = Auth::user();

        if(($request->company_id != $userData->company_id) && ($userData->user_type != 'admin')) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
        $postedData = $request->all();
        $postedData['created_at'] = date('Y-m-d H:i:s');
        $postedData['user_id'] = Auth::user()->id;
        
        $bankId = BankModel::insertGetId($postedData);
         
        if (!empty($bankId)) {
            $postedData['bank_id'] = $bankId;

            if($request->is_default == 1) {
                BankModel::where(['company_id' => $request->company_id])->where('bank_id', '!=', $bankId)->update(['is_default' => '0']);
            }

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($postedData, $this->successText, 'Bank Added Successfully.');

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
        $data = BankModel::find($id);
        if(empty($data)) {
            $response = api_create_response(2, $this->failureText, 'No Bank Found.');
            return response()->json($response, $this->statusCodes->not_found);
        }


        // USER TYPE VALIDATION
        $userData = Auth::user();

        if(($data->company_id != $userData->company_id) && ($userData->user_type != 'admin')) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        
        $this->responseStatusCode = $this->statusCodes->success;
        $response = api_create_response($data, $this->successText, 'Bank Details.');
        
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
         // USER TYPE VALIDATION
        $userData = Auth::user();

         $validator = Validator::make($request->all(), [
                    'company_id'  => 'required',
                    //'user_id'     => 'required',
                    'bank_name'   => 'required',
                    'iban_number' => 'required|alpha_num|min:12|max:16',
                    'swift_code'  => 'required|alpha_num|min:12|max:16',
                    'is_default'  => 'required',
        ]);
        
        if ($validator->fails()) {
           $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
           return response()->json($response, $this->statusCodes->bad_request);
        }

        $bankData = BankModel::find($id);
        if(empty($bankData)) {
            $response = api_create_response(2, $this->failureText, 'No Card Found.');
            return response()->json($response, $this->statusCodes->not_found);
        }
        
        
        // Validate user id
        $userId = Auth::user()->id;

        if($userData->company_id == $request['company_id']) {
            
            $postedData = $request->all();
            $postedData['updated_at'] = date('Y-m-d H:i:s');
            $data = BankModel::where('bank_id', $id)->update($postedData);
                
            if(!empty($data)){

                if($request->is_default == 1) {
                    BankModel::where(['company_id' => $request->company_id])->where('bank_id', '!=', $id)->update(['is_default' => '0']);
                }

                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response(2, $this->successText, 'Bank Updated Successfully.');
            } else {
                $this->responseStatusCode = $this->statusCodes->bad_request;
                $response = api_create_response(2, $this->failureText, 'Something went wrong.');
            }

        } else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'Invalid Bank.');
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
        $record = BankModel::find($id);
        if(empty($record)) {
            $response = api_create_response(2, $this->failureText, 'No Bank Found.');
            return response()->json($response, $this->statusCodes->not_found);
        }

        if(!empty($record->is_default)) {
            $response = api_create_response(2, $this->failureText, 'This is default bank, Please make another bank as default first.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $userData = Auth()->user();
        
        if(($userData->company_id != $record->company_id) && ($userData->user_type != 'admin')) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $record->delete();
        $response = api_create_response(2, $this->successText, 'Bank Deleted Successfully.');
        return response()->json($response, $this->statusCodes->success);
    }
}