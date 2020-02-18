<?php

namespace App\Http\Controllers\Admin;

use App\Ipuser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IpUsersController extends Controller
{

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {

        $ips = Ipuser::getIpuser($user_id);

    }

}
