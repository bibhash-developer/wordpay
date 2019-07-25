<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestArticleModel extends Model
{
    protected $table = 'test_articles';
    
    protected $primaryKey = 'article_id';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
