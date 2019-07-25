<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageModel extends Model
{
    use SoftDeletes;

    protected $table = 'packages';

    protected $primaryKey = 'package_id';

    protected $hidden = ['created_at', 'updated_at', 'published_at', 'deleted_at'];

    public function REL_Country() {
        return $this->belongsTo('App\Models\CountryModel', 'country_id', 'country_id');
    }
}