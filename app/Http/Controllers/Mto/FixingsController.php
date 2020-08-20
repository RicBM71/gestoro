<?php

namespace App\Http\Controllers\Mto;

use App\Fixing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class FixingsController extends Controller
{

    public function index()
    {

        $data = Fixing::get()->take(100);

        if (request()->wantsJson())
            return $data;
    }



    public function store(Request $request)
    {

        $data = $request->validate([
            'fecha' => ['required', 'date', Rule::unique('fixings')->where(function ($query) {
                return $query->where('clase_id', 1);
            })],
            'importe' => ['required', 'numeric'],
        ]);

        $data['clase_id'] = 1;
        $data['username'] = $request->user()->username;

        $reg = Fixing::create($data);

        if (request()->wantsJson())
            return ['registro'=>$reg, 'message' => 'EL registro ha sido creado'];
    }


    public function show($id)
    {
        return;
    }


    public function edit(Fixing $fixing)
    {

        if (request()->wantsJson())
            return [
                'registro' =>$fixing
            ];
    }

    public function update(Request $request, Fixing $fixing)
    {

        $data = $request->validate([
            'fecha' => ['required', 'date', Rule::unique('fixings')->ignore($fixing->id)->where(function ($query) {
                return $query->where('clase_id', 1);
            })],
            'importe' => ['required', 'numeric'],
        ]);

        $data['username'] = $request->user()->username;

        $fixing->update($data);

        if (request()->wantsJson())
            return ['registro'=>$fixing, 'message' => 'EL registro ha sido modificado'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fixing $fixing)
    {

        $fixing->delete();

        if (request()->wantsJson()){
            return response()->json(Fixing::all());
        }
    }
}

