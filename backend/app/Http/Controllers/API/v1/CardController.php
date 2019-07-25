<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\CardModel;
use Validator;

class CardController extends Controller
{

    private $statusCodes, $responseStatusCode, $successText, $failureText;
    public function __construct() {

        //$this->middleware('auth');
        $this->middleware('admin_user', ['only' => ['userCardList']]);
        //$this->middleware('subscribed', ['except' => ['fooAction', 'barAction']]);


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
        
        $data = CardModel::where('user_id', $userData->id)->orderBy('is_default' , 'DESC')->get();
    
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Card List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Card Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userCardList($userId)
    {
        $userData = Auth::user();

        if($userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $data = CardModel::where('user_id', $userId)->orderBy('is_default' , 'DESC')->get();
    
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Card List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Card Found.');
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
                    'user_id'   =>'required',
                    'card_type' =>'required',
                    'number'    =>'required|alpha_num|min:12|max:16',
                    'expired_on' =>'required|date_format:m/y',
                    'cvc'       =>'required|numeric',
                    'first_name'=> 'required',
                    'last_name' => 'required',
                    'address'   => 'required',
                    'city'      => 'required',
                    'state'     => 'required',
                    'postal_code'=> 'required|numeric',
                    'country'    => 'required',
                    'phone'      => 'required||numeric|digits_between:8,11',
                    'is_default'    => 'required',
        ],
        [
            'expired_on.required' => 'The expired on does not match the format MM/YY.',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        // USER TYPE VALIDATION
        $userData = Auth::user();

        if(($request->user_id != $userData->id) && ($userData->user_type != 'admin')) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $postedData = $request->all();
        $postedData['created_at'] = date('Y-m-d H:i:s');
        
        $cardId     = CardModel::insertCardId($postedData);
        
        if (!empty($cardId)) {

            if($request->is_default == 1) {
                CardModel::where(['user_id' => $request->user_id])->where('card_id', '!=', $cardId)->update(['is_default' => '0']);
            }

            $postedData['card_id'] = $cardId;
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($postedData, $this->successText, 'Card Added Successfully.');
            
        }else{
            
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
        
        $data = CardModel::find($id);
        if(empty($data)) {
            $response = api_create_response(2, $this->failureText, 'No Card Found.');
            return response()->json($response, $this->statusCodes->not_found);
        }


        // USER TYPE VALIDATION
        $userData = Auth::user();

        if(($data->user_id != $userData->id) && ($userData->user_type != 'admin')) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        
        $this->responseStatusCode = $this->statusCodes->success;
        $response = api_create_response($data, $this->successText, 'Cards Details.');
        
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
                    //'user_id'   =>'required',
                    'card_type' =>'required',
                    'number'    =>'required|alpha_num|min:12|max:16',
                    'expired_on' =>'required|date_format:m/y',
                    'cvc'       =>'required|numeric',
                    'first_name'=> 'required',
                    'last_name' => 'required',
                    'address'   => 'required',
                    'city'      => 'required',
                    'state'     => 'required',
                    'postal_code'=> 'required|numeric',
                    'country'    => 'required',
                    'phone'      => 'required||numeric|digits_between:8,11',
                    'is_default'    => 'required',
        ],
        [
            'expired_on.required' => 'The expired on does not match the format MM/YY.',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
           return response()->json($response, $this->statusCodes->bad_request);
        }

        $record = CardModel::find($id);
        if(empty($record)) {
            $response = api_create_response(2, $this->failureText, 'No Card Found.');
            return response()->json($response, $this->statusCodes->not_found);
        }

        $userData = Auth::user();

        if($userData->id == $record['user_id']) {
        
            $postedData = $request->all();
            $postedData['updated_at'] = date('Y-m-d H:i:s');

            $data = CardModel::where('card_id', $id)->update($postedData);
        
            if(!empty($data)) {

                if($request->is_default == 1) {
                    CardModel::where(['user_id' => $request->user_id])->where('card_id', '!=', $id)->update(['is_default' => '0']);
                }

                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response(2, $this->successText, 'Card Updated Successfully.');

            } else {

                $this->responseStatusCode = $this->statusCodes->bad_request;
                $response = api_create_response(2, $this->failureText, 'No Card Found.');

            }

        } else {

            $this->responseStatusCode = $this->statusCodes->unprocessable;
            $response = api_create_response(2, $this->failureText, 'Invalid Card.');

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
        $record = CardModel::find($id);
        if(empty($record)) {
            $response = api_create_response(2, $this->failureText, 'No Card Found.');
            return response()->json($response, $this->statusCodes->not_found);
        }
        
        if(!empty($record->is_default)) {
            $response = api_create_response(2, $this->failureText, 'This is default card, Please make another card as default first.');
            return response()->json($response, $this->statusCodes->bad_request);
        }


        $userData = Auth()->user();
        
        if(($userData->id != $record->user_id) && ($userData->user_type != 'admin')) {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $record->delete();
        $response = api_create_response(2, $this->successText, 'Card Deleted Successfully.');
        return response()->json($response, $this->statusCodes->success);
    }
}
