<?php

namespace App\Http\Controllers\Admin;

use App\Socialmedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SocialmediasController extends Controller
{
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = Socialmedia::all();

        if (request()->wantsJson())
            return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$this->authorize('create', new Socialmedia);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $data = $request->validate([
            'texto' => ['required', 'string', 'max:50'],
        ]);

        $data['empresa_id'] = session('empresa_id');
        $data['username'] = $request->user()->username;

        $reg = Socialmedia::create($data);

        if (request()->wantsJson())
            return ['registro'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id (id del archivo)
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Socialmedia $social)
    {

        if (request()->wantsJson())
            return [
                'registro' =>$social
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Socialmedia $social)
    {

        $data = $request->validate([
            'texto' => ['required', 'string'],
            'logo'   => ['string'],
        ]);

        $data['username'] = $request->user()->username;


        $social->update($data);

        if (request()->wantsJson())
            return ['registro'=>$social, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Socialmedia $social)
    {

        $social->delete();

        if (request()->wantsJson()){
            return response()->json(Socialmedia::all());
        }
    }

    public function logo(Request $request, Socialmedia $social){

        $this->validate(request(),[
    		'logo' => 'required|image|max:256'	//jpeg png, gif, svg
        ]);

        $img = request()->file('logo')->store('logos','public');
        //$img = request()->file('logo')->storeAs('/public/logos','logokk');

    	$fotoUrl = Storage::url($img);

    	// 	//insert en la tabla photos
    	$social->update([
    	 	'logo'	=> $fotoUrl,
        ]);

        return ['social'=>$social];


    }

    public function deletelogo(Request $request, Socialmedia $social){

        $fotoPath = str_replace('storage', 'public', $social->img_logo);

        Storage::delete($fotoPath);

        $social->update([
            'logo'	=> null,
       ]);



       return ['social'=>$social];

    }
}
