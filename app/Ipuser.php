<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ipuser extends Model
{
    protected $fillable = [
        'ip','username'
    ];

    public static function getIpUser($user_id){

        $data = Ipuser::select('ip')->where('user_id', $user_id)->get();

        //return false;

        return $data->pluck('ip')->toArray();


    }

}
