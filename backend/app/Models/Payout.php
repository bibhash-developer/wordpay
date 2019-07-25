<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $table = 'article_payout';
    protected $id = 'payout_id';
    protected $primaryKey = 'article_payout';
}