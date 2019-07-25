<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinModel extends Model
{
    protected $table = 'user_coin_transactions';
    
    protected $primaryKey = 'id';

    protected $hidden = ['created_at', 'updated_at'];
}
