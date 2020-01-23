<?php

use App\Clase;
use App\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YjProductosSeeder extends Seeder
{
    protected $bbdd="yajap";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $etiqueta['N']=1; //no
        $etiqueta['S']=2; // sí
        $etiqueta['C']=3; // si con pvp
        $etiqueta['I']=4; // ya impresa
        $etiqueta['Y']=4; // ya impresa con pvp
        $etiqueta['D']=5; // ya impresa con pvp

        Producto::truncate();
        Clase::truncate();


        $clase = new Clase;

        $clase->nombre = "Oro";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = true;

        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Plata";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Platino";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Piedras Preciosas";
        $clase->grupo_id = 1;
        $clase->peso = true;
        $clase->quilates = true;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Otros";
        $clase->grupo_id = 1;
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Bisutería";
        $clase->grupo_id = 2;  //B
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Complementos";
        $clase->grupo_id = 2;  //C
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();

        $clase = new Clase;
        $clase->nombre = "Otros";
        $clase->grupo_id = 2;
        $clase->peso = false;
        $clase->quilates = false;
        $clase->save();






        /// depósitos
        $reg = DB::connection($this->bbdd)
            ->select('select * from productos '.
            //' where empresa in('.$empresa.')');
            ' where empresa <> 2 or estado = "G"');

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

            $empresa_id = $row->tienda;
            if ($row->empresa == 3)
                $empresa_id  = 3;

            if ($row->nuevo == "N")
                $univen = 'U';
            else
                $univen = $row->univen;

            if (in_array($row->fechaalta, ["0000-00-00","1936-03-10","1900-01-01"]) || $row->fechaalta==null || $row->fechaalta == "")
                $fecha_alta = $row->sysfum.' 12:00:00';
            else
                $fecha_alta = $row->fechaalta;

            // if ($row->fechaalta == "1900-01-01")
            //     $row->sysfum = "2010-01-01";

            $data[]=array(
                'id' => $row->id,
                'empresa_id' => $empresa_id,
                //'empresa_id' => $cruce_alm_emp[$row->almacen],
                'almacen_id' => $row->almacen,
                //'destino_empresa_id' => $cruce_alm_emp[$row->almacen],
                'destino_empresa_id' => $empresa_id,
                'nombre' => $row->nombre,
                'nombre_interno' => $row->nomint,
                'clase_id' => $clase,
                'quilates' => $row->quilates == null ? 0 : $row->quilates,
                'caracteristicas'=> $row->quilacomp,
                'peso_gr' => $row->peso,
                'precio_coste' => $row->pcoste,
                'precio_venta' => $row->pventa,
                'univen' => $univen,
                'compra_id' => $row->albaran,
                'ref_pol' => $row->albarantx,
                'estado_id' => $estado,
                'etiqueta_id' => $row->etiqueta,
                'referencia' => str_replace("-","",$row->referencia),
                'cliente_id' => $row->proveedor <=-1 ? null : $row->proveedor,
                'iva_id' => $row->tipoiva,
                'etiqueta_id' => $row->etiqueta==null ? 1 : $etiqueta[$row->etiqueta],
                'online' => $row->online=="S" ? true : false,
                'deleted_at' => $row->baja=="S" ? $row->sysfum : null,
                'notas'=> $row->notas,
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
