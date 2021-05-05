<?php

use App\Albaran;
use App\Producto;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Scopes\EmpresaProductoScope;

class EoProductosSeeder extends Seeder
{
    protected $bbdd="evaoro";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->update_producto();
       // return;

        $i = DB::delete(DB::RAW('DELETE FROM `klt_albaranes` where id not in (select albaran_id from klt_albalins)'));
        //\Log::info('albaranes borrados: '.$i);
        DB::delete(DB::RAW('DELETE FROM `klt_cobros` where albaran_id not in (select id from klt_albaranes)'));

        DB::update(DB::RAW('UPDATE klt_productos set empresa_id = 3, destino_empresa_id = 5 where empresa_id = 6 and estado_id = 2 and deleted_at is null'));
        DB::update(DB::RAW('UPDATE klt_productos set empresa_id = 7, destino_empresa_id = 7 where estado_id = 5 and deleted_at is null'));

        //SELECT estado_id,substr(referencia,1,2), count(*) FROM `klt_productos` WHERE empresa_id = 3 and estado_id in(1,2,3) and deleted_at is null GROUP by 1,2

      //  $this->check_albaranes();

        $this->cambiar_ubicacion();

    }

    private function cambiar_ubicacion(){

        session(['empresa_id' => 3]);
        $data = DB::table('productos')->select('*')
                    ->where('empresa_id', 3)
                    ->where('estado_id','2')
                    ->whereNull('productos.compra_id')
                    ->whereNull('productos.deleted_at')
                    ->get();
        $i=0;
        foreach ($data as $row){

            if (substr($row->referencia,0,2)!='EO')
                continue;

            $producto = Producto::withOutGlobalScope(EmpresaProductoScope::class)
                        ->where('referencia',$row->referencia)
                        ->where('compra_id','>',0)
                        ->where('estado_id', 2)
                        ->first();

            if ($producto != null){
                $i++;
                // damos de baja la copia
                DB::table('productos')->where('id', $row->id)->update(['deleted_at'=> Carbon::now(),'username'=>'sys20']);

                // cambio de ubicación al original
                DB::table('productos')->where('id', $producto->id)->update(['destino_empresa_id'=>3,'username'=>'sys20']);
            }


        }

    }

    private function check_albaranes(){

        session(['empresa_id' => 3]);

        $data = DB::table('albaranes')->select('albalins.id AS albalin_id','referencia','productos.id','albaran_id')
                    ->join('albalins', 'albalins.albaran_id','=', 'albaranes.id')
                    ->join('productos', 'producto_id','=', 'productos.id')
                    ->where('albaranes.empresa_id', 3)
                    ->where('fase_id', 10)
                    ->whereNull('productos.compra_id')
                    ->get();

        $i=0;
        foreach ($data as $row){

            if (substr($row->referencia,0,2)!='EO')
                continue;

            $producto = Producto::withOutGlobalScope(EmpresaProductoScope::class)
                            ->where('referencia',$row->referencia)
                            ->where('compra_id','>',0)->first();

            if ($producto != null){
                $i++;
                DB::table('albalins')->where('albalins.id', $row->albalin_id)->update(['producto_id'=>$producto->id,'username'=>'sys20']);

                DB::table('albaranes')->where('id', $row->albaran_id)->update(['updated_at'=>Carbon::now(),'username'=>'sys20']);

                DB::table('productos')->where('id', $producto->id)->update(['destino_empresa_id'=>3,'estado_id'=>2,'username'=>'sys20']);
            }


        }
    }

    private function update_producto(){

        $etiqueta['N']=1; //no
        $etiqueta['S']=2; // sí
        $etiqueta['C']=3; // si con pvp
        $etiqueta['I']=4; // ya impresa
        $etiqueta['Y']=5; // ya impresa con pvp
        $etiqueta['D']=6; // ya impresa con pvp

        Producto::truncate();

        /// depósitos
        $reg = DB::connection($this->bbdd)
            ->select('select * from productos '.
            //' where empresa in('.$empresa.')');
            ' where empresa >= 1');

        $data=array();
        $i=0;
        foreach ($reg as $row){
            $i++;

            if ($row->tipo == "O")
                $clase=1;
            elseif($row->tipo == "P")
                $clase=2;
            elseif($row->tipo == "T")
                $clase=3;
            elseif($row->tipo == "I")
                $clase=4;
            elseif($row->tipo == "R")
                $clase=5;
            elseif($row->tipo == "A")
                $clase=6;
            elseif($row->tipo == "B")
                $clase=7;
            elseif($row->tipo == "C")
                $clase=8;

            if ($row->estado == "I")
                $estado=1;
            elseif($row->estado == "B")
                $estado=2;
            elseif($row->estado == "R")
                $estado=3;
            elseif($row->estado == "V")
                $estado=4;
            elseif($row->estado == "G")
                $estado=5;

            if ($row->tienda == 3)
                $empresa_id = 7;
            else
                $empresa_id = $row->empresa;

            if ($row->nuevo == "N")
                $univen = 'U';
            else
                $univen = $row->univen;

            if (in_array($row->fechaalta, ["0000-00-00","1936-03-10"]) || $row->fechaalta==null || $row->fechaalta == "")
                $fecha_alta = $row->sysfum.' 12:00:00';
            else
                $fecha_alta = $row->fechaalta;

            $cliente_id = $row->proveedor;
            if ($row->proveedor <= 0)
                $cliente_id  = null;
            if ($row->albaran <= 0 || $row->albaran == '')
                $cliente_id  = null;

            $data[]=array(
                'id' => $row->id,
                'empresa_id' => $empresa_id,
                //'empresa_id' => $cruce_alm_emp[$row->almacen],
                'almacen_id' => $row->almacen,
                //'destino_empresa_id' => $cruce_alm_emp[$row->almacen],
                'destino_empresa_id' => $empresa_id,
                'nombre' => $row->nombre,
                'nombre_interno' => $row->nomint == '' ? null : $row->nomint,
                'clase_id' => $clase,
                'quilates' => $row->quilates == null ? 0 : $row->quilates,
                'caracteristicas'=> $row->quilacomp,
                'peso_gr' => $row->peso,
                'precio_coste' => $row->pcoste,
                'precio_venta' => $row->pventa,
                'univen' => $univen,
                'compra_id' => $row->albaran == 0 ? null : $row->albaran,
                'ref_pol' => $row->albarantx=="" ? null: $row->albarantx,
                'estado_id' => $estado,
                'etiqueta_id' => $row->etiqueta,
                'referencia' => str_replace("-","",$row->referencia),
                'cliente_id' => $cliente_id,
                'iva_id' => $row->tipoiva,
                'etiqueta_id' => $row->etiqueta==null ? 1 : $etiqueta[$row->etiqueta],
                'online' => $row->online=="S" ? true : false,
                'deleted_at' => $row->baja=="S" ? $row->sysfum : null,
                'notas'=> $row->notas=='' ? null : $row->notas,
                'stock'=> $row->stock,
                'username' => $row->sysusr,
                'created_at' => $fecha_alta,
                'updated_at' => $row->sysfum.' '.$row->syshum,
            );

            if ($i % 1000 == 0){
                DB::table('productos')->insert($data);
                $data=array();
            }

        }

        DB::table('productos')->insert($data);
    }
}
