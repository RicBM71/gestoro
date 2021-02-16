<?php

namespace App\Http\Controllers\Ventas;

use PDF;
use App\Compra;
use App\Comline;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PrintRecuController extends Controller
{

    public function print($id){

        $file = false;

        $this->compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($id);

        // constrola si la compra está recuperada para poder imprimir como factura
        if ($this->compra->fase_id != 5 || $this->compra->factura <= 0 || $this->compra->factura==''){
            return redirect()->route('compra.print', ['id' => $id]);
        }
            //return abort(411,'La compra no está recuperada y/o facturada');;

        $this->lineasCompra = Comline::with('clase')->compraId($this->compra->id)->orderBy('id')->get();

        $empresa  = session()->get('empresa');

        ob_end_clean();

        $this->setPrepararFrmRecu($empresa);

        $this->frmRecu();

        PDF::Close();

        if ($file){
            if (file_exists(storage_path('compras'))==false)
                mkdir(storage_path('compras'), '0755');

            PDF::Output(storage_path('compras/recu'.$this->compra->albaran.'.pdf'), 'F');

        }
        else{
            PDF::Output('FR'.$this->compra->albaran.'.pdf','I');
        }

        PDF::reset();
    }

     /**
     *
     * Formulario de factura de recuperación de compras.
     *
     */
    private function frmRecu()    {


        PDF::AddPage();

        // cabecera cliente
        $this->setCabeceraClifrmRecu();
        //$this->setBodyComprafrmRecu();
        $this->setLineasComfrmRecu($this->lineasCompra);
        //$this->setFirmafrmRecu();

    }

     /**
     *
     * @param Model Cliente
     *
     */
    private function setCabeceraClifrmRecu(){

        if ($this->compra->factura == ""){
            $fecha =  getFecha($this->compra->fecha_compra);
            $num_doc = $this->compra->alb_ser;
        }else{
            $fecha =  getFecha($this->compra->fecha_factura);
            $num_doc = $this->compra->factura_compra;
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
        PDF::MultiCell(90, 5,  'NIF.: '.$this->compra->cliente->dni,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

        PDF::setXY(115,38);
        PDF::MultiCell(90, 5,  $this->compra->cliente->razon, '', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        PDF::setX(115);
        PDF::MultiCell(90, 5,  $this->compra->cliente->direccion,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        PDF::setX(115);
        PDF::MultiCell(90, 5,  $this->compra->cliente->cpostal.''.$this->compra->cliente->poblacion,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        if ($this->compra->cliente->poblacion != $this->compra->cliente->provincia){
            PDF::setX(115);
            PDF::MultiCell(90, 5,  $this->compra->cliente->provincia,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        }

    }

    /**
     *
     * @param Model Albalins
     *
     */
    private function setLineasComfrmRecu($lineas) {

        $this->cabeLin();

        $total=0;
		foreach ($lineas as $row) {

            $txt = $row->concepto;
			if ($row->quilates != null){
				$txt.= ' '.$row->quilates.'K';
			}

			if ($row->colores != null)
				$txt.= ' '.$row->colores;

			if ($row->grabaciones != null)
                $txt.= " (".$row->grabaciones.") ";

            $importe = ($this->compra->factura == '') ? $row->importe : $row->importe_venta;


			$total += $importe;

            $h = $alto=PDF::getStringHeight(160,$txt);

            $y = round(PDF::getY());

            // $txt .= " **PW: ".$y." H: ".$h;
            if ($y+$h >= 210){
                PDF::AddPage();
                $this->setCabeceraClifrmRecu();
                $y = round(PDF::getY());
                $this->cabeLin();
            }

            PDF::MultiCell($w=160, $h, $txt, $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            PDF::MultiCell($w=20, $h, getDecimal($importe), $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

        }

        $h=2;
        PDF::MultiCell($w=160, $h, '', $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=20, $h, '', $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        $h=6;
        PDF::MultiCell($w=160, $h, '', $border='T', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell($w=20, $h, '', $border='T', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

        PDF::SetFont('helvetica', 'R', 10, '', false);

        PDF::MultiCell(110, 8, '', '', '', 0, 0, '', '', true);
        PDF::MultiCell(43, 8, 'TOTAL', 0, 'R', 1, 0, '', '', true, 0, false, true, 8, 'M');
        PDF::MultiCell(1, 8, '', 0, 'R', 0, 0, '', '', true, 0, false, true, 8, 'M');
        PDF::MultiCell(26, 8, getCurrency($total), 0, 'R', 1, 1, '', '', true, 0, false, true, 8, 'M');

        PDF::SetFont('helvetica', 'R', 9, '', false);

        PDF::MultiCell(110, 8, '* IVA: Sujeto a régimen especial de bienes usados.', '', '', 0, 1, '', '', true);

    }

    private function cabeLin(){
        $txt = "CONCEPTO";
		$imp = "IMPORTE";

		PDF::SetFont('helvetica', 'RB', 8, '', false);
        PDF::Ln();
        PDF::Ln();
		PDF::Cell(160, 6, $txt, 'TRB', 0, 'C');
        PDF::Cell(20, 6, $imp, 'TB', 0, 'C');
        // PDF::MultiCell(160, 4,$txt, 'TRB', 'C', 0, 0, '', '', true);
        // PDF::MultiCell(20, 4,$imp, 'TB', 'C', 0, 1, '', '', true);
        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 8, '', false);
        PDF::MultiCell(160, 2,"", 'R', '', 0, 0, '', '', true);
        PDF::MultiCell(20, 2,"", '', '', 0, 1, '', '', true);
    }

     /**
     *
     * @param Model Empresa
     *
     */
    private function setPrepararFrmRecu($empresa){

        PDF::setHeaderCallback(function($pdf) {

            if (session('empresa')->img_logo > ""){
                $pdf->setImageScale(1.60);

                // $pdf->SetXY(14, 5);
                // $pdf->Image($imagen, '', '', 42, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
                //Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

                $f = str_replace('storage', 'public', session()->get('empresa')->img_logo);

                $file = '@'.(Storage::get($f));
                $pdf->setJPEGQuality(75);
                $pdf->Image($file, $x='5', $y='2', $w=0, $h=25, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

            }

            $pdf->SetFont('helvetica', 'B', 16, '', false);


            $txt = $this->compra->factura == "" ? "NOTA DE VENTA" : "FACTURA";

            $pdf->SetXY(4, 5);
            $pdf->Write($h=0, $txt, $link='', $fill=0, $align='R', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            $pdf->SetFont('helvetica', 'R', 9, '', false);

            $y = 25;
            $pdf->SetXY(10, $y);
            $pdf->Write($h=0,  session('empresa')->razon, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(10, $y+=5);
            $pdf->Write($h=0,  session('empresa')->direccion, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(10, $y+=5);
            $pdf->Write($h=0,  session('empresa')->cpostal.' '.session('empresa')->poblacion, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(10, $y+=5);
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

            $pdf->SetFont('helvetica', 'R', 8);
            $pdf->MultiCell($w=190, $h, $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);


        });


                // set document information
        PDF::SetCreator(PDF_CREATOR);
        PDF::SetAuthor($empresa->nombre);
        PDF::SetTitle('Factura');
        PDF::SetSubject('');

        // set default header data
        PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        //PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        PDF::SetFooterMargin(32);

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
