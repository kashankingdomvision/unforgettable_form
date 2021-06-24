<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QouteDetailLog extends Model
{
    public function getCategory()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::class,'id','supplier');
    }
}
