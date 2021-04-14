<?php

namespace App\Http\Controllers\Mto;

use PDF;
use App\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PrintGarantiaDepositoController extends Controller
{
        public function print($id){

            $file = false;

            $this->producto = Producto::with(['cliente','clase'])->findOrFail($id);

            ob_end_clean();

            $this->setPrepararFrm(session("empresa"));

            $this->frmGarantia();

            PDF::Close();

            if ($file){
                if (file_exists(storage_path('compras'))==false)
                    mkdir(storage_path('compras'), '0755');

                PDF::Output(storage_path('compras/gd'.$this->producto->id.'.pdf'), 'F');

            }
            else{
                PDF::Output('GD'.$this->producto->id.'.pdf','I');
            }

            PDF::reset();
        }

         /**
         *
         * Formulario de factura de recuperación de compras.
         *
         */
        private function frmGarantia()    {


            PDF::AddPage();

            // cabecera cliente
            $this->setCabeceraCliPro();

            $this->setDetalle();


        }

         /**
         *
         * @param Model Cliente
         *
         */
        private function setCabeceraCliPro(){

            //PDF::SetFillColor(235, 235, 235);
            PDF::SetFillColor(215, 235, 255);

            PDF::SetFont('helvetica', 'R',11, '', false);
            PDF::setXY(122,14);
            PDF::MultiCell(40, 8, $this->producto->referencia,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

            PDF::setXY(165,14);
            PDF::MultiCell(36, 8,  $this->producto->ref_pol,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

            PDF::SetFont('helvetica', '', 9, '', false);
            PDF::setXY(115,30);
            PDF::MultiCell(90, 5,  'NIF.: '.$this->producto->cliente->dni,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

            PDF::setXY(115,38);
            PDF::MultiCell(90, 5,  $this->producto->cliente->razon, '', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
            PDF::setX(115);
            PDF::MultiCell(90, 5,  $this->producto->cliente->direccion,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
            PDF::setX(115);
            PDF::MultiCell(90, 5,  $this->producto->cliente->cpostal.' '.$this->producto->cliente->poblacion,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
            if ($this->producto->cliente->poblacion != $this->producto->cliente->provincia){
                PDF::setX(115);
                PDF::MultiCell(90, 5,  $this->producto->cliente->provincia,'', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
            }

        }

        /**
         *
         * @param Model Albalins
         *
         */
        private function setDetalle() {

            $date = Carbon::today()->locale('es_ES');

            PDF::Ln();
            PDF::Ln();
            PDF::SetFont('helvetica', 'R', 12, '', false);

            $txt = session('empresa')->poblacion.', '.$date->isoFormat('LL').".\n\n\n";
            PDF::Write($h=0, $txt, '', 0, 'L', true, 0, false, true, 0);


            if ($this->producto->cliente->tipo_doc == 'C')
                $txt='D/Dña. '.$this->producto->cliente->razon.' con DNI: '.$this->producto->cliente->dni.' como propietario del establecimiento de compra-venta oro establecido en  '.$this->producto->cliente->direccion.' '.$this->producto->cliente->poblacion.
                            ', por medio de la presente: '."\n\n".
                            'DECLARO RECIBIR DE '.session('empresa')->razon.' con CIF: '. session('empresa')->cif.' y domicilio en '.session('empresa')->direccion.', '.session('empresa')->poblacion.
                            ' un importe de '.getCurrency($this->producto->precio_coste).' en concepto de garantía, por el depósito para su venta de: '.$this->producto->nombre.
                            ', asentado con el número '.$this->producto->ref_pol.".\n\n".
                            'Y para que conste emito la presente a efectos de recibí.'."\n\n\n\n";
            else
                $txt='D/Dña. '.$this->producto->cliente->razon.' con DNI: '.$this->producto->cliente->dni.' como propietario/a de la pieza detallada más abajo, '.
                            'por medio de la presente: '."\n\n".
                            'DECLARO RECIBIR DE '.session('empresa')->razon.' con CIF: '. session('empresa')->cif.' y domicilio en '.session('empresa')->direccion.', '.session('empresa')->poblacion.
                            ' un importe de '.getCurrency($this->producto->precio_coste).' en concepto de garantía, por el depósito para su venta de: '.$this->producto->nombre.
                            ', asentado con el número '.$this->producto->ref_pol.".\n\n".
                            'Y para que conste emito la presente a efectos de recibí.'."\n\n\n\n";


                //$this->Write($h=0, $html, $link='', $fill=0, $align='J', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
            PDF::Write($h=0, $txt, '', 0, 'J', true, 0, false, true, 0);

            PDF::Ln();
            PDF::Ln();
            $txt = $this->producto->cliente->razon;
            PDF::Write($h=0, $txt, '', 0, 'R', true, 0, false, true, 0);



        }

         /**
         *
         * @param Model Empresa
         *
         */
        private function setPrepararFrm($empresa){

            PDF::setHeaderCallback(function($pdf) {

                if (session('empresa')->img_logo > ""){
                    $pdf->setImageScale(1.60);

                    // $pdf->SetXY(14, 5);
                    // $pdf->Image($imagen, '', '', 42, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
                    //Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

                    $f = str_replace('storage', 'public', session()->get('empresa')->img_logo);

                    if (Storage::exists($f)){
                        $file = '@'.(Storage::get($f));
                        $pdf->setJPEGQuality(75);
                        $pdf->Image($file, $x='5', $y='4', $w=0, $h=25, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
                    }

                }

                $pdf->SetFont('helvetica', 'B', 16, '', false);


                $txt = "GARANTÍA DE DEPÓSITO";

                $pdf->SetXY(4, 5);
                $pdf->Write($h=0, $txt, $link='', $fill=0, $align='R', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

                $pdf->SetFont('helvetica', 'R', 9, '', false);

                $y = 30;
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


                PDF::SetFont('helvetica', 'R', 8);

                $pdf->Write($h=0, session()->get('empresa')->txtpie1, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
                $pdf->Write($h=0, session()->get('empresa')->txtpie2, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);


            });


                    // set document information
            PDF::SetCreator(PDF_CREATOR);
            PDF::SetAuthor($empresa->nombre);
            PDF::SetTitle('Garantía de depósito');
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
            PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        //    PDF::SetFooterMargin(32);

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
