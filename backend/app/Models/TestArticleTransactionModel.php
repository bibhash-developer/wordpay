<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestArticleTransactionModel extends Model
{
    protected $table = 'test_article_transactions';
    protected $id = 'article_transaction_id';

    protected $hidden = ['updated_at', 'deleted_at'];
}