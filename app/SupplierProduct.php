<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    public $timestamps = false;
    protected $fillable = [
       'supplier_id', 'product_id'
    ];
}
