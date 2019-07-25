<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankModel extends Model
{
      use SoftDeletes;
      protected $table    = 'banks';
      protected $primaryKey = 'bank_id';
      protected $fillable = ['company_id', 'user_id', 'bank_name', 'iban_number', 'swift_code', 'is_default'];
      public $timestamps  = true;
    
    static public function insertBankId($postedData)
     {
        $response = New BankModel($postedData);
        
        if($response->save())
        {
            $lastInsertedID = $response->bank_id;
            return $lastInsertedID;
        }
     }
}
