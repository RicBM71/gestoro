<?php

use App\Producto;
use Illuminate\Database\Seeder;

class KltProductosSeeder extends Seeder
{

    protected $bbdd="quilates";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $empresa = '1,2';
        $cruce_alm_emp[1]=1; // bombo
        $cruce_alm_emp[2]=9;  // edel
        $cruce_alm_emp[3]=11; // gold
        $cruce_alm_emp[4]=12; //recupera
        $cruce_alm_emp[5]=13; // maravillas
        $cruce_alm_emp[6]=0; //banco
        $cruce_alm_emp[7]=14; // chollo o.
        $cruce_alm_emp[8]=15; // bravo m
        $cruce_alm_emp[9]=16;  // prestige

        $etiqueta['N']=1; //no
        $etiqueta['S']=2; // sí
        $etiqueta['C']=3; // si con pvp
        $etiqueta['I']=4; // ya impresa
        $etiqueta['Y']=4; // ya impresa con pvp
        $etiqueta['D']=5; // ya impresa con pvp

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

            if ($row->empresa == 1)
                $empresa_ori_id = 3;
            else
                $empresa_ori_id = $row->empresa;

            $empresa_des_id = $empresa_ori_id;

            if ($empresa_ori_id == 16){
                if (substr($row->referencia,0,2) == 'BM')
                    $empresa_ori_id = 15;
                elseif (substr($row->referencia,0,2) == 'ED')
                    $empresa_ori_id = 9;
                elseif (substr($row->referencia,0,2) == 'GS')
                    $empresa_ori_id = 11;
                elseif (substr($row->referencia,0,2) == 'KL')
                    $empresa_ori_id = 3;
                elseif (substr($row->referencia,0,2) == 'MA')
                    $empresa_ori_id = 13;
                elseif (substr($row->referencia,0,2) == 'PR')
                    $empresa_ori_id = 16;
                elseif (substr($row->referencia,0,2) == 'RE')
                    $empresa_ori_id = 12;
            }

            if ($row->almacen == 0)
                $row->almacen = 6;

            if ($row->almacen == 6){
                $cruce_alm_emp[6] = $empresa_ori_id;
            }

            if ($row->nuevo == "N")
                $univen = 'U';
            else
                $univen = $row->univen;

            // if (in_array($row->fechaalta, ['0000-00-00',"1936-03-10","1900-01-01"]) || $row->fechaalta==null || $row->fechaalta == "")
            //     $fecha_alta = '2000-01-01';
            // else
            //     $fecha_alta = $row->fechaalta;

            if ($row->fechaalta == "0000-00-00")
                $row->fechaalta = "2000-01-01";

            if ($row->sysfum == "0000-00-00")
                $row->sysfum = "2000-01-01";

            $data[]=array(
                'id' => $row->id,
                'empresa_id' => $empresa_ori_id,
                //'empresa_id' => $cruce_alm_emp[$row->almacen],
                'almacen_id' => $row->almacen,
                //'destino_empresa_id' => $cruce_alm_emp[$row->almacen],
                'destino_empresa_id' => $empresa_des_id,
                'nombre' => $row->nombre,
                'nombre_interno' => $row->nomint,
                'clase_id' => $clase,
                'quilates' => $row->quilates == "" ? 0 : $row->quilates,
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
                'username' => $row->sysusr,
                'created_at' => $row->fechaalta.' 00:00:00',
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
