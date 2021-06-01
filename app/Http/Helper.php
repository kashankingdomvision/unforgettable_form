<?php
namespace App\Http;
use App\User;

class Helper
{
    public static function get_supervisor($id){

        $user = User::where('id',$id)->first();
        return $user;
    }
}