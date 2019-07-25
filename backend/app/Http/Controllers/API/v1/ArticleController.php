<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

use App\Models\ApikeyModel;
use App\Models\ArticleModel;
use App\Models\ArticleTransactionModel;
use App\Models\CoinModel;
use App\Models\TestArticleModel;
use App\Models\TestArticleTransactionModel;
use App\Models\TestCoinModel;

class ArticleController extends Controller
{
    
    private $statusCodes, $responseStatusCode, $successText, $failureText;
    public function __construct() {
        $this->statusCodes = config('api.status_codes');
        $this->tokenName = config('api.TOKEN_NAME');
        $this->successText = config('api.SUCCESS_TEXT');
        $this->failureText = config('api.FAILURE_TEXT');
    }


    public function index() {
        $userData = Auth::user();

        $list = ArticleTransactionModel::where([ 'user_id' => $userData->id ])->get();
        // pr($list);die;
        $data = array();
        foreach($list as $li) {
            $l = new \stdclass();
            $l->article_transaction_id = $li->article_transaction_id; 
            $l->user_id = $li->user_id; 
            $l->cpd_id = $li->cpd_id; 
            $l->article_type = $li->article_type; 
            $l->title = $li->title;
            $l->post_id = $li->post_id; 
            $l->post_url = $li->post_url; 
            $l->max_price = $li->max_price; 
            $l->min_price = $li->min_price; 
            $l->coins_used = $li->coins_used; 
            $l->coins_balance = $li->coins_balance; 
            $l->payout_id = $li->payout_id; 
            $l->payout_date = $li->payout_date; 
            $l->purchased_at = $li->purchased_at; 
            $l->media_name = DB::select('SELECT name FROM companies WHERE company_id=(SELECT company_id FROM api_keys WHERE cpd_id='. $li->cpd_id .')')[0]->name;
            $data[] = $l;
        }

        
        if(count($data)) {
            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response($data, $this->successText, 'Usages List.');
        }
        else {
            $this->responseStatusCode = $this->statusCodes->not_found;
            $response = api_create_response(2, $this->failureText, 'No Data Found.');
        }
        
        return response()->json($response, $this->responseStatusCode);
    }

    
    public function read(Request $request) {

        $validator = Validator::make($request->all(), [
            'cpd_id' => 'required',
            'article_type' => 'required',
            'publish_date' => 'required',
            'title' => 'required',
            'post_id' => 'required',
            'post_url' => 'required',
            'max_price' => 'required',
            'min_price' => 'required',
            'fixed_coins' => 'required',
            'purchase' => 'required',
            'wpmode' => 'required'
            //'email' => 'required|email|unique:users,email|same:password',
        ]);
        
        if ($validator->fails()) {
            $response = api_create_response($validator->errors(), $this->failureText, 'Please enter valid input.');
            return response()->json($response, $this->statusCodes->bad_request);
        }

        if($request->wpmode == 1) {
            $data = $this->readInLiveMode($request);
        }
        else {
            $data = $this->readInTestMode($request);
        }

        return response()->json($data['response'], $data['status']);
    }


    public function readInLiveMode($request) {
        
        $purchase = $request->purchase;
        $userData = Auth::user();
        $postedData = $request->all();
        unset($postedData['purchase']);
        unset($postedData['wpmode']);

        // Check Already Records Existence For Articles
        $article = ArticleModel::where(['cpd_id' => $request->cpd_id, 'post_id' => $request->post_id])->first();
        
        // Check Already Records Existence For User
        $articleTransaction = ArticleTransactionModel::where(['user_id' => $userData->id, 'cpd_id' => $request->cpd_id, 'post_id' => $request->post_id])->first();
        //pr($articleTransaction);die;

        if(!empty($articleTransaction) && !empty($articleTransaction->purchased_at)) {
            // if we get any record in transaction table then return is_purchased = true

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(['is_purchased' => true, 'balance_coins' => $userData->balance_coins], $this->successText, 'You have already bought this article.');

        } else {

            // PLAY WITH ARTICLE TABLE
            if(empty($article)) {
                // Insert no record in article then insert new record
                $postedData['created_at'] = date('Y-m-d H:i:s');
                $postedData['visitors'] = 1;
                $postedData['purchased'] = 0;
                $aId = ArticleModel::insertGetId($postedData);
                $article = (object) $postedData;
                $article->article_id = $aId;

                unset($postedData['visitors']);
                unset($postedData['purchased']);

                // TRANSACTIONS
                $postedData['user_id'] = $userData->id;
                $postedData['coins_used'] = $request->fixed_coins;
                $postedData['created_at'] = date('Y-m-d H:i:s');
                unset($postedData['fixed_coins']);
                $transactionId = ArticleTransactionModel::insertGetId($postedData);
            }
            else if(empty($articleTransaction)) {
                // If there is no record related to this user in transaction table then Update existing visitors record
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                $updateData['visitors'] = ($article->visitors + 1);
                ArticleModel::where(['article_id' => $article->article_id])->update($updateData);

                // TRANSACTIONS
                $postedData['user_id'] = $userData->id;
                $postedData['coins_used'] = $request->fixed_coins;
                $postedData['created_at'] = date('Y-m-d H:i:s');
                unset($postedData['fixed_coins']);
                $transactionId = ArticleTransactionModel::insertGetId($postedData);
            }


            if(!empty($purchase)) {
                // In case of buy

                if($userData->balance_coins < $request->fixed_coins) {
                    $this->responseStatusCode = $this->statusCodes->unprocessable;
                    $response = api_create_response(2, $this->failureText, 'You don\'t have sufficient coins, Please buy it.');
                } else {
                    $updateData['updated_at'] = date('Y-m-d H:i:s');
                    $updateData['purchased'] = ($article->purchased + 1);
                    ArticleModel::where(['article_id' => $article->article_id])->update($updateData);
                    
                    $remainingCoinBalance = ($userData->balance_coins - $request->fixed_coins);
                    $purchaseData = array(
                        'coins_used' => $request->fixed_coins,
                        'coins_balance' => $remainingCoinBalance,
                        'purchased_at' => date('Y-m-d H:i:s')
                    );
                    ArticleTransactionModel::where(['user_id' => $userData->id, 'cpd_id' => $request->cpd_id, 'post_id' => $request->post_id])->update($purchaseData);


                    // Entry in coin transaction for debit records
                    $transaction = [
                        'user_id' => $userData->id,
                        'package_article_id' => $article->article_id,
                        'description' => 'Deducted for reading articles',
                        'amount' => '0.00',
                        'coins' => $request->fixed_coins,
                        'balance_coins' => $remainingCoinBalance,
                        'transaction_mode' => 'DR',
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    CoinModel::insertGetId($transaction);

                    
                    // Update coin balance in user table
                    $userData->balance_coins = $remainingCoinBalance;
                    $userData->save();

                    $this->responseStatusCode = $this->statusCodes->success;
                    $response = api_create_response(['is_purchased' => true, 'balance_coins' => $remainingCoinBalance], $this->successText, 'You have bought article successfully.');
                }
            } else {
                // In case of read

                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response(['is_purchased' => false, 'balance_coins' => $userData->balance_coins], $this->successText, 'User details.');

            }
        }
        
        return [
            'response' => $response,
            'status' => $this->responseStatusCode,
        ];
    }


    public function readInTestMode($request) {
        
        $purchase = $request->purchase;
        $userData = Auth::user();
        $postedData = $request->all();
        unset($postedData['purchase']);
        unset($postedData['wpmode']);

        // Check Already Records Existence For Articles
        $article = TestArticleModel::where(['cpd_id' => $request->cpd_id, 'post_id' => $request->post_id])->first();
        
        // Check Already Records Existence For User
        $articleTransaction = TestArticleTransactionModel::where(['user_id' => $userData->id, 'cpd_id' => $request->cpd_id, 'post_id' => $request->post_id])->first();
        //pr($articleTransaction);die;

        if(!empty($articleTransaction) && !empty($articleTransaction->purchased_at)) {
            // if we get any record in transaction table then return is_purchased = true

            $this->responseStatusCode = $this->statusCodes->success;
            $response = api_create_response(['is_purchased' => true, 'balance_coins' => $userData->test_coins], $this->successText, 'You have already bought this article.');

        } else {

            // PLAY WITH ARTICLE TABLE
            if(empty($article)) {
                // Insert no record in article then insert new record
                $postedData['created_at'] = date('Y-m-d H:i:s');
                $postedData['visitors'] = 1;
                $postedData['purchased'] = 0;
                $aId = TestArticleModel::insertGetId($postedData);
                $article = (object) $postedData;
                $article->article_id = $aId;
                unset($postedData['visitors']);
                unset($postedData['purchased']);

                // TRANSACTIONS
                $postedData['user_id'] = $userData->id;
                $postedData['coins_used'] = $request->fixed_coins;
                $postedData['created_at'] = date('Y-m-d H:i:s');
                unset($postedData['fixed_coins']);
                $transactionId = TestArticleTransactionModel::insertGetId($postedData);
            }
            else if(empty($articleTransaction)) {
                // If there is no record related to this user in transaction table then Update existing visitors record
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                $updateData['visitors'] = ($article->visitors + 1);
                TestArticleModel::where(['article_id' => $article->article_id])->update($updateData);

                // TRANSACTIONS
                $postedData['user_id'] = $userData->id;
                $postedData['coins_used'] = $request->fixed_coins;
                $postedData['created_at'] = date('Y-m-d H:i:s');
                unset($postedData['fixed_coins']);
                $transactionId = TestArticleTransactionModel::insertGetId($postedData);
            }

            if(!empty($purchase)) {
                // In case of buy

                if($userData->test_coins < $request->fixed_coins) {
                    $this->responseStatusCode = $this->statusCodes->unprocessable;
                    $response = api_create_response(2, $this->failureText, 'You don\'t have sufficient coins, Please buy it.');
                } else {
                    $updateData['updated_at'] = date('Y-m-d H:i:s');
                    $updateData['purchased'] = ($article->purchased + 1);
                    TestArticleModel::where(['article_id' => $article->article_id])->update($updateData);
                    
                    $remainingCoinBalance = ($userData->test_coins - $request->fixed_coins);
                    $purchaseData = array(
                        'coins_used' => $request->fixed_coins,
                        'coins_balance' => $remainingCoinBalance,
                        'purchased_at' => date('Y-m-d H:i:s')
                    );
                    TestArticleTransactionModel::where(['user_id' => $userData->id, 'cpd_id' => $request->cpd_id, 'post_id' => $request->post_id])->update($purchaseData);


                    // Entry in coin transaction for debit records
                    $transaction = [
                        'user_id' => $userData->id,
                        'package_article_id' => $article->article_id,
                        'description' => 'Deducted for reading articles',
                        'amount' => '0.00',
                        'coins' => $request->fixed_coins,
                        'balance_coins' => $remainingCoinBalance,
                        'transaction_mode' => 'DR',
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    TestCoinModel::insertGetId($transaction);

                    
                    // Update coin balance in user table
                    $userData->test_coins = $remainingCoinBalance;
                    $userData->save();

                    $this->responseStatusCode = $this->statusCodes->success;
                    $response = api_create_response(['is_purchased' => true, 'balance_coins' => $remainingCoinBalance], $this->successText, 'You have bought article successfully.');
                }
            } else {
                // In case of read

                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response(['is_purchased' => false, 'balance_coins' => $userData->test_coins], $this->successText, 'User details.');

            }
        }
        
        return [
            'response' => $response,
            'status' => $this->responseStatusCode,
        ];
    }







    public function show($id) {

        $data = ApikeyModel::where('company_id', $id)->first();

        if(!empty($data['cpd_id'])) {

            $records = ArticleTransactionModel::where('cpd_id', $data['cpd_id'])->first();
        
            if(!empty($records)){
             
                $this->responseStatusCode = $this->statusCodes->success;
                $response = api_create_response($records, $this->successText, 'Articles Details.');

            } else {
                $this->responseStatusCode = $this->statusCodes->bad_request;
                $response = api_create_response(2, $this->failureText, 'No Articles Found.');
            }
            
        } else {
            $this->responseStatusCode = $this->statusCodes->bad_request;
            $response = api_create_response(2, $this->failureText, 'No Company Id Found.');
        }

        return response()->json($response, $this->responseStatusCode);
    }
}