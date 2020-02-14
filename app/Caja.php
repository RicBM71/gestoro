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
        'username','apunte_id','updated_at'
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

    public function apunte()
    {
    	return ($this->belongsTo(Apunte::class));
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

        if ($dh != null)
            $query->where('dh',$dh);

        return $query;

    }

    public function scopeManual($query, $manual){

        switch ($manual) {
            case '':
                return $query;
                break;
            case 'C':
                return $query->where('deposito_id', '>', 0);
                break;
            case 'V':
                return $query->where('cobro_id', '>', 0);
                break;
            default:
                return $query->where('manual', $manual);
                break;
        }

    }

    public function scopeAdmin($query){

        if (!esAdmin())
            $query->where('manual','<>','A');

        return $query;

    }

    public function scopeApunte($query, $apunte_id){

        if ($apunte_id != null)
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

        return round($saldo,2);

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
