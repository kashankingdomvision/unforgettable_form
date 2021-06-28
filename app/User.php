<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'supervisor_id', 'currency_id', 'brand_id', 'holidaytype_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function getSaleAgent()
    {
        return $this->hasMany(User::class, 'supervisor_id', 'id');
    }
    
    function getRole() {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
    
    function getSupervisor() {
        return $this->hasOne(User::class, 'id', 'supervisor_id');
    }

    function getCurrency() {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }
    
    function getBrand() {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }
}
