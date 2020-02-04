<?php

namespace App\Http\Controllers\Rfid;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportRfidController extends Controller
{
    public function index(){

        // if (!auth()->user()->hasRole('Gestor')){
        //     return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Gestor');
        // }

    }

    public function upload(Request $request){

        $this->validate(request(),[
    		'file' => 'required|*|max:256'
        ]);

        $file = request()->file('file'); //->store('logos','public');

        \Log::info($file);

        return "llega";

    }
}
