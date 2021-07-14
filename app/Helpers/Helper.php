<?php

use App\Whatsapp;
use Carbon\Carbon;

function getDecimal($valor, $dec=2){
    return number_format($valor,$dec, ",", ".");
}

function getCurrency($valor,$dec=2, $currency="€"){
    return number_format($valor,$dec, ",", ".")." ".$currency;
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

    return substr($iban_print,0,-1);

}

function getTelefono($n){

    if (strlen($n) != 9) return $n;

    if ($n[0]=='6' || $n[0]=='7')
        return substr($n,0,3).' '.substr($n,3,3).' '.substr($n,6,3);
    else
        return substr($n,0,2).' '.substr($n,2,3).' '.substr($n,5,2).' '.substr($n,7,2);

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

function esGestor(){
    return auth()->user()->hasRole('Gestor');
}

function hasEdtInt(){
    return auth()->user()->hasPermissionTo('edtcom');
}

function hasEdtAlb(){
    return auth()->user()->hasPermissionTo('edtalb');
}

function hasAbono(){
    return auth()->user()->hasPermissionTo('abono');
}

function hasEdtPro(){
    return auth()->user()->hasPermissionTo('edtpro');
}

function hasMail(){
    return auth()->user()->hasPermissionTo('mail');
}


function hasHardDel(){
    return auth()->user()->hasPermissionTo('harddel');
}

function hasWhatsApp(){
    return auth()->user()->hasPermissionTo('whatsapp');
}

function hasEdtCaj(){
    return auth()->user()->hasPermissionTo('edtcaj');
}

function hasDelCom(){
    return auth()->user()->hasPermissionTo('delcom');
}

function hasDelAlb(){
    return auth()->user()->hasPermissionTo('delalb');
}

function hasDelCaj(){
    return auth()->user()->hasPermissionTo('delcaj');
}

function hasExcel(){
    return auth()->user()->hasPermissionTo('excel');
}

function hasConsultas(){
    return auth()->user()->hasPermissionTo('consultas');
}


function hasReaCom(){
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

function hasECommerce(){
    return auth()->user()->hasPermissionTo('ecommerce');
}

function hasSepa(){
    return auth()->user()->hasPermissionTo('gensepa');
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

function importeLinea($data){

    $importe = round($data['unidades'] * $data['importe_unidad'], 2);

    return round($importe - ($importe * $data['descuento'] / 100 ), 2);

}


function getWhatsAppRenova($compra){

    if (strlen($compra->cliente->tfmovil) != 9) return false;

    if ($compra->tipo_id == 1 && $compra->fase_id == 4){
        $msg = formatMsg($compra);
    }else{
        $msg="Hola%20".mb_convert_case(trim($compra->cliente->nombre),MB_CASE_TITLE, "UTF-8");
    }

    $ws="https://api.whatsapp.com/send?phone=34".$compra->cliente->tfmovil."&text=".$msg;

    return $ws;

}

function formatMsg($compra){

    $plantilla = Whatsapp::firstOrFail();

    $dt = Carbon::parse($compra->fecha_renovacion);

    $fecha = $dt->isoFormat('DD/MM/YYYY');

    //$fecha = $dt->format('l j \\de F \\a \\las H:i');
    $fecha = str_replace(' ', '%20', $fecha);

    if (session('empresa')->telefono1 > '' && session('empresa')->telefono2 > '')
        $tfs = "de los teléfonos ".session('empresa')->telefono1."-".session('empresa')->telefono2;
    else
        $tfs = "del teléfono ".session('empresa')->telefono1;

        // nombre
    $plantilla = str_replace('%n',mb_convert_case(trim($compra->cliente->nombre),MB_CASE_TITLE, "UTF-8"), $plantilla->texto);
        // fecha
    $plantilla = str_replace('%f',$fecha, $plantilla);
        // lote
    $plantilla = str_replace('%l',$compra->alb_ser, $plantilla);
        //teléfonos
    $plantilla = str_replace('%t',$tfs, $plantilla);

    $texto = str_replace(' ', '%20', $plantilla);

    return $texto;
}
