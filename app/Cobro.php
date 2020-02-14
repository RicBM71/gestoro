<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cobro extends Model
{
    use SoftDeletes;


    protected $dates =['fecha'];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];


    protected $fillable = [
        'fecha','albaran_id','empresa_id', 'cliente_id','fpago_id','importe','notas',
        'username',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope(new EmpresaScope);
    // }

    public function albaran(){
        return $this->hasOne(Albaran::class);
    }

    public function fpago()
    {
    	return $this->belongsTo(Fpago::class);
    }

    public function apuntesCajaSinEmpresa(){
        return $this->hasMany(Caja::class)->withoutGlobalScope(EmpresaScope::class);
    }

    public function scopeAlbaranId($query, $albaran_id)
    {
        $query->with(['fpago'])
              ->where('albaran_id', $albaran_id )
              ->orderBy('fecha','desc')
              ->orderBy('id','desc');

    }

    public static function getAcuentaByAlbaran($albaran_id){

        $q = DB::table('cobros')
                ->select(DB::raw('ROUND(SUM(importe), 2) AS importe'))
                ->where('albaran_id', '=',$albaran_id)
                ->whereNull('deleted_at')
                ->first();

        return is_null($q->importe) ? 0 : $q->importe;

    }

    public static function getAcuentaByClienteFecha($cliente_id, $fecha){

        $q = DB::table('cobros')
                ->select(DB::raw('ROUND(SUM(importe), 2) AS importe'))
                ->where('cliente_id', '=',$cliente_id)
                ->whereDate('fecha', $fecha)
                ->whereNull('deleted_at')
                ->first();

        return is_null($q->importe) ? 0 : $q->importe;

    }



}
