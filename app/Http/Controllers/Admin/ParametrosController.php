<?php

namespace App\Http\Controllers\Admin;

use App\Empresa;
use App\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ParametrosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson())
            return Parametro::findOrfail(1);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parametro $parametro)
    {

        $data = $request->validate([

            'lim_efe'        => ['required','numeric'],
            'lim_efe_nores'  => ['required', 'numeric'],
            'pie_rebu1'      => ['nullable','string'],
            'retencion'      => ['required','numeric'],
            'aislar_empresas'=> ['required','boolean'],
            'carpeta_docs'   => ['required','string'],
            'email_productos_online' => ['nullable','email']

        ]);

        $data['username'] = $request->user()->username;

        $parametro->update($data);

        if (request()->wantsJson())
            return ['parametro'=>$parametro, 'message' => 'EL registro ha sido modificado'];

    }

    public function main(Request $request){

        $this->validate(request(),[
    		'imagen' => 'required|image|max:1024'	//jpeg png, gif, svg
        ]);

        $parametro = Parametro::findOrFail(1);

        $img = request()->file('imagen')->store('assets','public');

        $fotoUrl = Storage::url($img);




    	// 	//insert en la tabla photos
    	$parametro->update([
    	 	'img1'	=> $fotoUrl,
        ]);

        return ['parametro'=>$parametro];


    }

    public function section(Request $request){

        $this->validate(request(),[
    		'imagen' => 'required|image|max:1024'	//jpeg png, gif, svg
        ]);

        $parametro = Parametro::findOrFail(1);

        $img = request()->file('imagen')->store('assets','public');

        $fotoUrl = Storage::url($img);



    	// 	//insert en la tabla photos
    	$parametro->update([
    	 	'img2'	=> $fotoUrl,
        ]);

        return ['parametro'=>$parametro];


    }


    public function deletemain(Request $request){

        $parametro = Parametro::findOrFail(1);

        $fotoPath = str_replace('storage', 'public', $parametro->img1);

        Storage::delete($fotoPath);

        $parametro->update([
            'img1'	=> null,
       ]);



       return ['parametro'=>$parametro];

    }

    public function deletesection(Request $request){

        $parametro = Parametro::findOrFail(1);

        $fotoPath = str_replace('storage', 'public', $parametro->img2);

        Storage::delete($fotoPath);

        $parametro->update([
            'img2'	=> null,
       ]);



       return ['parametro'=>$parametro];

    }



}
