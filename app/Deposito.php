<?php

namespace App;

use App\Caja;
use App\Compra;
use App\Scopes\EmpresaScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    //protected $esDebe = array(1,4,6,8);
    protected $dates =['fecha'];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];


    protected $fillable = [
        'fecha','compra_id','empresa_id', 'cliente_id','dias','concepto_id','importe','dias','notas',
        'iban','bic','username','remesada',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);

    }

    public function apuntesCaja()
    {
        return $this->hasMany(Caja::class);
    }

    public function apuntesCajaSinEmpresa(){
        return $this->hasMany(Caja::class)->withoutGlobalScope(EmpresaScope::class);
    }

    public function compra(){
        //return $this->hasOne(Compra::class);
        return $this->belongsTo(Compra::class);
    }

    public function concepto()
    {
    	return $this->belongsTo(Concepto::class);
    }

    public function cliente()
    {
    	return $this->belongsTo(Cliente::class);
    }

    public function scopeCompraId($query, $compra_id)
    {
        $query->with(['concepto'])
              ->where('compra_id', $compra_id )
              ->orderBy('fecha','desc')
              ->orderBy('id','desc');

    }

    public function scopeRemesada($query, $remesada)
    {
        $query->where('concepto_id', 2)
              ->where('remesada', $remesada);

    }


    /**
    *  @param int $compra_id
    */

    public static function totalesConcepto($compra_id){

        $data = Deposito::where('compra_id', $compra_id)
                ->get();

        $total=[0,0,0,0,0,0];

        foreach ($data as $row){
            if ($row->concepto_id >=1 && $row->concepto_id <= 3)
                $i = 0;  // depósito
            else if($row->concepto_id >=4 && $row->concepto_id <= 6)
                    $i = 1; // ampliación
            else if($row->concepto_id >=7 && $row->concepto_id <= 9)
                    $i = 2;  // a cuenta
            else if($row->concepto_id >=10 && $row->concepto_id <= 12)
                    $i = 3;  // recuperado
            else if($row->concepto_id >=13 && $row->concepto_id <= 15)
                    $i = 4;  // comprado
            else if($row->concepto_id >=16 && $row->concepto_id <= 18)
                    $i = 5;  // ampliación de capital

            $total[$i]+=$row->importe;
        }

        return $total;
    }

    public static function getAcuentaEnFecha($compra_id, $fecha){

        $q = DB::table('depositos')
                ->select(DB::raw('ROUND(SUM(importe), 0) AS importe'))
                ->where('empresa_id', session('empresa_id'))
                ->where('compra_id', '=',$compra_id)
                ->where('fecha', $fecha)
                ->whereIn('concepto_id',[7,8,9])
                ->first();

        return is_null($q->importe) ? 0 : $q->importe;

    }

    public static function valorCompras($fecha,$cliente_id, $compra_id){

        $q = DB::table('depositos')
                ->select(DB::raw('ROUND(SUM(importe), 0) AS importe'))
                ->where('empresa_id', session('empresa_id'))
                ->where('compra_id', '<>',$compra_id)
                ->where('cliente_id', $cliente_id)
                ->where('fecha', $fecha)
                // ->whereIn('concepto_id',[1,2,3,11,12,13,14])
                ->whereIn('concepto_id',[1,2,3,13,14,15,16,17,18])
                ->first();

        return is_null($q->importe) ? 0 : $q->importe;
    }

    /**
     * @param $fecha
     * @param $cliente_id
     * Obtiene el valor de los importes entregados a cuenta + recuperaciones
     */
    public static function valorAcuentaEnFecha($fecha,$cliente_id){

        $q = DB::table('depositos')
                ->select(DB::raw('ROUND(SUM(importe), 0) AS importe'))
                ->where('empresa_id', session('empresa_id'))
                ->where('cliente_id', $cliente_id)
                ->where('fecha', $fecha)
                ->whereIn('concepto_id',[7,8,9])
                ->first();

        return is_null($q->importe) ? 0 : $q->importe;
    }

    public static function valorCobrado($fecha,$cliente_id){

        $q = DB::table('depositos')
                ->select(DB::raw('ROUND(SUM(importe), 0) AS importe'))
                ->where('empresa_id', session('empresa_id'))
                ->where('cliente_id', $cliente_id)
                ->where('fecha', $fecha)
                ->whereIn('concepto_id',[4,5,6,7,8,9,10,11,12])
                ->first();

        return is_null($q->importe) ? 0 : $q->importe;
    }

    public static function valorPagado($fecha,$cliente_id){

        $q = DB::table('depositos')
                ->select(DB::raw('ROUND(SUM(importe), 0) AS importe'))
                ->where('empresa_id', session('empresa_id'))
                ->where('cliente_id', $cliente_id)
                ->where('fecha', $fecha)
                ->whereNotIn('concepto_id',[4,5,6,7,8,9,10,11,12])
                ->first();

        return is_null($q->importe) ? 0 : $q->importe;
    }


    public function scopeRecuperaciones($query){

        return $query->whereIn('concepto_id',[10,11,12]);

    }

}
