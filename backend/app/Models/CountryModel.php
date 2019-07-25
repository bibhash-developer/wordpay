<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryModel extends Model
{
    /*protected $table       = 'country';

    protected $fillable    = ['country_id', 'country_name', 'vat', 'currancy', 'currancy_symbol'];
    public    $timestamps  = true;*/

    protected $table = 'country';
    protected $primaryKey = 'country_id';

    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = false;

    

    public function REL_Country() {
        return $this->hasMany('App\Models\PackageModel', 'country_id', 'country_id');
    }

}