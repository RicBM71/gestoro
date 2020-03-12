<?php

use Carbon\Carbon;

function getDecimal($valor, $dec=2){
    return number_format($valor,$dec, ",", ".");
}

function getCurrency($valor, $currency="€"){
    return number_format($valor,2, ",", ".")." ".$currency;
}

function getDecimalExcel($valor, $dec=2){
    return round($valor, $dec);
}

function getQuilates($q){

    return $q > 0 ? $q.' K': null;

}

function getFecha($value)
{

    if (is_null($value)) return null;

    return Carbon::parse($value)->format('d/m/Y');

}

function getEjercicio($fecha){
    return Carbon::parse($fecha)->format('Y');
}

function getIbanPrint($iban){

    $iban_print = '';

    $iban = str_split($iban,4);

    foreach ($iban as $e){
        $iban_print .= $e.' ';
    }

    return $iban_print;

}


function sumarDiasAFecha($fecha, $dias){

    //$fecha = fechaAMysql($fecha);

    $fecha_tope = date("Y-m-d", strtotime("$fecha + $dias days"));

    //$fecha_tope = date('Y-m-d',$fecha + ($dias *24*60*60));
    return $fecha_tope;
}

function setImporteGr($peso_gr,$importe){
    return $peso_gr != 0 ? round($importe / $peso_gr, 2) : 0;
}

function esRoot(){
    return auth()->user()->hasRole('Root');
}

function esAdmin(){
    return auth()->user()->hasRole('Admin');
}

function esSupervisor(){
    return (auth()->user()->hasRole('Supervisor') || auth()->user()->hasRole('Admin'));
}

function esGestor(){
    return auth()->user()->hasRole('Gestor');
}


function hasHardDel(){
    return auth()->user()->hasPermissionTo('harddel');
}

function hasReabreCompras(){
    return auth()->user()->hasPermissionTo('reacom');
}

function hasScan(){
    return auth()->user()->hasPermissionTo('scan');
}

function hasUsers(){
    return auth()->user()->hasPermissionTo('userr');
}

function hasDesLoc(){
    return auth()->user()->hasPermissionTo('desloc');
}



function esPropietario($obj)
{
    return ($obj->username == auth()->user()->username && Carbon::today()->format('Y-m-d')== Carbon::parse($obj->created_at)->format('Y-m-d'))
         ? true : false;
}
/**
 * @param integer $ejercicio
 * @param integer $trimestre
 * @return array ['d','h']
 */
function trimestre($ejercicio,$trimestre){

    $m = (3 * $trimestre) - 2;

    return [
        'd' => Carbon::parse($ejercicio.'-'.$m.'-01')->startOfQuarter()->format('Y-m-d'),
        'h' => Carbon::parse($ejercicio.'-'.$m.'-01')->endOfQuarter()->format('Y-m-d')
    ];

    //h 3x1 - 2

    // echo Carbon::parse('2019-01-01')->endOfQuarter()->format('Y-m-d');
    // echo Carbon::parse('2019-04-01')->endOfQuarter();
    // echo Carbon::parse('2019-07-01')->endOfQuarter();
    // echo Carbon::parse('2019-10-01')->endOfQuarter();
}

function totalAlbalin($data){

    $t = 0;
    foreach ($data as $row){
        $t =+ $row['importe_venta'];
    }

    return $t;

}
