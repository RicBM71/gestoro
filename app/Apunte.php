<?php

namespace App;

use App\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Apunte extends Model
{
    protected $fillable = [
        'empresa_id','nombre', 'username', 'color'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EmpresaScope);
    }

      /**
     *
     * @return Array formateado para select Vuetify
     *
     */
    public static function selApuntes()
    {

        return Apunte::select('id AS value', 'nombre AS text')
            ->orderBy('nombre', 'asc')
            ->get();

    }

    public static function selColores(){

        $colores[]=['value' => 'red--text',  'text' => 'Rojo'];

        $colores[]=['text' => 'Rosa', 'value' => 'pink--text'];
        $colores[]=['text' => 'Morado', 'value' => 'purple--text'];
        $colores[]=['text' => 'Morado Oscuro', 'value' => 'deep-purple--text'];
        $colores[]=['text' => 'Azul Oscuro', 'value' => 'indigo--text'];
        $colores[]=['text' => 'Azul ', 'value' => 'blue--text'];
        $colores[]=['text' => 'Azul Claro', 'value' => 'light-blue--text'];
        $colores[]=['text' => 'Cyan', 'value' => 'cyan--text'];
        $colores[]=['text' => 'Verde Marino', 'value' => 'teal--text'];
        $colores[]=['text' => 'Verde Claro', 'value' => 'ligth-green--text'];
        $colores[]=['text' => 'Lima', 'value' => 'lime--text'];
        $colores[]=['text' => 'Amarillo', 'value' => 'yellow--text'];
        $colores[]=['text' => 'Ambar', 'value' => 'amber--text'];
        $colores[]=['text' => 'Naranja', 'value' => 'orange--text'];
        $colores[]=['text' => 'Naranja Oscuro', 'value' => 'deep-orange--text'];
        $colores[]=['text' => 'Marron', 'value' => 'brown--text'];
        $colores[]=['text' => 'Gris', 'value' => 'blue-grey--text'];
        $colores[]=['text' => 'Negro', 'value' => 'black--text'];

        return $colores;

    }

    // public static function selApuntesUser()
    // {

    //     return Apunte::select('id AS value', 'nombre AS text')
    //         ->where('id', '>', 30)
    //         ->orderBy('nombre', 'asc')
    //         ->get();
    // }


    // public static function scopeLibres($query){

    //     return $query->where('id', '>', 30);

    // }



}
