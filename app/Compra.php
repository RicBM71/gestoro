<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\EmpresaScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $dates =['fecha_compra','fecha_bloqueo','fecha_renovacion','fecha_recogida','fecha_factura'];

    protected $fillable = [
        'empresa_id', 'grupo_id','dias_custodia', 'ejercicio','serie_com','albaran','cliente_id','tipo_id',
        'fecha_compra','fecha_bloqueo','fecha_renovacion','fecha_recogida','importe',
        'importe_renovacion','importe_acuenta', 'interes','fase_id', 'factura','fecha_factura',
        'serie_fac','papeleta','notas', 'username','retencion'
    ];

    protected $casts = [
        'fecha_compra' => 'date:Y-m-d',
        'fecha_bloqueo' => 'date:Y-m-d',
        'fecha_recogida' => 'date:Y-m-d',
    ];

    protected $appends = ['alb_ser','fac_ser','eje_alb','retraso', 'resto_custodia', 'imp_recu', 'imp_pres','factura_compra', 'imp_ret'];

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

    public function empresa()
    {
    	return ($this->belongsTo(Empresa::class));
    }

    public function grupo()
    {
    	return ($this->belongsTo(Grupo::class));
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

    public function comlines()
    {
        return $this->hasMany(Comline::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }


    public function depositos()
    {
        return $this->hasMany(Deposito::class);
    }

    // public function getFechaCompraAttribute($value){

    //     return Carbon::parse($this->attributes['fecha_compra'])->format('Y-m-d');
    // }

    public function getAlbSerAttribute(){

        $l = strlen($this->albaran);

        if ($l <= 4)
            return $this->serie_com."0".str_repeat('0', 4-$l).$this->albaran.'-'.substr($this->ejercicio,-2);
        else
            return $this->serie_com.$this->albaran.'-'.substr($this->ejercicio,-2);

        //str_pad($this->albaran, 4, "0", STR_PAD_LEFT);

    }

    public function getFacSerAttribute(){

        // $l = strlen($this->factura);

        // if ($l <= 4)
        //     return $this->serie_fac."0".str_repeat('0', 4-$l).$this->factura;
        // else
            return $this->serie_fac.$this->factura;



    }

    public function getEjeAlbAttribute(){

        $l = strlen($this->albaran);

        if ($l <= 4)
            return $this->serie_com.substr($this->ejercicio,-2)."0".str_repeat('0', 4-$l).$this->albaran;
        else
            return $this->serie_com.substr($this->ejercicio,-2).$this->serie_com.$this->albaran;
    }



    public function getFacturaCompraAttribute(){

        return $this->serie_fac.$this->factura;


    }

    public function getImpRecuAttribute(){

        //$imp =  $this->importe + $this->getImpRenovaAttribute() - $this->importe_acuenta;
        $imp =  $this->importe + $this->importe_renovacion - $this->importe_acuenta;

        if ($imp < 0 ) return 0;
        else return $imp;

    }

    public function getImpPresAttribute(){

        $pres = $this->importe - $this->importe_acuenta;

        return $pres < 0 ? 0 : $pres;

    }

    public function getImpRetAttribute(){

        return round($this->importe * $this->retencion / 100, 2);

    }

    // public function getImpRenovaAttribute(){
    //     return $this->importe_renovacion;
    //     //return round(($this->importe - $this->importe_acuenta) * $this->interes / 100, 0);
    // }

    public function getRestoCustodiaAttribute(){

        if ($this->tipo_id == 2) return 0;

        $hoy = Carbon::today();
        $fecha_renovacion = Carbon::createFromFormat('Y-m-d h:i:s', $this->fecha_renovacion);
        $fecha_compra = Carbon::createFromFormat('Y-m-d h:i:s', $this->fecha_compra);

        $retraso = $fecha_renovacion->diffInDays($hoy,true);

        if  ($this->fecha_compra == $hoy)
            return  $this->dias_custodia;
        else{
            return $retraso;
        }

    }

    public function getRetrasoAttribute(){

        if ($this->tipo_id == 2 || $this->fase_id >=5) return "";

        $hoy = Carbon::today();
        $fecha_renovacion = Carbon::createFromFormat('Y-m-d h:i:s', $this->fecha_renovacion);

        $retraso = $fecha_renovacion->diffInDays($hoy,false);

        return ($retraso > 0) ? $retraso : 0;

    }

    /**
    *
    * Marca bloqueo en base a semana +  y día, por ejemplo 3/1 días libera el martes (hasta el lunes)
    *           de la fecha dada a 3 semanas según el ejemplo 3/1
    * @param date fecha
    * @param string semana/día
    */
    public static function Bloqueo($fecha, $semdia){

        $s = explode("/", $semdia);
        // $fecha = date('Y-m-d', strtotime($fecha));
        //$f = Carbon::createFromFormat('Y-m-d h:i:s', $fecha);
        $f = Carbon::parse($fecha);

        // if ($s[0]==0)   // no hay bloqueo, para complementos.
        //     return $fecha;

        $f->addWeek($s[0]);

        return $f->subDays($f->dayOfWeek - $s[1]);

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
                         ->where('serie_fac', $data['serie']);
        else
            return $query->where('ejercicio', $ejercicio)
                     ->where('albaran', $albaran)
                     ->where('serie_com', $data['serie']);

    }

    public static function scopeFecha($query, $d,$h,$tipo){

        if ($tipo == "C")
            return $query->whereDate('fecha_compra','>=', $d)
                         ->whereDate('fecha_compra','<=', $h);
        elseif ($tipo == "R")
            return $query->whereDate('fecha_recogida','>=', $d)
                         ->whereDate('fecha_recogida','<=', $h);
        elseif ($tipo == "F")
            return $query->whereDate('fecha_factura','>=', $d)
                         ->whereDate('fecha_factura','<=', $h);
        else
            return $query->whereDate('updated_at','>=', $d)
                         ->whereDate('updated_at','<=', $h);
    }

    public static function scopeGrupo($query, $grupo_id){

        if (!Empty($grupo_id))
            return $query->where('grupo_id','=', $grupo_id);

        return $query;

    }

    public static function scopeFase($query, $fase_id){

        if (!Empty($fase_id) && $fase_id > 0)
            return $query->where('fase_id','=', $fase_id);

        return $query;

    }

    public static function scopeTipo($query, $tipo_id){

        if (!Empty($tipo_id) && $tipo_id > 0)
            return $query->where('tipo_id','=', $tipo_id);

        return $query;

    }





    /**
     * Lista líneas para generar libro excel
     *
     * @param [type] $data
     * @return collect
     */
    public static function getLibroPolExcel($data){

        return DB::table('compras')
            ->join('clientes','compras.cliente_id','=','clientes.id')
            ->join('comlines','compras.id','=','comlines.compra_id')
            ->join('clases','comlines.clase_id','=','clases.id')
            ->select('compras.fecha_compra','compras.albaran','papeleta','clientes.dni','clientes.nombre','apellidos','direccion','poblacion','provincia','nacpro',
                    'concepto','peso_gr','comlines.importe','clases.nombre AS clase','comlines.quilates','grabaciones','colores')
            ->where('compras.empresa_id', session('empresa')->id)
            ->where('compras.grupo_id', $data['grupo_id'])
            ->whereDate('fecha_compra','>=',$data['fecha_d'])
            ->whereDate('fecha_compra','<=',$data['fecha_h'])
            ->orderBy('albaran')
            ->get();
    }


}

