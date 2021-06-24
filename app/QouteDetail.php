<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QouteDetail extends Model
{
    protected $table = 'qoute_details';

    protected $guarded = [];

    public function getCategory()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::class,'id','supplier');
    }

    // function getSupplier() {

        //  $this->category_id;

        // return $supplier_category = supplier_category::where('category_id', $this->category_id)
        // ->select('suppliers.id', 'suppliers.name')
        // ->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_categories.supplier_id')
        // ->get();
    

        // return $this->belongsToMany(Supplier::class, 'supplier_categories','supplier_id');
    // }
}
