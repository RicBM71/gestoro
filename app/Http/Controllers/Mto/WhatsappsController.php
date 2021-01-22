<?php

namespace App\Http\Controllers\Mto;

use App\Whatsapp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WhatsappsController extends Controller
{
    public function index()
    {

        $data = Whatsapp::all();

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

        if (request()->wantsJson())
        return [

        ];

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Whatsapp::class);

        $data = $request->validate([
            'texto' => ['required', 'string'],
        ]);

        $data['empresa_id'] = session('empresa_id');
        $data['username'] = session('username');

        $reg = Whatsapp::create($data);

        if (request()->wantsJson())
            return ['whatsapp'=>$reg, 'message' => 'EL registro ha sido creado'];
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
    public function edit(Whatsapp $whatsapp)
    {
        $this->authorize('update', $whatsapp);

        if (request()->wantsJson())
            return [
                'whatsapp' =>$whatsapp
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Whatsapp $whatsapp)
    {
        $this->authorize('update', $whatsapp);

        $data = $request->validate([
            'texto' => ['required', 'string'],
        ]);

        $data['username'] = $request->user()->username;


        $whatsapp->update($data);

        if (request()->wantsJson())
            return ['whatsapp'=>$whatsapp, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Whatsapp $whatsapp)
    {
        $this->authorize('delete', $whatsapp);

        $whatsapp->delete();

        if (request()->wantsJson()){
            return response()->json(Whatsapp::get());
        }
    }

}
