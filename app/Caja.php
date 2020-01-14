<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'empresa_id', 'fecha', 'dh', 'nombre', 'importe', 'manual','deposito_id', 'cobro_id',
        'username','apunte_id'
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

    public function setImporteAttribute($value)
    {
        $this->attributes['importe'] = round($value,2);
    }

    public function scopeRangoFechas($query, $d, $h){

        $query->where('fecha','>=',$d);
        $query->where('fecha','<=',$h);

        return $query;

    }
    public function scopeDh($query, $dh){

        if ($dh != 'T')
            $query->whereYear('dh',$dh);

        return $query;

    }

    public function scopeManual($query, $manual){

        if ($manual <> 'T')
            $query->where('manual',$manual);

        return $query;

    }

    public function scopeApunte($query, $apunte_id){

        if ($apunte_id <> '')
            $query->where('apunte_id',$apunte_id);

        return $query;

    }


    public static function saldo($fecha){

        $f = Carbon::parse($fecha)->format('Y-m-d');
        $ejercicio = Carbon::parse($fecha)->format('Y');

        $saldo = 0;

        $data = DB::table('cajas')
                ->selectRaw('dh, SUM(importe) AS importe')
                ->where('empresa_id', session('empresa_id'))
                ->groupBy('dh')
                ->whereYear('fecha', $ejercicio)
                ->where('fecha', '<=', $f)
                ->get();

        foreach ($data as $row){
            $valor = ($row->dh == 'D') ? $row->importe * -1 : $row->importe;
            $saldo += $valor;
        }

        return $saldo;

    }

    public static function saldoByEmpresa($empresa_id, $fecha){

        $f = Carbon::parse($fecha)->format('Y-m-d');
        $ejercicio = Carbon::parse($fecha)->format('Y');

        $saldo = 0;

        $data = DB::table('cajas')
                ->selectRaw('dh, SUM(importe) AS importe')
                ->groupBy('dh')
                ->where('empresa_id', $empresa_id)
                ->whereYear('fecha', $ejercicio)
                ->where('fecha', '<=', $f)
                ->get();

        foreach ($data as $row){
            $valor = ($row->dh == 'D') ? $row->importe * -1 : $row->importe;
            $saldo += $valor;
        }

        return $saldo;

    }


}
