<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qoute extends Model
{
    protected $table = 'qoutes';
    protected $guarded = [];


    public function season(){
    	return $this->belongsTo('App\season');
    }

    public function quotes()
    {
        return $this->belongsToMany('App\QouteDetail','qoute_details','qoute_id');
    }


    // public function categories()
    // {
    // 	return $this->belongsToMany('App\Model\user\category','category_posts')->withTimestamps();;
    // }
}
