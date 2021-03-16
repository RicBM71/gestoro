<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Contador extends Model
{

    protected $table = 'contadores';

    protected $appends = ['fac_ser','fac_auto','alb_ser','abo_ser'];

    protected $fillable = [
        'empresa_id','tipo_id','ejercicio','ult_albaran','serie_albaran','ult_factura','serie_factura',
        'ult_factura_auto','serie_factura_auto','serie_factura_abono','ult_factura_abono',
        'cerrado','username'
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

    public function tipo()
    {
    	return ($this->belongsTo(Tipo::class));
    }

    public function scopeEjercicio($query, $ejercicio){

        if ($ejercicio == 0) $ejercicio = date('Y');

        return $query->where('ejercicio', '=', $ejercicio);

    }
    public function scopeAbierto($query){

        return $query->where('cerrado', '=', false);

    }

    public function getAlbSerAttribute(){

        $l = strlen($this->ult_albaran);

        return $this->serie_albaran."0".str_repeat('0', 4-$l).$this->ult_albaran;


    }

    public function getFacSerAttribute(){

        $l = strlen($this->ult_factura);

        return $this->serie_factura."0".str_repeat('0', 4-$l).$this->ult_factura;


    }

    public function getFacAutoAttribute(){

        $l = strlen($this->ult_factura_auto);

        return $this->serie_factura_auto."0".str_repeat('0', 4-$l).$this->ult_factura_auto;


    }

    public function getAboSerAttribute(){

        $l = strlen($this->ult_factura_abono);

        return $this->serie_factura_abono."0".str_repeat('0', 4-$l).$this->ult_factura_abono;


    }

    public function setSerieAlbaranAttribute($s)
    {
        $this->attributes['serie_albaran'] = strtoupper($s);

    }

    public function setSerieFacturaAttribute($s)
    {
        $this->attributes['serie_factura'] = strtoupper($s);

    }

    public function setSerieFacturaAutoAttribute($s)
    {
        $this->attributes['serie_factura_auto'] = strtoupper($s);

    }

    public function setSerieFacturaAbonoAttribute($s)
    {
        $this->attributes['serie_factura_abono'] = strtoupper($s);

    }

     /**
     * @param integer ejercicio
     * @param integer tipo_id
     * @return array data
     */
    public static function incrementaContador($ejercicio, $tipo_id){

        //abort(403,'Something went wrong');

        try {

            $contador =  Contador::where('ejercicio',$ejercicio)
                                 ->where('tipo_id', $tipo_id)
                        ->lockForUpdate()->firstOrFail();

            $contador->ult_albaran++;

            $data['ult_albaran']  = $contador->ult_albaran;
            $data['serie_albaran']= $contador->serie_albaran;

            $arr = [
                'ult_albaran' => $contador->ult_albaran,
                'username' => session()->get('username')
            ];
            //Contador::where('id', $contador->id)->update($arr);
            $contador->update($arr);

            return $data;

          } catch (\Exception $e) {
                return abort(404, 'Contador no existe! '.$e->getMessage());
          }
    }

     /**
     * @param integer ejercicio
     * @param integer tipo_id
     * @return array data
     */
    public static function incrementaContadorReubicar($ejercicio, $tipo_id, $empresa_id){

        //abort(403,'Something went wrong');

        try {

            $contador =  Contador::withoutGlobalScope(EmpresaScope::class)
                                 ->where('ejercicio',$ejercicio)
                                 ->where('empresa_id', $empresa_id)
                                 ->where('tipo_id', $tipo_id)
                        ->lockForUpdate()->firstOrFail();

            $contador->ult_albaran++;

            $data['ult_albaran']  = $contador->ult_albaran;
            $data['serie_albaran']= $contador->serie_albaran;

            $arr = [
                'ult_albaran' => $contador->ult_albaran,
                'username' => session()->get('username')
            ];
            //Contador::where('id', $contador->id)->update($arr);
            $contador->update($arr);

            return $data;

          } catch (\Exception $e) {
                return abort(404, 'Emp: '.$empresa_id.' - Contador no existe! '.$ejercicio);
                //return abort(404, 'Contador no existe! '.$e->getMessage());
          }
    }

     /**
     * @param integer ejercicio
     * @param integer albaran para comparar si se puede o no retrasar el contador, solo si es la misma.
     * @return App\Contador $contador
     */
    public static function restaContadorAlbaran($ejercicio, $albaran, $tipo_id){


        try {

            $contador =  Contador::where('ejercicio',$ejercicio)
                                ->where('tipo_id', $tipo_id)
                                ->lockForUpdate()->firstOrFail();

            $sincronizado = [
                'estado'    =>  true,
                'msg'       => 'Albarán borrado: '.$contador->ult_albaran];

            if ($contador->ult_albaran == $albaran){
                $sincronizado = [
                    'estado'    =>  true,
                    'msg'       => 'Contador Sincronizado'];
                $contador->ult_albaran--;
            }

            $arr = [
                'ult_albaran' => $contador->ult_albaran,
                'serie_albaran'=> $contador->serie_albaran,
                'username' => session()->get('username')
            ];

            Contador::where('id', $contador->id)->update($arr);

            return $sincronizado;

          } catch (\Exception $e) {
                return abort(404, 'Error en contador. '.$e->getMessage());
          }
    }

     /**
     * @param integer ejercicio
     * @param integer factura (si se indica, no incremente contador.)
     * @return array data
     */
    public static function incrementaFactura($ejercicio, $tipo_id, $tipo_factura, $factura=0){

        //abort(403,'Something went wrong');

        try {

            $contador =  Contador::where('ejercicio',$ejercicio)
                                  ->where('tipo_id', $tipo_id)
                        ->lockForUpdate()->firstOrFail();

            if ($tipo_factura == 1){
                //$contador->ult_factura = $factura == 0 ? ($contador->ult_factura++) : $factura;
                if ($factura == 0)
                    $contador->ult_factura++;
                else
                    $contador->ult_factura = $factura;

                $data['ult_factura'] = $contador->ult_factura;
                $data['serie_factura']= $contador->serie_factura;
            }elseif ($tipo_factura == 2){
                if ($factura == 0)
                    $contador->ult_factura_auto++;
                else
                    $contador->ult_factura_auto = $factura;

                $data['ult_factura'] = $contador->ult_factura_auto;
                $data['serie_factura']= $contador->serie_factura_auto;

            }elseif ($tipo_factura == 3){
                if ($factura == 0)
                    $contador->ult_factura_abono++;
                else
                    $contador->ult_factura_abono = $factura;

                $data['ult_factura'] = $contador->ult_factura_abono;
                $data['serie_factura']= $contador->serie_factura_abono;
            }


            $arr = [
                'ult_factura' => $contador->ult_factura,
                'ult_factura_auto' => $contador->ult_factura_auto,
                'ult_factura_abono' => $contador->ult_factura_abono,
                'username' => session()->get('username')
            ];

            if ($factura == 0){
              //  Contador::where('id', $contador->id)->update($arr);
                $contador->update($arr);
            }

            return $data;

          } catch (\Exception $e) {
                 return abort(404, 'Contador Facturas no existe! '.$e->getMessage());
          }
    }

       /**
     * @param integer ejercicio
     * @param integer factura (si se indica, no incremente contador.)
     * @return array data
     */
    public static function incrementaFacturaAuto($ejercicio, $tipo_id, $factura=0){

        //abort(403,'Something went wrong');

        try {

            $contador =  Contador::where('ejercicio',$ejercicio)
                                 ->where('tipo_id', $tipo_id)
                                 ->lockForUpdate()->firstOrFail();

            if ($factura == 0){
                $contador->ult_factura_auto++;
            }else{
                $contador->ult_factura_auto = $factura;
            }

            $data['ult_factura'] = $contador->ult_factura_auto;
            $data['serie_factura']= $contador->serie_factura_auto;

            $arr = [
                'ult_factura_auto' => $contador->ult_factura_auto,
                'username' => session()->get('username')
            ];

            if ($factura == 0){
                Contador::where('id', $contador->id)->update($arr);
            }

            return $data;

          } catch (\Exception $e) {
                return abort(404, 'Contador FA existe! '.$e->getMessage());
          }
    }


       /**
     * @param integer ejercicio
     * @param integer albaran para comparar si se puede o no retrasar el contador, solo si es la misma.
     * @return App\Contador $contador
     */
    public static function restaContadorFactura($ejercicio, $tipo_id, $tipo_factura, $factura){


        try {

            $contador =  Contador::where('ejercicio',$ejercicio)
                                ->where('tipo_id', $tipo_id)
                                ->lockForUpdate()->firstOrFail();

            $estado = false;

            if ($tipo_factura == 1){
                if ($contador->ult_factura == $factura){
                    $estado = true;
                    $contador->ult_factura--;
                }
            }elseif ($tipo_factura == 2){
                if ($contador->ult_factura_auto == $factura){
                    $estado = true;
                    $contador->ult_factura_auto--;
                }
            }elseif ($tipo_factura == 3){
                if ($contador->ult_factura_abono == $factura){
                    $estado = true;
                    $contador->ult_factura_abono--;
                }
            }

            $arr = [
                'ult_factura'       => $contador->ult_factura,
                'ult_factura_auto'  => $contador->ult_factura_auto,
                'ult_factura_abono' => $contador->ult_factura_abono,
                'username' => session()->get('username')
            ];

            Contador::where('id', $contador->id)->update($arr);

            $msg = $estado ? 'Ok' : 'Contador no sincronizado. Revisar contador!';

            $sincronizado = [
                'estado'    =>  $estado,
                'msg'       =>  $msg];

            return $sincronizado;

          }catch (\Exception $e) {
                return abort(404, 'Contador no existe! '.$e->getMessage());
          }
    }




}
