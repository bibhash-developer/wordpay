<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleModel extends Model
{
    protected $table = 'articles';
    
    protected $primaryKey = 'article_id';

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
