<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany('App\Category', 'supplier_categories');
    }

    public function products(){
        return $this->belongsToMany('App\Product', 'supplier_products');
    }
}
