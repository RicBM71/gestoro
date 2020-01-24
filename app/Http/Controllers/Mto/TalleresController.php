<?php

namespace App\Http\Controllers\Mto;

use App\Banco;
use App\Fpago;
use App\Taller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTalleresRequest;

class TalleresController extends Controller
{
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->wantsJson())
            if (esRoot())
                return Taller::withTrashed()->get();
            else
                return Taller::all();

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
                'fpagos'=> Fpago::selFPagos(),
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
        $this->authorize('create', new Taller);

        $data = $request->validate([
            'razon'   => ['required', 'string', 'max:70'],
            'tipodoc' => ['string'],
            'dni'     => ['required', Rule::unique('talleres')],
        ]);

        $data['username'] = $request->user()->username;
        $data['empresa_id'] = session()->get('empresa')->comun_empresa_id;

        $reg = Taller::create($data);

        if (request()->wantsJson())
            return ['taller'=>$reg, 'message' => 'EL registro ha sido creado'];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Taller $tallere)
    {


        if (request()->wantsJson())
            return [
                'taller' =>$tallere,
                'fpagos'=> Fpago::selFPagos(),
            ];

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Taller $tallere)
    {
        $this->authorize('update', $tallere);

        if (request()->wantsJson())
            return [
                'taller' =>$tallere,
                'fpagos'=> Fpago::selFPagos(),
                'bancos'=> Banco::selEntidadBic(),
            ];

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTalleresRequest $request, Taller $tallere)
    {
        $this->authorize('update', $tallere);

        $data = $request->validated();

        $data['username'] = $request->user()->username;

        $tallere->update($data);

        if (request()->wantsJson())
            return [
                'taller'=>$tallere,
                'message' => 'EL Taller ha sido modificado'
                ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        if (esRoot()){
            $tallere = Taller::withTrashed()->findOrFail($id);
        }else{
            $tallere = Taller::findOrFail($id);
        }

        $this->authorize('delete', $tallere);

        if ($tallere->trashed()){
            $tallere->restore();
            $msg="Registro restaurado!";
        }
        else{
            if (esRoot() && hasHardDel()){
                $msg="Registro eliminado permanentemente!";
                $tallere->forceDelete();
            }
            else{
                $msg="Registro eliminado!";
                $tallere->delete();
            }
        }

        if (request()->wantsJson()){
            if (esRoot())
                return [
                    'taller' => Taller::withTrashed()->get(),
                    'msg' => $msg
                ];
            else
                return [
                    'taller' => Taller::all(),
                    'msg' => $msg
            ];

        }
    }
}
