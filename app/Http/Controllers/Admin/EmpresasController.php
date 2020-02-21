<?php

namespace App\Http\Controllers\Admin;

use App\Almacen;
use App\Empresa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmpresas;
use Illuminate\Support\Facades\Storage;

class EmpresasController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (esRoot())
            $data = Empresa::withTrashed()->get();
        else
            $data = Empresa::whereIn('empresas.id', session('empresas_usuario'))->get();

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
        $this->authorize('create', new Empresa);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->authorize('create', new Empresa);

        $data = $request->validate([
            'nombre' => ['required','string'],
            'razon' => ['required', 'string'],
            'titulo' => ['required','string'],
        ]);

        $data['username'] = $request->user()->username;
        $data['flags']='1'.str_repeat('0',19);

        $reg = Empresa::create($data);

        $request->user()->empresas()->attach($reg->id);

        if (request()->wantsJson())
            return ['empresa'=>$reg, 'message' => 'EL registro ha sido creado'];


    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Empresa $empresa )
    {
        //$empresa = Empresa::findOrFail($id);
        // if (esRoot()){
        //     $empresa = Empresa::findOrFail($id);
        // }else{
        //     $empresa = Empresa::where('id',$id)
        //                       ->whereIn('empresas.id', session('empresas_usuario'))->firstOrFail();
        // }

        $this->authorize('update', $empresa);

        if (request()->wantsJson())
            return [
                'empresa' =>$empresa,
                'almacenes'=>Almacen::selAlmacenes(),
                'empresas'=>Empresa::selAllEmpresas()
            ];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreEmpresas $request, Empresa $empresa)
    {

        $this->authorize('update', $empresa);

        $data = $request->validated();

        $data['username'] = $request->user()->username;

        $empresa->update($data);

        if (request()->wantsJson())
            return ['empresa'=>$empresa, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //TODO: Comprobar políticas y permisos para poder borrar, un admin no debería.

        if (esRoot()){
            $empresa = Empresa::withTrashed()->find($id);
        }else{
            $empresa = Empresa::findOrFail($id);
        }

        $this->authorize('delete', $empresa);

        if ($empresa->trashed()) {
            $empresa->restore();
        }else
            $empresa->delete();


        if (esRoot())
            $data = Empresa::withTrashed()->get();
        else
            $data = Empresa::all();

        if (request()->wantsJson()){
            return response()->json($data);
        }
    }

    public function logo(Request $request, Empresa $empresa){
        $this->validate(request(),[
    		'logo' => 'required|image|max:256'	//jpeg png, gif, svg
        ]);

        $img = request()->file('logo')->store('logos','public');
        //$img = request()->file('logo')->storeAs('/public/logos','logokk');

    	$fotoUrl = Storage::url($img);

    	// 	//insert en la tabla photos
    	$empresa->update([
    	 	'img_logo'	=> $fotoUrl,
        ]);

        return ['empresa'=>$empresa];


    }

    public function deletelogo(Request $request, Empresa $empresa){

        $fotoPath = str_replace('storage', 'public', $empresa->img_logo);

        Storage::delete($fotoPath);

        $empresa->update([
            'img_logo'	=> null,
       ]);



       return ['empresa'=>$empresa];

    }

    public function fondo(Empresa $empresa){
        $this->validate(request(),[
    		'logo' => 'required|image|max:256'	//jpeg png, gif, svg
        ]);

        $img = request()->file('logo')->store('logos','public');
        //$img = request()->file('logo')->storeAs('/public/logos','logokk');

    	$fotoUrl = Storage::url($img);

    	// 	//insert en la tabla photos
    	$empresa->update([
    	 	'img_fondo'	=> $fotoUrl,
        ]);

        return ['empresa'=>$empresa];


    }

    public function deletefondo(Empresa $empresa){

        $fotoPath = str_replace('storage', 'public', $empresa->img_fondo);

        Storage::delete($fotoPath);

        $empresa->update([
            'img_fondo'	=> null,
       ]);



       return ['empresa'=>$empresa];

    }
}
