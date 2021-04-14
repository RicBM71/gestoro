<?php

namespace App;

use Carbon\Carbon;
use App\Scopes\EmpresaComunScope;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $dates =['fecha_nacimiento','fecha_dni', 'fecha_baja'];


    protected $fillable = [
        'empresa_id','nombre', 'razon', 'apellidos', 'direccion','cpostal','poblacion', 'provincia', 'telefono1', 'telefono2',
        'tfmovil','email', 'tipodoc', 'dni', 'fecha_nacimiento','fecha_baja','nacpro','nacpais',
        'fecha_dni','notas','bloqueado','iva_no_residente','facturar','vip','listar_347','asociado', 'fpago_id','iban','bic','username',
        'interes', 'interes_recuperacion','notificar_iban','descuento', 'ecommerce_id'
    ];



    // protected $casts = [
    //     'fecha_nacimiento' => 'datetime:Y-m-d',
    // ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaComunScope);

    }


    public function setNombreAttribute($nombre)
    {
        $this->attributes['nombre'] = strtoupper($nombre);

    }

    public function setRazonAttribute($razon)
    {
        $this->attributes['razon'] = strtoupper($razon);

    }

    public function setApellidosAttribute($apellidos)
    {
        $this->attributes['apellidos'] = strtoupper($apellidos);

    }
    public function setDireccionAttribute($direccion)
    {
        $this->attributes['direccion'] = strtoupper($direccion);

    }
    public function setCpostalAttribute($cpostal)
    {
        $this->attributes['cpostal'] = strtoupper($cpostal);

    }

    public function setPoblacionAttribute($poblacion)
    {
        $this->attributes['poblacion'] = strtoupper($poblacion);

    }

    public function setProvinciaAttribute($provincia)
    {
        $this->attributes['provincia'] = strtoupper($provincia);

    }

    public function setDniAttribute($dni)
    {
        $this->attributes['dni'] = strtoupper($dni);

    }

    public function setNacproAttribute($nacpro)
    {
        $this->attributes['nacpro'] = strtoupper($nacpro);

    }

    public function setNacpaisAttribute($nacpais)
    {
        $this->attributes['nacpais'] = strtoupper($nacpais);

    }

    public function setIbanAttribute($iban)
    {
        $this->attributes['iban'] = strtoupper($iban);

    }

    public function setBicAttribute($bic)
    {
        $this->attributes['bic'] = strtoupper($bic);

    }

    public static function updateIBAN($id,$iban,$bic){

        $reg = Cliente::find($id);

        $data=[
            'fpago_id' => 2,
            'iban'  => $iban,
            'bic'   => $bic,
            'username' => session()->get('username')
        ];


        $reg->update($data);

        return $reg;

        // $reg = Compra::where('id', $id)
        //       ->update($data);


    }

    public static function selAsociados()
    {

        return Cliente::select('id AS value', 'razon AS text')
            ->asociado()
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public function scopeAsociado($query){

        $query->where('asociado', true);

        return $query;

    }

    public function scopeDni($query, $dni){

        if (!Empty($dni))
        if (strpos($dni, '.') === false){
            $query->where('dni','like','%'.$dni.'%');
        }else{
            $id = explode(".", $dni);
            $query->where('id', '=', $id[1]);
        }

        return $query;

    }

    public function scopeRazon($query, $razon){

        if (!Empty($razon)){
            if (strpos($razon,',')){
                $cli = explode(",", $razon);
                $query->where('nombre','like',$cli[0].'%')
                      ->where('apellidos','like',$cli[1].'%');
            }else{
                $query->where('razon','like','%'.$razon.'%');
            }
        }

        return $query;

    }

    public function scopeBaja($query, $baja){

        if ($baja == "A")
            return $query->whereNull('fecha_baja');
        elseif ($baja == "B")
            return $query->where('fecha_baja','>','');
        else
            return $query;

    }

    public function scopeBloqueado($query, $bloqueado){

        if ($bloqueado != 'T')
            return $query->where('bloqueado',$bloqueado);


    }

    public function scopeNotas($query, $notas){

        if (!Empty($notas))
            $query->where('notas','like','%'.$notas.'%');

        return $query;

    }

    public function clidoc()
    {
    	return $this->hasOne(Clidoc::class);
    }


    // public function setFechaNacimientoAttribute($fecha_nacimiento)
    // {
    //     //$fecha_nacimiento = "21/06/1971";

    //     $this->attributes['fecha_nacimiento'] = $fecha_nacimiento
    //         ? Carbon::createFromFormat('dmY', $fecha_nacimiento)
    //         : null;

    // }

    // public function getFechaNacimientoAttribute($value){
    //     //return Carbon::createFromFormat('d/m/Y', $value);
    //     return is_null($value) ? null :  date('d/m/Y', strtotime($value));

    // }

}
