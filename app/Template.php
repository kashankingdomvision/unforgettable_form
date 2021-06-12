<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'season_id'
    ];
    
    
    public function getUser()
    {
        return $this->hasOne(User::class, 'id','user_id');
    }
    
    public function getSeason()
    {
        return $this->hasOne(Season::class, 'id', 'season_id');
    }
    
    public function getFormatedStatusAttribute()
    {
        $status = $this->status;
        switch ($status) {
            case 'active':
                return '<span class="badge badge-pill badge-success">active</span>';
                break;
            case 'inactive':
                return '<span class="badge badge-pill badge-dark ">in-active</span>';
            break;
        }
        
        return $status;
    }
    
    public function getTemplateDetails()
    {
        return $this->hasMany(TemplateDetail::class, 'template_id', 'id');
    }
}
