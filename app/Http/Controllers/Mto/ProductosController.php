<?php

namespace App\Http\Controllers\Mto;

use App\Iva;
use App\Clase;
use App\Estado;
use App\Almacen;
use App\Cliente;
use App\Empresa;
use App\Quilate;
use App\Etiqueta;
use App\Garantia;
use App\Producto;
use Carbon\Carbon;
use App\Traits\SessionTrait;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\InventarioExport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProducto;
use App\Rules\MaxDiasRangoFechaRule;
use App\Scopes\EmpresaProductoScope;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\UpdateProducto;

class ProductosController extends Controller
{

    use SessionTrait;

        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (request()->session()->has('filtro_pro')){
            $data = $this->miFiltro();
        }else{
            // if (auth()->user()->hasRole('Admin'))
            //     $data = Producto::withTrashed()->with(['clase','estado','destino'])->orderBy('id','desc')
            //                             ->where('updated_at','>=',Carbon::now()->subDays(30))
            //                             ->get()
            //                             ->take(50);
            // else
                $data = Producto::with(['clase','estado','destino'])->orderBy('id','desc')
                        ->where('updated_at','>=',Carbon::now()->subDays(30))
                        ->get()
                        ->take(50);

        }

        if (request()->wantsJson())
            return $data;
    }

    public function filtrar(Request $request)
    {

        $data = $request->validate([
            'fecha_d'       =>['date','nullable'],
            'fecha_h'       =>['date','nullable'],
            'clase_id'      =>['integer','nullable'],
            'estado_id'     =>['integer','nullable'],
            'referencia'    =>['string','nullable'],
            'ref_pol'       =>['string','nullable'],
            'precio'        =>['string','nullable'],
            'notas'         =>['string','nullable'],
            'quilates'      =>['integer','nullable'],
            'online'        =>['boolean'],
            'alta'          =>['boolean'],
            'cliente_id'    => ['nullable','integer'],
            'tipo_fecha'    =>['string','required'],
            'fecha_d'       =>['nullable','date',new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'       =>['nullable','date',new MaxDiasRangoFechaRule($request->fecha_d, $request->fecha_h)],
            'destino_empresa_id'=>['nullable','integer'],
            'sinscope'       =>['boolean'],
            'interno'        =>['string','required'],
        ]);

        session(['filtro_pro' => $data]);

        return $this->miFiltro();

    }

    private function miFiltro(){

        $data = request()->session()->get('filtro_pro');

        if (esAdmin() && session('parametros')->aislar_empresas == false && $data['sinscope']==true){
            if ($data['alta'] == false)
                $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->withTrashed()->with(['clase','estado','destino'])
                            ->referencia($data['referencia'])
                            ->fecha($data['fecha_d'],$data['fecha_h'],$data['tipo_fecha'])
                            ->clase($data['clase_id'])
                            ->estado($data['estado_id'])
                            ->destino($data['destino_empresa_id'])
                            ->notasNombre($data['notas'])
                            ->refPol($data['ref_pol'])
                            ->precioPeso($data['precio'])
                            ->quilates($data['quilates'])
                            ->online($data['online'])
                            ->internos($data['interno'])
                            ->asociado($data['cliente_id'])
                            ->orderBy('id','desc')
                            ->get()
                            ->take(500);
            else{

                $data = Producto::withOutGlobalScope(EmpresaProductoScope::class)->with(['clase','estado','destino'])
                        ->referencia($data['referencia'])
                        ->fecha($data['fecha_d'], $data['fecha_h'],$data['tipo_fecha'])
                        ->clase($data['clase_id'])
                        ->estado($data['estado_id'])
                        ->destino($data['destino_empresa_id'])
                        ->notasNombre($data['notas'])
                        ->refPol($data['ref_pol'])
                        ->precioPeso($data['precio'])
                        ->quilates($data['quilates'])
                        ->online($data['online'])
                        ->internos($data['interno'])
                        ->asociado($data['cliente_id'])
                        ->orderBy('id','desc')
                        ->get()
                        ->take(500);
            }

        }
        else{
            if ($data['alta'] == false)
                $data = Producto::withTrashed()->with(['clase','estado','destino'])
                            ->referencia($data['referencia'])
                            ->fecha($data['fecha_d'],$data['fecha_h'],$data['tipo_fecha'])
                            ->clase($data['clase_id'])
                            ->estado($data['estado_id'])
                            ->destino($data['destino_empresa_id'])
                            ->notasNombre($data['notas'])
                            ->refPol($data['ref_pol'])
                            ->precioPeso($data['precio'])
                            ->quilates($data['quilates'])
                            ->online($data['online'])
                            ->internos($data['interno'])
                            ->asociado($data['cliente_id'])
                            ->orderBy('id','desc')
                            ->get()
                            ->take(500);
            else{

                $data = Producto::with(['clase','estado','destino'])
                        ->referencia($data['referencia'])
                        ->fecha($data['fecha_d'], $data['fecha_h'],$data['tipo_fecha'])
                        ->clase($data['clase_id'])
                        ->estado($data['estado_id'])
                        ->destino($data['destino_empresa_id'])
                        ->notasNombre($data['notas'])
                        ->refPol($data['ref_pol'])
                        ->precioPeso($data['precio'])
                        ->quilates($data['quilates'])
                        ->online($data['online'])
                        ->internos($data['interno'])
                        ->asociado($data['cliente_id'])
                        ->orderBy('id','desc')
                        ->get()
                        ->take(500);
            }
        }
        return $data;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', new Producto);

        if (request()->wantsJson())
            return [
                'clases'    => Clase::selGrupoClase(),
                'estados'   => Estado::selEstados(),
                'quilates'  => Quilate::selQuilates()
            ];

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProducto $request)
    {
        $this->authorize('create', new Producto);

        $data = $request->validated();

        $data['empresa_id'] = session()->get('empresa')->id;
        $data['destino_empresa_id'] = session()->get('empresa')->id;
        $data['almacen_id'] = session()->get('empresa')->almacen_id;

        //$data['etiqueta_id']  = $data['clase_id'] == 3 ? 3 : 1;

        if ($data['precio_venta'] == 0)
            $data['precio_venta'] = $data['precio_coste'] + round($data['precio_coste'] * 30 / 100, 0);
        $data['username'] = $request->user()->username;


        $reg = Producto::create($data);

        //$data['referencia']=session('empresa')->sigla.'-'.$reg->id;
        $data['referencia']=session('empresa')->sigla.$reg->id;

        $reg->update($data);

        $reg->load('clase');

        if (request()->wantsJson())
            return ['producto'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (esAdmin()){
            $producto = Producto::withTrashed()->with(['estado','clase','iva'])->findOrFail($id);
        }else{
            $producto = Producto::with(['estado','clase','iva'])->findOrFail($id);
        }


        if (request()->wantsJson())
            return [
                'producto' =>$producto,
                'empresas' => Empresa::selEmpresas()->Venta()->get(),
            ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if (esAdmin()){
            $producto = Producto::withTrashed()->findOrFail($id);
        }else{
            $producto = Producto::findOrFail($id);
        }

        $parametros = false;

        // con esto cambiamos de empresa si la empresa no coincide
        // $producto = Producto::withOutGlobalScope(EmpresaProductoScope::class)->withTrashed()->findOrFail($id);
        // $collection = session('empresas_usuario');
        // if ($collection->search($producto->empresa_id, true)===false && $collection->search($producto->destino_empresa_id, true)===false){
        //     return abort(404, "No se ha encontrado el registro");
        // }

        // if ($producto->empresa_id != session('empresa_id')){
        //     $parametros = $this->loadSession($producto->empresa_id);
        // }else{
        //     $parametros = false;
        // }

        $this->authorize('update', $producto);

        if (request()->wantsJson())
            return [
                'parametros'=> $parametros,
                'producto' => $producto->load('clase'),
                'empresas' => Empresa::selEmpresas()->Venta()->get(),
                'clases'   => Clase::selGrupoClase(),
                'estados'  => Estado::selEstados(),
                'ivas'     => Iva::selIvas(),
                'almacenes'=> Almacen::selAlmacenes(),
                'etiquetas'=> Etiqueta::selEstados(),
                'asociados'=> Cliente::selAsociados(),
                'garantias'=> Garantia::selGarantias(),
                'quilates'   => Quilate::selQuilates()
            ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProducto $request, Producto $producto)
    {
        $this->authorize('update', $producto);

        $data = $request->validated();

        // if ($data['univen'] == 'U'){
        //     $data['peso_gr'] = 1;
        // }

        $data['username'] = $request->user()->username;

        $producto->update($data);

        if (request()->wantsJson())
            return [
                'producto'=> $producto->load('clase'),
                'message' => 'EL producto ha sido modificado'
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
        if (esAdmin()){
            $producto = Producto::withTrashed()->find($id);
        }else{
            $producto = Producto::findOrFail($id);
        }

        if ($producto->trashed()) {
            $producto->restore();
            $msg="Registro restaurado!";
        }else{
            if (esAdmin() && hasHardDel()){
                $msg="Registro eliminado permanentemente!";
                $producto->forceDelete();
            }
            else{
                $msg="Registro eliminado!";
                $producto->delete();
            }
        }

        // if (esRoot())
        //     $data = Producto::with(['clase','estado','destino'])->withTrashed()->get();
        // else
        //     $data = Producto::with(['clase','estado','destino'])->all();

        if (request()->wantsJson()){
            return [
                'producto'=> $producto->load('clase'),
                'msg'=> $msg
            ];
        }
    }

     /**
     * Recibe las facturas por request, previamente de $this->lisfac()
     *
     * @param Request $request
     * @return void
     */
    public function excel(Request $request){

        return Excel::download(new InventarioExport($request->data, 'Productos '.session('empresa')->razon), 'inventario.xlsx');


    }

}
