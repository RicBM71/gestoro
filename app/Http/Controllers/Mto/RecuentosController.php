<?php

namespace App\Http\Controllers\Mto;

use App\Recuento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecuentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Recuento::with(['producto','rfid','estado'])->get();

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recuento $recuento)
    {
        $data = $request->validate([
            'rfid_id' => ['required', 'integer'],
        ]);

        if ($data['rfid_id'] == 3)
            $data['rfid_id'] = 13;
        else
            $data['rfid_id'] = 3;

        $recuento->update($data);

        $recuento->load(['producto','rfid','estado']);

        if (request()->wantsJson())
            return ['recuento'=>$recuento, 'message' => 'EL registro ha sido modificado'];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
