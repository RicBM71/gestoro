<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $fillable = [
        'id','nombre', 'empresa_id', 'grupo_id', 'ejercicio', 'ult_compra', 'ult_factura', 'serie_fac','serie_com', 'semdia_bloqueo',
        'dias_custodia','dias_cortesia','interes','codigo_pol','nombre_csv','cerrado','grabaciones','peso_frm','username', 'recompras',
        'interes_recuperacion','plantilla_excel','establecimiento','tramo','interes_min'
    ];

    /**
     *
     * Añadimos global scope para filtrado por empresa.
     *
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);
    }

    public function grupo()
    {
    	return $this->belongsTo(Grupo::class);
    }

    public static function selLibros($ejercicio,$grupo_id){

        // return Libro::select('id AS value', 'nombre AS text')
        //     ->ejercicio($ejercicio)
        //     ->orderBy('nombre', 'asc')
        //     ->get();

        return Libro::select(DB::raw('id AS value, CONCAT(nombre, " " , ejercicio) AS text'))
                        ->ejercicio($ejercicio)
                        ->grupo($grupo_id)
                        ->abierto()
                        ->orderBy('nombre', 'asc')
                        ->get();

    }

    public static function selLibrosByEjercicio($ejercicio){

        return Libro::select(DB::raw('id AS value, CONCAT(nombre, " " , ejercicio) AS text, ejercicio,grupo_id'))
                        ->ejercicio($ejercicio)
                        ->orderBy('nombre', 'asc')
                        ->get();

    }

    public static function selLibrosAbiertos(){

        return Libro::select(DB::raw('id AS value, CONCAT(nombre, " " , ejercicio) AS text, ejercicio, grupo_id, codigo_pol, nombre_csv'))
                        ->abierto()
                        ->orderBy('nombre', 'asc')
                        ->get();

    }

    public static function selLibrosGrupoEmpresa($empresa_id, $ejercicio){

        return DB::table("libros")->select(DB::raw('grupo_id AS value, CONCAT(nombre, " " , ejercicio) AS text'))
                        ->where('empresa_id', $empresa_id)
                        ->where('ejercicio', $ejercicio)
                        ->orderBy('nombre', 'asc')
                        ->get();

    }

    // public static function selSerieCompras(){

    //     return Libro::select(DB::raw('DISTINCT serie_com AS value, '.DB::getTablePrefix().'nombre AS text'))
    //                     ->orderBy('text', 'asc')
    //                     ->get();

    // }

    public function scopeEjercicio($query, $ejercicio){

        if ($ejercicio == 0) $ejercicio = date('Y');

        return $query->where('ejercicio', '=', $ejercicio);

    }

    public function scopeAbierto($query){

        return $query->where('cerrado', '=', false);

    }

    public function scopeGrupo($query, $grupo_id){

        return $query->where('grupo_id', '=', $grupo_id);

    }


    /**
     * @param integer ejercicio
     * @param integer grupo_id
     * @return array data
     */
    public static function incrementaContador($ejercicio, $grupo_id, $albaran){

        //abort(403,'Something went wrong');

        try {

            $libro =  Libro::where('grupo_id', $grupo_id)
                            ->where('ejercicio',$ejercicio)
                            ->lockForUpdate()->firstOrFail();


            if (is_null($albaran)){
                $libro->ult_compra++;

                $data['albaran'] = $libro->ult_compra;

                $arr = [
                    'ult_compra' => $libro->ult_compra,
                    'username' => session()->get('username')
                ];
                Libro::where('id', $libro->id)->update($arr);
            }
            else{
                $data['albaran'] = $albaran;
            }

            $data['serie_com'] = $libro->serie_com;
            $data['semdia_bloqueo'] = $libro->semdia_bloqueo;
            $data['dias_custodia'] = $libro->dias_custodia;
            $data['interes'] = $libro->interes;
            $data['interes_recuperacion'] = $libro->interes_recuperacion;

            return $data;

          } catch (\Exception $e) {
                return $e->getMessage();
          }
    }

    /**
     * @param integer ejercicio
     * @param integer grupo_id
     * @param integer número para comparar si se puede o no retrasar el contador, solo si es la misma.
     * @param integer actua dependiendo de si se borra la compra = 1 ó es un traslado de compra = 0.
     * @return App\Libro $contador
     */
    public static function restaContadorCompra($ejercicio, $grupo_id, $num_compra, $delete=1){

        $numero_compras = DB::table('compras')
                                ->where('empresa_id', session('empresa_id'))
                                ->where('ejercicio', $ejercicio)
                                ->where('grupo_id', $grupo_id)->count();

        try {

            $contador = Libro::where('grupo_id',$grupo_id)
                              ->where('ejercicio',$ejercicio)
                              ->lockForUpdate()->firstOrFail();

            $sincronizado = [
                'estado'    =>  false,
                'msg'       => 'Revisar contador! Compra: '.$contador->ult_compra];

            if ($contador->ult_compra == $num_compra && $contador->ult_compra == ($numero_compras + $delete)){
                $sincronizado = [
                    'estado'    =>  true,
                    'msg'       => 'Contador Sincronizado'];
                $contador->ult_compra--;

                $arr = [
                    'ult_compra' => $contador->ult_compra,
                    'username' => session()->get('username')
                ];

                Libro::where('id', $contador->id)->update($arr);
            }


            return $sincronizado;

          } catch (\Exception $e) {
                return $e->getMessage();
          }
    }

      /**
     * @param integer ejercicio
     * @param integer grupo_id
     * @return String $serie
     */
    public static function getSerieCom($ejercicio, $grupo_id){

        try {

            $libro =  Libro::where('grupo_id',$grupo_id)
                            ->where('ejercicio',$ejercicio)
                            ->lockForUpdate()->firstOrFail();

            return $libro->serie_com;

          } catch (\Exception $e) {
                return $e->getMessage();
          }
    }

     /**
     * @param integer $ejercicio
     * @param integer $grupo_id
     * @return Array $data['serie','factura']
     */
    public static function getContadorCompra($ejercicio, $grupo_id){

        try {

            $libro =  Libro::where('grupo_id',$grupo_id)
                            ->where('ejercicio',$ejercicio)
                            ->lockForUpdate()->firstOrFail();

            return $libro;

          } catch (\Exception $e) {
                //return $e->getMessage();
                return abort(403,"No se ha encontrado el libro para el ejercicio");
          }
    }

}
