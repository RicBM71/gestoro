<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Albaran extends Model
{
    use SoftDeletes;

    protected $table = 'albaranes';

    protected $dates =['fecha_albaran','fecha_factura','fecha_notificacion'];
    protected $appends = ['fac_ser','alb_ser','alb_sere','fac_sere'];

    protected $fillable = [
        'empresa_id', 'tipo_id','serie_albaran','albaran', 'fase_id',
        'cliente_id','fecha_albaran','fecha_factura','factura',
        'serie_factura','tipo_factura','clitxt','fecha_notificacion','online','iva_no_residente',
        'notas_int','motivo_id','factura_txt','albaran_abonado_id', 'username', 'cuenta_id', 'fpago_id', 'notas_ext',
        'procedencia_empresa_id','facturar','taller_id','express','pedido','validado'
    ];

    protected $casts = [
        'fecha_albaran' => 'date:Y-m-d',
        'fecha_factura' => 'date:Y-m-d',
        'fecha_notificacion' => 'date:Y-m-d',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);

        static::deleting(function($albaran){

         //   $albaran->albalins->each->delete();
         //   $compra->depositos->each->delete();

        });
    }

    public function setClitxtAttribute($clitxt)
    {
        $this->attributes['clitxt'] = strtoupper($clitxt);

    }

    public function empresa()
    {
    	return ($this->belongsTo(Empresa::class));
    }


    public function cliente()
    {
    	return ($this->belongsTo(Cliente::class));
    }

    public function tipo()
    {
    	return ($this->belongsTo(Tipo::class));
    }

    public function fase()
    {
    	return ($this->belongsTo(Fase::class));
    }

    public function fpago()
    {
    	return ($this->belongsTo(Fpago::class));
    }

    public function cuenta()
    {
    	return ($this->belongsTo(Cuenta::class));
    }

    public function motivo()
    {
    	return ($this->belongsTo(Motivo::class));
    }

    public function taller()
    {
    	return ($this->belongsTo(Taller::class));
    }

    public function procedencia()
    {
    	return ($this->belongsTo(Empresa::class,'procedencia_empresa_id'));
    }

    public function albalins()
    {
        return $this->hasMany(Albalin::class);
    }

    public function cobros()
    {
        return $this->hasMany(Cobro::class);
    }

    public function getAlbSerAttribute(){

        return $this->serie_albaran." ".$this->albaran;

        $l = strlen($this->albaran);

        if ($l <= 4)
            return $this->serie_albaran."0".str_repeat('0', 4-$l).$this->albaran;
        else
            return $this->serie_albaran.$this->albaran;


    }

    public function getAlbSereAttribute(){

        return $this->serie_albaran." ".$this->albaran.substr(getEjercicio($this->fecha_albaran),-2);

        $l = strlen($this->albaran);

        if ($l <= 4)
            return $this->serie_albaran."0".str_repeat('0', 4-$l).$this->albaran.substr(getEjercicio($this->fecha_albaran),-2);
        else
            return $this->serie_albaran.$this->albaran.substr(getEjercicio($this->fecha_albaran),-2);

        //str_pad($this->albaran, 4, "0", STR_PAD_LEFT);

    }

    public function getFacSereAttribute(){

        if ($this->factura == null) return "";

        $l = strlen($this->factura);

        if ($l <= 4)
            return $this->serie_factura."0".str_repeat('0', 4-$l).$this->factura.substr(getEjercicio($this->fecha_factura),-2);
        else
            return $this->serie_factura.$this->factura.substr(getEjercicio($this->fecha_factura),-2);

        //str_pad($this->albaran, 4, "0", STR_PAD_LEFT);

    }

    public function getFacSerAttribute(){

        if ($this->factura == null) return "";

        $l = strlen($this->factura);

        if ($l <= 4)
            return $this->serie_factura."0".str_repeat('0', 4-$l).$this->factura;
        else
            return $this->serie_factura.$this->factura;

        //str_pad($this->albaran, 4, "0", STR_PAD_LEFT);

    }


    public static function scopeSerieNumero($query, $data){

        $albaran = $data['albaran'];

        $albaran=str_replace('.','-',$albaran);

        if (strstr($albaran,'-') === false)
			$ejercicio = date('Y');
		else{
			$arr = preg_split('/-/',$albaran);
			$albaran = $arr[0];
			if ($arr[1]>0)
				$ejercicio = '20'.$arr[1];
			else
				$ejercicio = date('Y');
        }

        if ($data['esfactura'])
            return $query->whereYear('fecha_factura', $ejercicio)
                        ->where('factura', $albaran)
                        ->where('serie_factura', $data['serie']);
        else
            return $query->whereYear('fecha_albaran', $ejercicio)
                         ->where('albaran', $albaran)
                         ->where('serie_albaran', $data['serie']);

    }


    public static function scopeFecha($query, $d,$h,$tipo){

        if ($tipo == "V")
            return $query->whereDate('fecha_albaran','>=', $d)
                         ->whereDate('fecha_albaran','<=', $h);
        elseif ($tipo == "F")
            return $query->whereDate('fecha_factura','>=', $d)
                         ->whereDate('fecha_factura','<=', $h);
        else
            return $query->whereDate('updated_at','>=', $d)
                         ->whereDate('updated_at','<=', $h);


    }

    public static function scopeFacturados($query, $tipo){

        if ($tipo == "F")
            return $query->where('factura','>', 0);
        elseif ($tipo == "N")
            return $query->whereNull('factura');
        else
            return $query;

    }

    public static function scopeFase($query, $fase_id){

        if (!Empty($fase_id) && $fase_id > 0)
            return $query->where('fase_id','=', $fase_id);

        return $query;

    }

    public static function scopeTipo($query, $tipo_id){

        if (hasConsultas()){
            if (!Empty($tipo_id) && $tipo_id > 0)
                return $query->where('tipo_id','=', $tipo_id);
        }else{
            if ($tipo_id == null)
                return $query->whereIn('tipo_id', [3,5]);
            else{

                if ($tipo_id == 4) // para no permitir listar a los que no son administradores albaranes de nuevo
                    $tipo_id = 0;

                return $query->where('tipo_id','=', $tipo_id);
            }
        }


        return $query;

    }

    public static function scopeFpago($query, $fpago_id){

        if (!Empty($fpago_id) && $fpago_id > 0)
            return $query->where('fpago_id','=', $fpago_id);

        return $query;

    }


    public static function scopeDepositos($query, $depositos){

        if ($depositos == true)
            return $query->where('procedencia_empresa_id','>', 0);

        return $query;

    }

    public static function scopeClitxt($query, $clitxt){

        if ($clitxt != null || $clitxt > ''){

            if ($clitxt[0] ==':'){
                $clitxt = str_replace(':','',$clitxt);
                return $query->where('notas_int','like', '%'.$clitxt.'%');
            }else{
                return $query->where('clitxt','like', '%'.$clitxt.'%');
            }

        }

        return $query;
    }

    public static function scopePedido($query, $pedido){

        if (!Empty($pedido) && $pedido > 0)
            return $query->where('pedido','=', $pedido);

        return $query;

    }




    // public static function pendientesDeFacturar($d, $h, $tipo_id, $cobro){

    //     if ($cobro == 'T')
    //         $fpago = array(1,2,3,4);
    //     elseif($cobro == "B")
    //         $fpago = array(2,3,4);
    //     else
    //         $fpago = array(1);


    //     return DB::table('albaranes')
    //             ->select(DB::raw(DB::getTablePrefix().'albaranes.id,'.
    //                              DB::getTablePrefix().'albaranes.albaran,'.
    //                              DB::getTablePrefix().'albaranes.iva_no_residente, MAX('.
    //                              DB::getTablePrefix().'cobros.fecha) AS fecha'))
    //         //    ->join('clientes', 'cliente_id', '=', 'clientes.id')
    //             ->join('cobros', 'albaran_id', '=', 'albaranes.id')
    //             ->where('albaranes.empresa_id', session('empresa')->id)
    //             ->where('tipo_id', $tipo_id)
    //             ->where('fase_id', 11)
    //             ->where('factura', 0)
    //             ->where('facturar', true)
    //             ->whereNull('albaranes.deleted_at')
    //             ->whereIn('cobros.fpago_id', $fpago)
    //             ->groupBy('albaranes.id','albaran','iva_no_residente')
    //             ->havingRaw('MAX('.DB::getTablePrefix().'cobros.fecha) >= ? AND MAX('.DB::getTablePrefix().'cobros.fecha) <= ?',[$d,$h])
    //             ->orderBy('fecha')
    //             ->get();

    // }


}
