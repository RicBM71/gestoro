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
use App\Empresa;
use App\Garantia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PrintHojaTallerController extends Controller
{

    protected $albaran;

    public function print($id){

        $file = false;
        $this->albaran = Albaran::with(['cliente','tipo','taller'])->findOrFail($id);

        // constrola si la compra está recuperada para poder imprimir como factura
        // if ($this->albaran->fase_id != 5 || $this->albaran->factura <= 0 || $this->albaran->factura==''){
        //     return redirect()->route('albaran.print', ['id' => $id]);
        // }
            //return abort(411,'La compra no está recuperada y/o facturada');;


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
            PDF::Output('HT'.$this->albaran->albaran.'.pdf','I');
        }

        PDF::reset();
    }

     /**
     *
     * Formulario de factura de recuperación de compras.
     *
     */
    private function frmAlbaran()    {


        PDF::AddPage('L', 'A4');

        // cabecera cliente
        $this->setCabeceraAlbaran();

        $this->printAlbalin();

        $this->pieDibujo();

    }

     /**
     *
     * @param Model Cliente
     *
     */
    private function setCabeceraAlbaran(){


        $fecha =  getFecha($this->albaran->fecha_albaran);
        $num_doc = $this->albaran->alb_ser;

        PDF::SetFillColor(215, 235, 255);

        PDF::SetFont('helvetica', 'R',11, '', false);

        if ($this->albaran->express){
            PDF::setXY(10,14);
            PDF::MultiCell(38, 8,  'Servicio Express','', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
            PDF::setXY(160,14);
            PDF::MultiCell(38, 8,  'Servicio Express','', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
        }

        PDF::setXY(56,14);
        PDF::MultiCell(42, 8, $fecha,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

        PDF::setXY(101,14);
        PDF::MultiCell(38, 8,  $num_doc,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

        PDF::setXY(206,14);
        PDF::MultiCell(40, 8, $fecha,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

        PDF::setXY(250,14);
        PDF::MultiCell(36, 8,  $num_doc,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);


        PDF::Ln();
        PDF::SetFont('helvetica', 'BR', 10, '', false);
        PDF::MultiCell(92, 5, $this->albaran->taller->nombre,'', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(55, 5,'','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::SetFont('helvetica', 'BR', 10, '', false);
        PDF::MultiCell(92, 5,$this->albaran->taller->nombre,'', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);

        try {
            //code...
            $procedencia = Empresa::findOrfail($this->albaran->procedencia_empresa_id);

            PDF::Ln();
            PDF::SetFont('helvetica', 'R', 10, '', false);
            PDF::MultiCell(48, 5, 'Entrega: '.getFecha($this->albaran->fecha_notificacion),'', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(80, 5, $procedencia->nombre,'', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);

            PDF::MultiCell(20, 5,'','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(48, 5, 'Entrega: '.getFecha($this->albaran->fecha_notificacion),'', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(92, 5, $procedencia->nombre,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        } catch (\Exception $e) {

            PDF::Ln();
            PDF::SetFont('helvetica', 'R', 10, '', false);
            PDF::MultiCell(48, 5, 'Entrega: '.getFecha($this->albaran->fecha_notificacion),'', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(80, 5, 'n/d','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);

            PDF::MultiCell(20, 5,'','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(48, 5, 'Entrega: '.getFecha($this->albaran->fecha_notificacion),'', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(92, 5, 'n/d','', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        }


        PDF::Ln();
        PDF::SetFont('helvetica', 'I', 8, '', false);
        //PDF::setXY(5,30);
        PDF::MultiCell(132, 5,'Descripción de la pieza ','LTR', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(16, 5,'','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(132, 5,'Descripción de la pieza ','LTR', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

        if ($this->albaran->notas_ext == null)
            $this->albaran->notas_ext = 'Sin especificar!';
        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::MultiCell(132, 30, $this->albaran->notas_ext, $border='LRB', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell(16, 5,'','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(132, 30, $this->albaran->notas_ext, $border='LRB', $align='L', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

    }

    /**
     *
     * @param Model Albalins
     *
     */
    private function printAlbalin() {

        $this->cabeLin();

        //return;

        $lineas = Albalin::with(['producto' => function ($query) {
                                      $query->withTrashed();},
                                'producto.clase','producto.garantia','producto.iva'])->albaranId($this->albaran->id)->orderBy('id')->get();

		foreach ($lineas as $row) {


            $nombre = $row->producto->referencia.' - '.$row->producto->nombre.' '.$row->notas;
            $h = PDF::getStringHeight(100,$nombre);

            PDF::SetFont('helvetica', 'R', 8, '', false);
            PDF::MultiCell(112, $h,$nombre, 'RL', '', 0, 0, '', '', true);
            PDF::MultiCell(20, $h,getDecimal($row->precio_coste), 'R', 'R', 0, 0, '', '', true);
            PDF::Cell(16, 6, '', '', 0, 'C');
            PDF::MultiCell(112, $h,$nombre, 'RL', '', 0, 0, '', '', true);
            PDF::MultiCell(20, $h,getDecimal($row->precio_coste), 'R', 'R', 0, 1, '', '', true);

            // $h = $alto=PDF::getStringHeight(100,$txt);
            // $h = $h + 2;

            // $y = round(PDF::getY());

            // // $txt .= " **PW: ".$y." H: ".$h;
            // if ($y+$h >= 210){
            //     PDF::AddPage();
            //     $this->setCabeceraAlbaran();
            //     $y = round(PDF::getY());
            //     $this->cabeLin();
            // }

        }

        PDF::MultiCell(112, 2,"", 'RLB', '', 0, 0, '', '', true);
        PDF::MultiCell(20, 2,"", 'RB', '', 0, 0, '', '', true);
        PDF::Cell(16, 6, '', '', 0, 'C');
        PDF::MultiCell(112, 2,"", 'RLB', '', 0, 0, '', '', true);
        PDF::MultiCell(20, 2,"", 'RB', '', 0, 1, '', '', true);

    }

    private function cabeLin(){

		PDF::SetFont('helvetica', 'RB', 8, '', false);

        PDF::Cell(112, 6, 'CONCEPTO', 'LRB', 0, 'C');
        PDF::Cell(20, 6, 'IMPORTE', 'LRB', 0, 'C');

        PDF::Cell(16, 6, '', '', 0, 'C');
        PDF::Cell(112, 6, 'CONCEPTO', 'LRB', 0, 'C');
        PDF::Cell(20, 6, 'IMPORTE', 'LRB', 0, 'C');
        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 8, '', false);
        PDF::MultiCell(112, 2,"", 'RL', '', 0, 0, '', '', true);
        PDF::MultiCell(20, 2,"", 'R', '', 0, 0, '', '', true);
        PDF::Cell(16, 6, '', '', 0, 'C');
        PDF::MultiCell(112, 2,"", 'RL', '', 0, 0, '', '', true);
        PDF::MultiCell(20, 2,"", 'R', '', 0, 1, '', '', true);
    }


    private function pieDibujo(){


        PDF::Ln();
        PDF::SetFont('helvetica', 'I', 8, '', false);

        PDF::MultiCell(132, 5,'Boceto','LTR', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(16, 5,'','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(132, 5,'Boceto','LTR', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::MultiCell(132, 30,'', $border='LRB', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
        PDF::MultiCell(16, 5,'','', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(132, 30,'', $border='LRB', $align='L', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

    }



     /**
     *
     * @param Model Empresa
     *
     */
    private function setPrepararFormulario($empresa){

        PDF::setHeaderCallback(function($pdf) {

            $pdf->SetFont('helvetica', 'B', 16, '', false);
            $txt = "HOJA DE TALLER";

            $pdf->SetXY(90, 5);
            $pdf->Write($h=0, $txt, $link='', $fill=0, $align='L', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
            $pdf->SetXY(230, 5);
            $pdf->Write($h=0, $txt, $link='', $fill=0, $align='R', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            $pdf->SetFont('helvetica', 'R', 10, '', false);

            $y = 6;
            $pdf->SetXY(5, $y);
            $pdf->Write($h=0,  session('empresa')->razon, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(160, $y);
            $pdf->Write($h=0,  session('empresa')->razon, '', 0, 'L', true, 0, false, true, 0);

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
        PDF::SetMargins(8, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        //PDF::SetFooterMargin(34);

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
