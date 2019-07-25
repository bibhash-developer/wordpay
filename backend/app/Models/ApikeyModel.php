<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApikeyModel extends Model
{
    protected $table    = 'api_keys';
    protected $primaryKey = 'cpd_id';

    protected $fillable = ['cpd_id', 'company_id', 'name', 'domain', 'key', 'secret_key', 'apply_vat', 'status',
                           'created_at'];
    protected $hidden = ['updated_at', 'deleted_at'];
    public $timestamps  = true;
    
    static public function insertApikey($postedData)
     {
        $response = New ApikeyModel($postedData);
        
        if($response->save())
        {
            $lastInsertedID = $response->id;
            return $lastInsertedID;
        }
     }

}
