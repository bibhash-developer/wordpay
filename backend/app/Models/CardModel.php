<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CardModel extends Model
{
    use SoftDeletes;

    protected $table    = 'cards';
    protected $primaryKey = 'card_id';

    protected $hidden   = ['updated_at', 'deleted_at'];
    protected $fillable = ['user_id', 'card_type', 'number', 'expired_on', 'cvc', 'first_name', 'last_name', 
                           'address', 'city', 'state', 'postal_code', 'country', 'phone', 'is_default'];
    public $timestamps  = true;
    
    static public function insertCardId($postedData)
     {
        $parent = New CardModel($postedData);
        
        if($parent->save())
        {
            $lastInsertedID = $parent->card_id;
            return $lastInsertedID;
        }
     }

}

