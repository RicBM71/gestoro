<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Albalin extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'albaran_id','empresa_id','producto_id','unidades','importe_unidad',
        'precio_coste','importe_venta','iva_id','iva','username','notas'
    ];

    protected $appends = [
        'margen'
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope(new EmpresaScope);

    // }

    public function producto()
    {
    	return $this->belongsTo(Producto::class);
    }

    public function albaran(){
        return $this->belongsTo(Albaran::class);
    }

    public function productos(){
        return $this->belongsTo(Producto::class);
    }

    public function scopeAlbaranId($query, $albaran_id)
    {
        $query->where('albaran_id', '=', $albaran_id );

    }

    public function getImporteUnidadAttribute()
    {
        return round($this->attributes['importe_unidad'], 4);
    }

    // public function setImporteVentaAttribute()
    // {
    //     $this->attributes['importe_venta'] = 100;//round($this->unidades * $this->importe_unidad, 2);
    // }

    // public static function totalAlbaran($id){
    //     return DB::table('albalins')
    //             ->select(DB::raw('SUM(importe_gr_uni) AS importe_gr_uni, SUM(importe_venta) AS importe_venta'))
    //             ->where('albaran_id', $id)
    //             ->whereNull('deleted_at')
    //             ->first();
    // }

    public function getMargenAttribute(){

        if ($this->precio_coste == 0) return null;

        return $this->importe_venta - $this->precio_coste;

    }


    // public static function totalLineasByAlb($id){

    //     $q = DB::table('albalins')
	// 	    ->select(DB::raw('iva_id,iva, SUM(unidades) AS unidades, SUM(importe_venta) importe_venta, SUM(precio_coste) AS precio_coste'))
	// 	    ->where('albaran_id', $id)
    //         ->groupby('iva_id','iva')
    //         ->get();

    //     $data = [
    //         'importe_venta' => 0,
    //         'precio_coste'  => 0,
    //         'unidades'      => 0,
    //         'base_iva'      => 0,
    //         'por_iva'       => 0,
    //         'iva'           => 0,
    //         'total'         => 0,
    //     ];

    //     $esRebu = false;
    //     foreach ($q as $row){
    //         if ($row->iva_id == 2){ // es iva rebu,
    //             $esRebu = true;
    //             //  no puede haber REBU y no REBU en un mismo albarán
    //             $data['base_iva']=+ ($row->importe_venta - $row->precio_coste);
    //             $data['por_iva'] = $row->iva;
    //         }else{
    //             $data['por_iva'] = $row->iva;
    //             $data['base_iva']=  $row->importe_venta;  // iva general
    //         }

    //         $data['unidades']+= $row->unidades;
    //         $data['importe_venta']+= $row->importe_venta;
    //         $data['precio_coste']+= $row->precio_coste;

    //     }

    //     if ($data['base_iva']){
    //         $data['iva'] = round($data['base_iva'] * $data['por_iva'] / 100, 2);
    //     }

    //     if ($esRebu)
    //         $data['total'] = round($data['importe_venta'],2);
    //     else
    //         $data['total'] = round($data['importe_venta'] + $data['iva'], 2);

    //     return $data;
    //     // return $q;
	// 	// return array('importe'=> round($q->importe,2),
	// 	// 		'iva'=> $q->iva,
	// 	// 		'irpf'=> $q->irpf,
	// 	// 		'poriva'=> $q->poriva,
	// 	// 		'porirpf'=> $q->porirpf,
	// 	// 		'base'=> round($q->base,2));

    // }

    public static function totalAlbaranByAlb($id){

        $q = DB::table('albalins')
            ->select(DB::raw(DB::getTablePrefix().'albalins.iva_id AS iva_id,'.
                             DB::getTablePrefix().'albalins.iva, rebu, SUM('.
                             DB::getTablePrefix().'albalins.unidades) AS unidades, SUM('.
                             DB::getTablePrefix().'albalins.importe_venta) importe_venta, SUM('.
                             DB::getTablePrefix().'albalins.precio_coste) AS precio_coste'))
            ->join('productos', 'albalins.producto_id', 'productos.id')
            ->join('ivas', 'productos.iva_id', 'ivas.id')
            ->where('albalins.albaran_id', $id)
            ->whereNull('albalins.deleted_at')
            ->groupBy('albalins.iva_id','albalins.iva','ivas.rebu')
            ->get();

        $data = [
            'importe_venta' => 0,
            'precio_coste'  => 0,
            'unidades'      => 0,
            'base_iva'      => 0,
            'desglose_iva'  => [],
            'iva'           => 0,
            'iva_rebu'      => 0,
            'total'         => 0,
        ];

        $rebu = false;
        $desglose_iva = array();
        foreach ($q as $row){
            if ($row->rebu){ // es iva rebu,
                $rebu = true;
                //  no puede haber REBU y no REBU en un mismo albarán
                $base_iva  = ($row->importe_venta - $row->precio_coste);
                $cuota_iva = round(($row->importe_venta - $row->precio_coste) * $row->iva / 100, 2);

            }else{
                $rebu = false;
                $base_iva   = $row->importe_venta;
                $cuota_iva = round($row->importe_venta * $row->iva / 100, 2);
            }

            $desglose_iva[] = [
                'id'        => $row->iva_id,
                'por_iva'   => $row->iva,
                'base_iva'  => $base_iva,
                'cuota_iva' => $cuota_iva,
                'rebu'      => $rebu
            ];

            $data['base_iva']+= $base_iva;

            if (!$rebu)
                $data['iva']+= $cuota_iva;
            else
                $data['iva_rebu']+= $cuota_iva;

            $data['unidades']+= $row->unidades;
            $data['importe_venta']+= $row->importe_venta;
            $data['precio_coste']+= $row->precio_coste;

        }

        $data['desglose_iva'] = $desglose_iva;

        // if ($rebu)
        //     $data['total'] = round($data['importe_venta'], 2);
        // else
            $data['total'] = round($data['importe_venta'] + $data['iva'], 2);


        return $data;

	}
}
