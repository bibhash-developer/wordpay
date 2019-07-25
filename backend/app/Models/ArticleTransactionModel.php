<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleTransactionModel extends Model
{
    protected $table = 'article_transactions';
    protected $id = 'article_transaction_id';

    protected $hidden = ['updated_at', 'deleted_at'];
}