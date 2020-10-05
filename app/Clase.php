<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $fillable = [
        'nombre', 'grupo_id','peso','quilates','username', 'stockable'
    ];

    public function grupo()
    {
    	return $this->belongsTo(Grupo::class);
    }

    /**
     *
     * @return Array formateado para select Vuetify
     *
     */
    public static function selClases($grupo_id)
    {

        return Clase::select('id AS value', 'nombre AS text', 'peso', 'quilates','stockable')
            ->where('grupo_id', $grupo_id)
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selGrupoClase()
    {

        return DB::table('clases')
            ->join('grupos', 'grupos.id', '=', 'clases.grupo_id')
            ->select(DB::raw(DB::getTablePrefix().'clases.id AS value, CONCAT('.DB::getTablePrefix().'grupos.nombre," ",'.DB::getTablePrefix().'clases.nombre) AS text, quilates, stockable'))
            ->get();
    }

    public static function selGrupoClaseRebu()
    {

        return DB::table('clases')
            ->join('grupos', 'grupos.id', '=', 'clases.grupo_id')
            ->select(DB::raw(DB::getTablePrefix().'clases.id AS value, CONCAT('.DB::getTablePrefix().'grupos.nombre," ",'.DB::getTablePrefix().'clases.nombre) AS text, stockable'))
            ->where('rebu', true)
            ->get();
    }

}
