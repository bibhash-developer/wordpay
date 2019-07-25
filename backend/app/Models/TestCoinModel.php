<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCoinModel extends Model
{
    protected $table = 'test_user_coin_transactions';
    
    protected $primaryKey = 'id';

    protected $hidden = ['created_at', 'updated_at'];
}
