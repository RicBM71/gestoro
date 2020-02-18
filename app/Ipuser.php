<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ipuser extends Model
{
    protected $fillable = [
        'user_id','ip','username'
    ];

    public static function selIps($user_id)
    {

        return Ipuser::select('id AS value', 'ip AS text')
                        ->where('user_id', $user_id)
                        ->get();

    }

    public static function getIpUser($user_id){

        $data = Ipuser::select('ip')->where('user_id', $user_id)->get();

        //return false;

        return $data->pluck('ip')->toArray();


    }

}
