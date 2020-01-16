<?php

namespace App\Http\Controllers\Ventas;


use PDF;
use App\Iva;
use App\Cobro;

use App\Fpago;
use App\Cuenta;
use App\Motivo;
use App\Albalin;
use App\Albaran;
use App\Garantia;
use Carbon\Carbon;
use App\Jobs\SendFactura;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class PrintAlbController extends Controller
{

    protected $albaran;
    protected $hay_productos_con_garantia = false;

    public function mail(Request $request, Albaran $albarane){

        $albaran = Albaran::with(['cliente'])->findOrFail($albarane->id);

        if ($albaran->cliente->email=='')
            return response('El cliente no tiene email configurado', 403);
        elseif (session('empresa')->email=='')
            return response('Configurar email empresa', 403);

        $this->print($albaran->id, true);

        $data = [
            'razon'=> session('empresa')->razon,
            'from'=> session('empresa')->email,
            'msg' => null,
            'albaran' => $albaran
        ];

        // con esto previsualizamos el mail
        //return new Factura($data);

        dispatch(new SendFactura($data));

        if ($albaran->factura > 0){
            $data_alb['fecha_notificacion'] =  Carbon::now();
            $data_alb['username']   = session('username');;

            $albarane->update($data_alb);
        }

        if (request()->wantsJson())
            return [
                'albaran'=> Albaran::with(['cliente','tipo','fase','fpago','cuenta','procedencia','motivo'])->findOrFail($albaran->id),
                'message' => 'Mail enviado'];

    }

    public function print($id, $file=false){

        $this->albaran = Albaran::with(['cliente','tipo','fase'])->findOrFail($id);

        // constrola si la compra está recuperada para poder imprimir como factura
        // if ($this->albaran->fase_id != 5 || $this->albaran->factura <= 0 || $this->albaran->factura==''){
        //     return redirect()->route('albaran.print', ['id' => $id]);
        // }
            //return abort(411,'La compra no está recuperada y/o facturada');;

        $this->lineasAlbaran = Albalin::with(['producto.clase','producto.garantia','producto.iva'])->albaranId($this->albaran->id)->orderBy('id')->get();

        $empresa  = session()->get('empresa');

        ob_end_clean();

        $this->setPrepararFormulario($empresa);

        $this->frmAlbaran();

        PDF::Close();

        if ($file){
            if (file_exists(storage_path('albaranes'))==false)
                mkdir(storage_path('albaranes'), '0755');

            if ($this->albaran->factura > 0)
                PDF::Output(storage_path('albaranes/'.$this->albaran->fac_ser.'.pdf'), 'F');
            else
                PDF::Output(storage_path('albaranes/'.$this->albaran->alb_ser.'.pdf'), 'F');

        }
        else{
            PDF::Output('FR'.$this->albaran->albaran.'.pdf','I');
        }

        PDF::reset();
    }

     /**
     *
     * Formulario de factura de recuperación de compras.
     *
     */
    private function frmAlbaran()    {


        PDF::AddPage();

        // cabecera cliente
        $this->setCabeceraAlbaran();

        if ($this->albaran->tipo_id == 5)
            $this->impPiezaTaller();

        $this->printAlbalin($this->lineasAlbaran);

        if ($this->albaran->tipo_id == 3){
            if ($this->albaran->factura == "")
                $this->PagosCliente();
            if ($this->albaran->motivo_id > 0)
                $this->impMotivo();
            else
                $this->pieRebu();
        }else{
            if ($this->albaran->motivo_id > 0)
                $this->impMotivo();
            $this->formaDePago();
        }

        if ($this->hay_productos_con_garantia->count() > 0){
            $this->garantia($this->lineasAlbaran);
        }

        if ($this->albaran->tipo_id != 5 && $this->albaran->notas_ext > "")
            $this->impNotas();

    }

     /**
     *
     * @param Model Cliente
     *
     */
    private function setCabeceraAlbaran(){

        if ($this->albaran->factura == ""){
            $fecha =  getFecha($this->albaran->fecha_albaran);
            $num_doc = $this->albaran->alb_ser;
        }else{
            $fecha =  getFecha($this->albaran->fecha_factura);
            $num_doc = $this->albaran->fac_ser;
        }

        //PDF::SetFillColor(235, 235, 235);
        PDF::SetFillColor(215, 235, 255);

        PDF::SetFont('helvetica', 'R',11, '', false);
        PDF::setXY(122,14);
        PDF::MultiCell(40, 8, $fecha,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

        PDF::setXY(165,14);
        PDF::MultiCell(36, 8,  $num_doc,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

        PDF::SetFont('helvetica', '', 9, '', false);
        PDF::setXY(115,30);
        PDF::MultiCell(90, 5,  'NIF.: '.$this->albaran->cliente->dni,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

        PDF::setXY(115,38);
        PDF::MultiCell(90, 5,  $this->albaran->cliente->razon, '', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        PDF::setX(115);
        PDF::MultiCell(90, 5,  $this->albaran->cliente->direccion,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        PDF::setX(115);
        PDF::MultiCell(90, 5,  $this->albaran->cliente->cpostal.' '.$this->albaran->cliente->poblacion,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        if ($this->albaran->cliente->poblacion != $this->albaran->cliente->provincia){
            PDF::setX(115);
            PDF::MultiCell(90, 5,  $this->albaran->cliente->provincia,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        }

    }

    /**
     *
     * @param Model Albalins
     *
     */
    private function printAlbalin($lineas) {

        $this->cabeLin();

        $totales = Albalin::totalAlbaranByAlb($this->albaran->id);

        $this->hay_productos_con_garantia=collect([]);
		foreach ($lineas as $row) {

            $txt_garantia = $leyenda = null;
            if ($row->producto->garantia_id > 0){
                if ($this->hay_productos_con_garantia->search($row->producto->garantia_id)===false)
                    $this->hay_productos_con_garantia->add($row->producto->garantia_id);

                $txt_garantia = '('.$row->producto->garantia_id.') ';
                $leyenda = ' Garantía: '.$row->producto->meses_garantia.' meses. Última revisión: '.getFecha($row->producto->fecha_ultima_revision).'.';
            }

            $txt = $txt_garantia.$row->producto->nombre;
			if ($row->producto->quilates != null){
				$txt.= ' '.$row->producto->quilates.'K';
            }
            $txt.=$leyenda;

            if ($row->producto->caracteristicas != '')
                $txt.=(' '.$row->producto->caracteristicas);

            if ($row->notas != ''){
                $txt.=' ('.$row->notas.')';
            }

            $h = $alto=PDF::getStringHeight(95,$txt);
            $h = $h + 2;

            $y = round(PDF::getY());

            // $txt .= " **PW: ".$y." H: ".$h;
            if ($y+$h >= 210){
                PDF::AddPage();
                $this->setCabeceraAlbaran();
                $y = round(PDF::getY());
                $this->cabeLin();
            }

            PDF::MultiCell($w=20, $h, $row->producto->referencia, $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            PDF::MultiCell($w=96, $h, $txt, $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

            if ($this->albaran->factura > 0){
                if ($row->iva == 0)
                    PDF::MultiCell($w=12, $h, '('.$row->iva_id.') '.getDecimal($row->iva), $border='R', $align='R', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
                else
                    if ($row->producto->iva->rebu)
                        PDF::MultiCell($w=12, $h, getDecimal($row->iva).'*', $border='R', $align='C', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
                    else
                        PDF::MultiCell($w=12, $h, getDecimal($row->iva), $border='R', $align='R', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            }else{
                PDF::MultiCell($w=12, $h,"", $border='R', $align='R', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            }
            PDF::MultiCell($w=18, $h, getDecimal($row->unidades), $border='R', $align='R', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            $dec = ($this->albaran->tipo_id == 3) ? 2 : 2;
            PDF::MultiCell($w=20, $h, getDecimal($row->importe_unidad, $dec), $border='R', $align='R', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            PDF::MultiCell($w=20, $h, getDecimal($row->importe_venta), $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

        }

        $h=2;

        PDF::MultiCell($w=20, $h, '', $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=96, $h, '', $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=12, $h, '', $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=18, $h, '', $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=20, $h, '', $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=20, $h, '', $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        $h=6;
        PDF::MultiCell($w=20, $h, '', $border='T', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=96, $h, '', $border='T', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=12, $h, '', $border='T', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=18, $h, '', $border='T', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=20, $h, '', $border='T', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=20, $h, '', $border='T', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

        PDF::SetFont('helvetica', 'R', 10, '', false);

        PDF::MultiCell(118, 8, '', '', '', 0, 0, '', '', true);
        PDF::MultiCell(40, 8, 'TOTAL', 0, 'C', 1, 0, '', '', true, 0, false, true, 8, 'M');
        PDF::MultiCell(3, 8, '', 0, 'R', 0, 0, '', '', true, 0, false, true, 8, 'M');
        if ($this->albaran->factura > 0)
            PDF::MultiCell(26, 8, getCurrency($totales['total']), 0, 'R', 1, 1, '', '', true, 0, false, true, 8, 'M');
        else
            PDF::MultiCell(26, 8, getCurrency($totales['importe_venta']), 0, 'R', 1, 1, '', '', true, 0, false, true, 8, 'M');


        PDF::SetFont('helvetica', 'R', 8, '', false);
        if ($this->albaran->tipo_id == 3){

            $iva = Iva::findOrFail(2);

            PDF::MultiCell(110, 8, $iva->leyenda, '', '', 0, 1, '', '', true);
        }
        //else{

        if ($this->albaran->factura > 0){
            $linea = 0;
            foreach ($totales['desglose_iva'] as $tipos_iva){
                if ($tipos_iva['rebu'] == true)
                    continue;

                $linea++;
                if ($linea == 1){
                    PDF::MultiCell(24, 6,'Base IVA', 'RB', 'C', 0, 0, '', '', true);
                    PDF::MultiCell(16, 6, '% IVA', 'RB', 'C', 0, 0, '', '', true);
                    PDF::MultiCell(24, 6, 'Cuota', 'B', 'C', 0, 1, '', '', true);
                }
                PDF::MultiCell(24, 6, getDecimal($tipos_iva['base_iva']), 'R', 'R', 0, 0, '', '', true);
                PDF::MultiCell(16, 6, getDecimal($tipos_iva['por_iva']), 'R', 'R', 0, 0, '', '', true);
                PDF::MultiCell(24, 6, getDecimal($tipos_iva['cuota_iva']), '', 'R', 0, 1, '', '', true);


            }

            PDF::Ln();

            foreach ($totales['desglose_iva'] as $tipos_iva){
                if ($tipos_iva['cuota_iva'] == 0){
                    $iva = Iva::findOrFail($tipos_iva['id']);
                    PDF::MultiCell(140, 6, '* ('.$iva->id.') '.$iva->leyenda, '', 'L', 0, 1, '', '', true);
                }
            }
        }


    }

    private function cabeLin(){

		PDF::SetFont('helvetica', 'RB', 8, '', false);
        PDF::Ln();
        PDF::Ln();
        PDF::Cell(20, 6, 'REFERENCIA', 'TRB', 0, 'C');
        PDF::Cell(96, 6, 'PRODUCTO', 'TRB', 0, 'C');
        PDF::Cell(12, 6, 'IVA', 'TRB', 0, 'C');
        PDF::Cell(18, 6, 'Uds.', 'TRB', 0, 'C');
        PDF::Cell(20, 6, 'Imp. Ud.', 'TRB', 0, 'C');
        PDF::Cell(20, 6, 'IMPORTE', 'TB', 0, 'C');
        // PDF::MultiCell(160, 4,$txt, 'TRB', 'C', 0, 0, '', '', true);
        // PDF::MultiCell(20, 4,$imp, 'TB', 'C', 0, 1, '', '', true);
        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 8, '', false);
        PDF::MultiCell(20, 2,"", 'R', '', 0, 0, '', '', true);
        PDF::MultiCell(96, 2,"", 'R', '', 0, 0, '', '', true);
        PDF::MultiCell(12, 2,"", 'R', '', 0, 0, '', '', true);
        PDF::MultiCell(18, 2,"", 'R', '', 0, 0, '', '', true);
        PDF::MultiCell(20, 2,"", 'R', '', 0, 0, '', '', true);
        PDF::MultiCell(20, 2,"", '', '', 0, 1, '', '', true);
    }

    private function pagosCliente(){

        $data = Cobro::with('fpago')->albaranId($this->albaran->id)->get();

        if ($data->count() == 0){
            return;
        }

        PDF::SetFont('helvetica', 'R', 9, '', false);
        if ($data != null){
            PDF::MultiCell(80, 6, 'Pagos a Cuenta', 'B', 'L', 0, 1, '', '', true);
        }

        foreach ($data as $cobro){
            PDF::MultiCell(20, 5, getFecha($cobro->fecha), 'R', 'R', 0, 0, '', '', true);
            PDF::MultiCell(40, 5, $cobro->fpago->nombre, 'R', 'L', 0, 0, '', '', true);
            PDF::MultiCell(20, 5, getDecimal($cobro->importe), '', 'R', 0, 1, '', '', true);

        }

    }

    private function pieRebu(){

        PDF::Ln();

        PDF::SetFont('helvetica', 'RI', 9, '', false);
        PDF::MultiCell(188, 6, session('parametros')->pie_rebu1, '', 'L', 0, 1, '', '', true);

        if ($this->albaran->fase_id == 10){
            $txt = "Los apartados deberán de retirarse en un plazo máximo de 3 MESES desde la fecha de reserva. Superado ese periodo la ".
                   "empresa podrá disponer de pleno derecho de los objetos reseñados y supondrá la pérdida de la señal abonada.";
            PDF::MultiCell(188, 6, $txt, '', 'L', 0, 1, '', '', true);
        }
    }

    private function formaDePago(){

        if ($this->albaran->factura == "")
            return;

        $txt_fpago = "FORMA DE PAGO: ";
        $fpago = Fpago::findOrfail($this->albaran->fpago_id);
        if ($fpago->id == 2 && !is_null($this->albaran->cuenta_id)){
            $cuenta = Cuenta::findOrfail($this->albaran->cuenta_id);
            $txt_fpago .= strtoupper($fpago->nombre).' IBAN '.getIbanPrint($cuenta->iban);
        }else{
            $txt_fpago .= strtoupper($fpago->nombre);
        }

        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::MultiCell(188, 8, $txt_fpago, '', 'L', 1, 1, '', '', true, 0, false, true, 8, 'M');

    }

    private function impMotivo(){

        $motivo = Motivo::findOrFail($this->albaran->motivo_id);

        $origen = Albaran::findOrFail($this->albaran->albaran_abonado_id);

        if ($origen->factura > 0)
            $txt_motivo = "ANULA LA FACTURA: ".$origen->fac_ser.' '.getFecha($origen->fecha_factura).' Motivo: '.$motivo->nombre;
        else
            $txt_motivo = "ANULA EL ALBARÁN: ".$origen->alb_ser.' '.getFecha($origen->fecha_albaran).' Motivo: '.$motivo->nombre;

        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 9, '', false);
        PDF::MultiCell(188, 8, $txt_motivo, '', 'L', 1, 1, '', '', true, 0, false, true, 8, 'M');

    }

    private function garantia($lineas){

        PDF::SetFont('helvetica', 'I', 9, '', false);
        foreach ($this->hay_productos_con_garantia as $garantia_id){
            $garantia = Garantia::find($garantia_id);

                $leyenda_garantia = '('.$garantia_id.') '.$garantia->leyenda;

                $h = PDF::getStringHeight(188, $leyenda_garantia);

                PDF::MultiCell(188, $h, $leyenda_garantia."\n", '', 'J', 0, 1, '', '', true, 0, false, true, $h, 'M');
        }
    }

    private function impNotas(){

        PDF::Ln();

        PDF::SetFont('helvetica', 'I', 9, '', false);
        PDF::MultiCell(188, 8, $this->albaran->notas_ext, '', 'L', 0, 1, '', '', true, 0, false, true, 8, 'M');

    }

    private function impPiezaTaller(){

        PDF::Ln();

        PDF::SetFont('helvetica', 'I', 9, '', false);
        PDF::MultiCell(188, 16, $this->albaran->notas_ext, '', 'L', 1, 1, '', '', true, 0, false, true, 16, 'M');
        PDF::MultiCell(188, 2, '', '', 'L', 1, 1, '', '', true, 0, false, true, 2, 'M');
    }



     /**
     *
     * @param Model Empresa
     *
     */
    private function setPrepararFormulario($empresa){

        PDF::setHeaderCallback(function($pdf) {

            if (session('empresa')->img_logo > ""){
                $pdf->setImageScale(1.60);

                // $pdf->SetXY(14, 5);
                // $pdf->Image($imagen, '', '', 42, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
                //Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

                $f = str_replace('storage', 'public', session()->get('empresa')->img_logo);

                $file = '@'.(Storage::get($f));
                $pdf->setJPEGQuality(75);
                $pdf->Image($file, $x='5', $y='2', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

            }

            $pdf->SetFont('helvetica', 'B', 16, '', false);


            if ($this->albaran->factura == "")
                $txt = "ALBARÁN";
            else{
                if (strlen($this->albaran->cliente->dni) == 4)
                    $txt = $this->albaran->tipo_factura <> 3 ? "FACTURA SIMPLIFICADA" : "SIMPLIFICADA RECTIFICATIVA";
                else
                    $txt = $this->albaran->tipo_factura <> 3 ? "FACTURA" : "FACTURA RECTIFICATIVA";
            }

            $pdf->SetXY(4, 5);
            $pdf->Write($h=0, $txt, $link='', $fill=0, $align='R', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            $pdf->SetFont('helvetica', 'R', 9, '', false);

            $y = 25;
            $pdf->SetXY(16, $y);
            $pdf->Write($h=0,  session('empresa')->razon, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(16, $y+=5);
            $pdf->Write($h=0,  session('empresa')->direccion, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(16, $y+=5);
            $pdf->Write($h=0,  session('empresa')->cpostal.' '.session('empresa')->poblacion, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(16, $y+=5);
            $pdf->Write($h=0,  'CIF.: '.session('empresa')->cif, '', 0, 'L', true, 0, false, true, 0);

            //$pdf->MultiCell(34, 157, session('empresa')->razon, 0, 'L', 0, 0, '', '', true);
            //$pdf->MultiCell(70, 15, session('empresa')->direccion, 0, 'L', 0, 0, '', '', true);
            //$pdf->MultiCell(70, 25, session('empresa')->provincia, 0, 'R', 0, 0, '', '', true);





        });

        PDF::setFooterCallback(function($pdf) {

            PDF::SetFont('helvetica', 'R', 6);

            $html='En cumplimiento al Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo, de 27 de abril de '.
                    '2016, relativo a la protección de las personas físicas en lo que respecta al tratamiento de datos personales y a la libre '.
                    'circulación de estos datos SE INFORMA: Los datos de carácter personal solicitados y facilitados por usted, son incorporados a un fichero de '.
                    'titularidad privada cuyo responsable y único destinatario es %e. Solo serán solicitados aquellos datos estrictamente necesarios '.
                    'para prestar adecuadamente el servicio.'.
                    'Todos los datos recogidos cuentan con el compromiso de confidencialidad exigido por la normativa, '.
                    'con las medidas de seguridad establecidas legalmente, y bajo ningún concepto son cedidos o tratados '.
                    'por terceras personas, físicas o jurídicas, sin el previo consentimiento del cliente. '.
                    'Puede ejercitar los derechos de acceso, rectificación, cancelación, oposición, limitación y '.
                    'portabilidad indicándolo por escrito a %e '.session()->get('empresa')->direccion.' '.session()->get('empresa')->cpostal.' '.
                    session()->get('empresa')->poblacion.".\n";

            $html = str_replace('%e', session()->get('empresa')->nombre, $html);


            //$this->Write($h=0, $html, $link='', $fill=0, $align='J', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
            PDF::Write($h=0, $html, '', 0, 'J', true, 0, false, true, 0);

            PDF::Ln();
            PDF::SetFont('helvetica', 'R', 8);

            $pdf->Write($h=0, session()->get('empresa')->txtpie1, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
            $pdf->Write($h=0, session()->get('empresa')->txtpie2, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            // $pdf->SetFont('helvetica', 'R', 8);
            // $pdf->MultiCell($w=190, $h, $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);


        });


                // set document information
        PDF::SetCreator(PDF_CREATOR);
        PDF::SetAuthor($empresa->nombre);
        PDF::SetTitle('Albarán/Factura');
        PDF::SetSubject('');

        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        //PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetMargins(13, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        //PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        PDF::SetFooterMargin(34);

        // set auto page breaks
        PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            PDF::setLanguageArray($l);
        }

        // ---------------------------------------------------------

    }



}
