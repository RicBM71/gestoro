<?php

namespace App\Http\Controllers\Admin;

use App\Ipuser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class IpUsersController extends Controller
{


     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'ip'        => ['required', 'ip'],
            'user_id'   => ['required', 'integer'],
        ]);

        $data['username'] = $request->user()->username;


        $reg = Ipuser::create($data);

        if (request()->wantsJson())
            return ['ips'=> Ipuser::selIps($data['user_id'])];


    }

      /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ipuser $ipuser)
    {

        $ipuser->delete();

        if (request()->wantsJson()){
            return ['ips'=> Ipuser::selIps($ipuser->user_id)];
        }
    }


    //  /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($user_id)
    // {

    //     $ips = Ipuser::getIpuser($user_id);

    // }

}
