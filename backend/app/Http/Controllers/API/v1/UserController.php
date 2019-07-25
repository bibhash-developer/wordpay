<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;
use Validator;

use DB;
use Hash;
use Mail;

use App\Mail\UserForgotPassword;
use App\Mail\UserResetPassword;
use App\Mail\UserActivationLink;

class UserController extends Controller {
    
    private $statusCodes, $responseStatusCode, $successText, $failureText;
    public function __construct() {
        $this->statusCodes = config('api.status_codes');
        $this->tokenName = config('api.TOKEN_NAME');
        $this->successText = config('api.SUCCESS_TEXT');
        $this->failureText = config('api.FAILURE_TEXT');
    }
    

    /**
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    //'email' => 'required|email',
                    'user_type' => 'required',
                    'password' => [
                        'required',
                        'min:6',
                        'max:20'
                    ],
                    //'confirm_password' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
        
        $input = $request->all();

        $input['password'] = bcrypt($input['password']);
        $input['account_id'] = substr_replace($this->generateAccountId(9), '-', 3, 0);;
        $input['activation_code'] = $this->generateAccountId(50);
        unset($input['confirm_password']);
        //unset($input['type']);
        $user = User::create($input);
        
        if (!empty($user)) {

            //dd($user);die;
            
            $res['first_name'] = $user->first_name;
            $res['last_name'] = $user->last_name;
            $res['email'] = $user->email;
            $res['user_id'] = $user->id;
            $res['account_id'] = $user->account_id;
            $res['activation_code'] = $user->activation_code;
            //$res['token'] = $user->createToken($this->tokenName)->accessToken;

            // Send mail
            Mail::to($user->email)->send(new UserActivationLink($res));

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($res, $this->successText, 'Registration successfull, please verify your email to login.');
            
        } else {
            
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Something went wrong.');
        }
        //pr($response);die;
        
        return response()->json($response, $this->responseStatusCode);
    }
    

    /**
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function logout(Request $request) {
        
        //$userData = Auth::user();
        //pr(Auth::user()->token());

        Auth::user()->token()->revoke();
        Auth::user()->token()->delete();


        if(true){

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(2, $this->successText, 'You have been logged out successfully.');
            
        } else {
            
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Something went wrong.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }
    
    public function user_activation($token='') {

        if(empty($token)) {
            $response = api_create_response(2, $this->failureText, 'Invalid Token.');
            return response()->json($response, $this->statusCodes->not_found);
        }
        
        //$user = User::where($validateUserArr)->first();
        $user = User::where(['activation_code' => $token])->first();
        // pr($user);die;
        
        if (!empty($user)) {

            $user->activation_code = '';
            $user->save();

            // Remove key
            DB::table('password_resets')->where(['email' => $user->email])->delete();

            // Send mail
            //Mail::to($user->email)->send(new UserResetPassword($user));

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(2, $this->successText, 'Email Verified Successfully.');

        } else {

            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'Invalid Token.');

        }
        
        return response()->json($response, $this->responseStatusCode);
    }
    
    
    /**
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login(Request $request) {
        
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, '');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
        if(!empty(request('user_type')))  {
            $where = array('email' => request('email'), 'password' => request('password'), 'user_type' => request('user_type'));
        } else {
            $where = array('email' => request('email'), 'password' => request('password'));
        }        
        if (Auth::attempt($where)) {
            
            $user = Auth::user();

            if(!empty($user->activation_code)) {
                $response = api_create_response(2, $this->failureText, 'Please verify your email.');
                return response()->json($response, $this->statusCodes->unprocessable);
            }

            // INSERT LOG
            DB::table('user_login_logs')->insert(['user_id' => $user->id, 'domain' => $request->domain, 'server_details' => (!empty($request->server_details) ? json_encode($request->server_details) : NULL), 'created_at' => date('Y-m-d H:i:s')]);

            $user['user_id'] = $user->id;
            
            $user['token'] = $user->createToken($this->tokenName)->accessToken;
            $this->responseStatusCode = $this->statusCodes->success;
            
            unset($user->id);
            $response = api_create_response($user, $this->successText, 'Logged in successfully.');
            
        } else {
            
            $this->responseStatusCode = $this->statusCodes->unauthorised;
            $response = api_create_response(2, $this->failureText, 'Please enter valid credentials.');
        }
        //pr($response);die;
        
        return response()->json($response, $this->responseStatusCode);
    }
    

    /**
     * Social Login Api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function social_login(Request $request) {
        $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'email' => 'required|email',
                    'user_type' => 'required',
                    'social_type' => 'required',
                    'social_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
        $input = $request->all();
        
        // Check if already exists
        $validateUserArr = array(
            //'user_type' => $input['user_type'],
            'email' => $input['email'],
        );
        $user = User::where($validateUserArr)->first();
        
        if(empty($user)) {
            // Register and return token

            if($request->type == 'login') {
                // If request is for login
                $response = api_create_response(2, $this->failureText, 'Please sign up first.');
                return response()->json($response, $this->statusCodes->unprocessable);
            }

            $input['account_id'] = substr_replace($this->generateAccountId(9), '-', 3, 0);
            $input['created_at'] = date('Y-m-d H:i:s');
            unset($input['type']);
            $userId = User::insertGetId($input);

            $user = User::find($userId);
            $msg = 'Registration successfully.';
        }
        else {
            $msg = 'Login successfully.';
        }
        //pr($userDetails);die;

        
        // INSERT LOG
        DB::table('user_login_logs')->insert(['user_id' => $user->id, 'domain' => $request->domain, 'server_details' => (!empty($request->server_details) ? json_encode($request->server_details) : NULL), 'created_at' => date('Y-m-d H:i:s')]);

        $res['user_id'] = $user->id;
        $res['first_name'] = $user->first_name;
        $res['last_name'] = $user->last_name;
        $res['email'] = $user->email;
        $res['account_id'] = $user->account_id;
        $res['company_id'] = !empty($user->company_id) ? $user->company_id : 0;
        $res['user_type'] = $user->user_type;
        $res['token'] = $user->createToken($this->tokenName)->accessToken;
        
        
        if (!empty($user)) {

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($res, $this->successText, $msg);

        } else {

            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Something went wrong.');

        }
        
        return response()->json($response, $this->responseStatusCode);
    }
    

    /**
     * Forgot Password Api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function forgot_password(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
        // Check if exists
        $validateUserArr = array(
            //'user_type' => $input['user_type'],
            'email' => $request->email,
        );
        $user = User::where($validateUserArr)->first();
        
        if (!empty($user)) {

            // Update Token && Send To Mail
            $token = $this->generateAccountId(50);
            DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token, 'created_at' => date('Y-m-d H:i:s')]);
            $user->token = $token;

            // TODO :: Send mail
            Mail::to($request->email)->send(new UserForgotPassword($user));

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(2, $this->successText, 'We have sent an email to your mail.');

        } else {

            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'Email not found.');

        }
        
        return response()->json($response, $this->responseStatusCode);
    }
    

    /**
     * Reset Password Api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function reset_password(Request $request, $token='') {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'min:6',
                'max:20'
            ],
            'confirm_password' => 'required|same:password',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        if(empty($token)) {
            $response = api_create_response(2, $this->failureText, 'Invalid Token.');
            return response()->json($response, $this->statusCodes->not_found);
        }
        
        //$user = User::where($validateUserArr)->first();
        $user = DB::table('password_resets')->where(['token' => $token])->first();
        // pr($user);die;
        
        if (!empty($user)) {

            $user = User::where(['email' => $user->email])->first();
            $user->password = bcrypt($request->password);
            $user->save();

            // Remove key
            DB::table('password_resets')->where(['email' => $user->email])->delete();

            // TODO :: Send mail
            Mail::to($user->email)->send(new UserResetPassword($user));

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(2, $this->successText, 'Password Changed Successfully.');

        } else {

            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'Invalid Token.');

        }
        
        return response()->json($response, $this->responseStatusCode);
    }



    /**
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function details() {
        
        $user = Auth::user();
        
        if (!empty($user)) {

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($user, $this->successText, 'User details.');
            
        } else {
            
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Invalid User.');
        }
        //pr($response);die;
        
        return response()->json($response, $this->responseStatusCode);
    }

    
    /**
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function updateUser(Request $input, $userId='') {
       // dd($input->all()['confirm_password']);die;
        if(empty($input->old_password) && empty($input->new_password) && empty($input->confirm_password)) { 
            $x = array('new_password', 'confirm_password', 'old_password');
            unset($input->all()['confirm_password']);
           // unsetKeys($input->all(), $x);
        }
        $validator = Validator::make($input->all(), [
            'first_name' =>'required',
            'email' => [
                'required',
                'email',
                'unique:users,email,' . $userId
                //Rule::unique('users')->ignore($userId),
            ],
            'country_id' => 'required',
            'phone' => 'required|numeric|digits_between:8,11',
            'old_password' => 'required_with:new_password',
            'new_password' => 'nullable|sometimes|min:6|max:20',            
            'confirm_password' => 'nullable|sometimes|same:confirm_password',
        ],
        [
            'country_id.required' => 'The country field is required.',
        ]);

        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }
        
        $user = Auth::user();
        
        if (!empty($user)) {

            if(!empty($input->new_password) && ($input->old_password == $input->new_password))
            {            
                 $this->responseStatusCode = $this->statusCodes->bad_request;
                 $response = api_create_response($validator->errors()->add('new_password', 'New password can not be same as Old password.'), $this->failureText, 'Please enter valid input.');
            }
            elseif((!empty($input->new_password) || !empty($input->confirm_password)) && ($input->confirm_password != $input->new_password))
            {                
                 $this->responseStatusCode = $this->statusCodes->bad_request;
                 $response = api_create_response($validator->errors()->add('confirm_password', 'New and Confirm password not matched.'), $this->failureText, 'Please enter valid input.');
            }
            elseif(!empty($input->old_password) && !empty($input->new_password) && !empty($input->confirm_password)) {

                /*if($input->new_password != $input->confirm_password) {
                    $this->responseStatusCode = $this->statusCodes->bad_request;
                    $response = api_create_response(2, $this->failureText, 'New and Confirm password not matched.');
                }
                else*/
                //echo $input->old_password."==".$input->new_password;
           // die();
                if (Hash::check($input->old_password, $user->password)) {
                    $user->password = bcrypt($input->new_password);
                }                
                else {
                    $this->responseStatusCode = $this->statusCodes->bad_request;
                    $response = api_create_response($validator->errors()->add('old_password', 'Old password not correct.'), $this->failureText, 'Please enter valid input.');
                }
            }

            
            if(empty($response)) {

                $user->first_name = $input->first_name;
                $user->last_name = $input->last_name;
                $user->email = $input->email;
                $user->address = $input->address;
                $user->city = $input->city;
                $user->state = $input->state;
                $user->postal_code = $input->postal_code;
                $user->country_id = $input->country_id;
                $user->phone = $input->phone;
                $user->save();

                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response($user, $this->successText, 'User updated successfully.');
            }
            
        } else {

            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Invalid User.');

        }
        
        return response()->json($response, $this->responseStatusCode);
    }


    /**
     * Get Coins 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function getCoins(Request $request) {
        
        $user = Auth::user();
        // pr($user);die;

        if(isset($request->wpmode) && ($request->wpmode == 0)) {
            $coinBalance = $user->test_coins;
        }
        else {
            $coinBalance = $user->balance_coins;
        }
        
        
        if (!empty($user)) {

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(['coins' => $coinBalance], $this->successText, 'Coins details.');
            
        } else {
            
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Invalid User.');
        }
        //pr($response);die;
        
        return response()->json($response, $this->responseStatusCode);
    }

    /**
     * Do Payout 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function doPayout(Request $request) {
        
        $user = Auth::user();
        // pr($user->company_id);die;

        $cpdIds = \App\Models\ApikeyModel::where('company_id', $user->company_id)->pluck('cpd_id')->toArray();
        if(empty($cpdIds)) {
            $response = api_create_response(2, $this->failureText, 'Invalid Request.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $companyDetails = \App\Models\CompanyModel::where('company_id', $user->company_id)->first();
        $revenuePerCoins = $companyDetails->revenue_per_coin;

        $articleTransaction = \App\Models\ArticleTransactionModel::whereNull('payout_id')->whereNull('payout_id')->WhereIn('cpd_id', $cpdIds)->get();
        
        
        $articleIdArr = [];
        $totalCoins = 0;
        foreach($articleTransaction as $at) {
            // pr($at);
            $totalCoins += $at->total_coin_debit;

            $articleIdArr[] = $at->article_transaction_id;
        }
        $payoutCoin = ($revenuePerCoins/100)*$totalCoins;
        $payoutDate = date('Y-m-d H:i:s');

        if(!empty($articleIdArr)) {
            $payoutId = \App\Models\Payout::insertGetId([
                'company_id' => $user->company_id,
                'description' => 'Payout on '. $payoutDate,
                'no_of_articles' => count($articleIdArr),
                'paid_amount' => $payoutCoin,
                'total_paid_coins' => $totalCoins,
                'payout_date' => $payoutDate,
            ]);

            // Update article transaction table
            \App\Models\ArticleTransactionModel::whereIn('article_transaction_id', $articleIdArr)->update([
                'payout_id' => $payoutId,
                'payout_date' => $payoutDate,
            ]);

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(['payout' => $totalCoins], $this->successText, 'Next Payout details.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Nothing for payout.');
        }

        
        return response()->json($response, $this->responseStatusCode);
    }

/**
     * Next Payout 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function nextPayout(Request $request) {
        
        $user = Auth::user();
        // pr($user->company_id);die;

        $cpdIds = \App\Models\ApikeyModel::where('company_id', $user->company_id)->pluck('cpd_id')->toArray();
        if(empty($cpdIds)) {
            $response = api_create_response(2, $this->failureText, 'Invalid Request.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $companyDetails = \App\Models\CompanyModel::where('company_id', $user->company_id)->first();
        $revenuePerCoins = $companyDetails->revenue_per_coin;

        $articleTransaction = \App\Models\ArticleTransactionModel::whereNull('payout_id')->whereNull('payout_id')->WhereIn('cpd_id', $cpdIds)->get();
        
        
        $articleIdArr = [];
        $totalCoins = 0;
        foreach($articleTransaction as $at) {
            // pr($at);
            $totalCoins += $at->total_coin_debit;

            $articleIdArr[] = $at->article_transaction_id;
        }
        $payoutCoin = ($revenuePerCoins/100)*$totalCoins;
        $payoutDate = date('Y-m-d H:i:s');

        if(!empty($articleIdArr)) {

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(['payout' => $payoutCoin], $this->successText, 'Next Payout coin.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'Nothing for coin.');
        }

        
        return response()->json($response, $this->responseStatusCode);
    }

    public function mediaGraphData($companyId='') {

        $userData = Auth::user();

        if($userData->company_id != $companyId && $userData->user_type != 'admin') {
            $response = api_create_response(2, $this->failureText, 'Not allowed.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        $responseData = [
            'revenue_this_week' => '57.151 kr',
            'revenue_this_week_percent' => '4.14%',
            'article_purchased_this_week' => '38.910',
            'article_purchased_this_week_percent' => '7.31%',
            'next_payout' => '87.151,00 kr',
            'next_payout_percent' => '17.51%',
            'next_payout_done' => '68.910 kr',
            'paid_payout_percent' => '21.11%',
            'paid_payout_date' => '2019-01-20 12:00:00'
        ];

        $response = api_create_response($responseData, $this->successText, 'Graph Data.');
        return response()->json($response, $this->statusCodes->success);
    }

    public function adminDashboardGraphData($companyId='') {

        $responseData = [
            'revenue' => '57.151 kr',
            'revenue_percentage' => '4.14%',
            'article_purchased' => '38.910',
            'article_purchased_percentage' => '7.31%',
            'next_payout' => '87.151,00 kr',
            'next_payout_percent' => '17.51%',
            'next_payout_done' => '68.910 kr',
            'paid_payout_percent' => '21.11%',
            'paid_payout_date' => '2019-01-20 12:00:00',
            
            'unused_coins' => '1.290.000',
            'unused_coins_percentage' => '15.14%',
            'coin_purchased' => '26.000',
            'coin_purchased_percentage' => '15.14%',
            'coin_spent' => '51.880',
            'coin_spent_percentage' => '15.14%'
        ];

        $response = api_create_response($responseData, $this->successText, 'Graph Data.');
        return response()->json($response, $this->statusCodes->success);
    }

    


    /**
     * Generate Account ID
     */
    private function generateAccountId($length = 10) {
        //$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}