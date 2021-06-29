<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierCategory extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'supplier_id', 'category_id'
     ];
}
