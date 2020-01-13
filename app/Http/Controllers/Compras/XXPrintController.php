<?php

namespace App\Http\Controllers\Compras;

use PDF;
use App\Clidoc;
use App\Compra;
use App\Comline;
use App\Deposito;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class XXPrintController extends Controller
{
    protected $compra;
    protected $lineasCompra;
    protected $documentacion;

    public function print($id){

        $file = false;

        $this->compra = Compra::with(['cliente','grupo','libro','tipo','fase'])->findOrFail($id);

        //TODO: estudiar qué hacer para redirigir esto que no va bien.
        $this->documentacion = Clidoc::getDocumentos($this->compra->cliente->id,$this->compra->cliente->fecha_dni, true);
        if ($this->documentacion['status'] <= 0){
            abort(422,'La documentación no está actualizada');
            return redirect('home');
        }

        $this->lineasCompra = Comline::with('clase')->compraId($this->compra->id)->orderBy('id')->get();


        $empresa  = session()->get('empresa');

        ob_end_clean();

        $this->setPrepararComprafrmCompra1($empresa);

        if ($this->compra->tipo_id == 1){
            $this->frmReCompra1(true);
            $this->frmReCompra1(false);
            $t = Deposito::totalesConcepto($this->compra->id);
            if ($t[1]==0) // si no hay ampliaciones imprimimos la compra.
                $this->frmCompra1();
        }
        else{
            $this->frmCompra1();
        }

        PDF::Close();

        if ($file){
            if (file_exists(storage_path('compras'))==false)
                mkdir(storage_path('compras'), '0755');

            PDF::Output(storage_path('compras/com'.$this->compra->albaran.'.pdf'), 'F');

        }
        else{
            PDF::Output('COMP'.$this->compra->albaran.'.pdf','I');
        }

        PDF::reset();
    }


    /**
     *
     * Formulario de compras.
     *
     */
    private function frmCompra1()    {


        PDF::AddPage();

        // cabecera cliente
        $this->setCabeceraClifrmCompra1();
        $this->setBodyComprafrmCompra1();
        $this->setLineasComfrmCompra1($this->lineasCompra);
        $this->setFirmafrmCompra1();

    }

     /**
     *
     * Formulario de compras.
     *
     */
    private function frmReCompra1($copia)
    {
        // si es recompra, imprime documento de recompra

        PDF::AddPage();

        // cabecera cliente
        $this->setCabeceraClifrmCompra1();

        $this->setBodyRecomprafrmCompra1();
        $this->setLineasRecomprafrmCompra1($this->lineasCompra);
        $this->setFirmaRecomprafrmCompra1($copia);

    }

    /**
     *
     * @param Model Empresa
     *
     */
    private function setPrepararComprafrmCompra1($empresa){

        PDF::setHeaderCallback(function($pdf) {

            $pdf->SetFont('helvetica', 'B', 16, '', false);

            $txt = "COMPRA-VENTA\n".session('empresa')->razon;
            $pdf->Write($h=0, $txt, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            $pdf->SetFont('helvetica', 'B', 10, '', false);

            $tf = 'TEL.: '.substr(session('empresa')->telefono1,0,3).' '.
                            substr(session('empresa')->telefono1,3,3).' '.
                            substr(session('empresa')->telefono1,6,3);

            $pdf->MultiCell(70, 5, session('empresa')->direccion, 0, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(40, 5, $tf, 0, 'C', 0, 0, '', '', true);
            $pdf->MultiCell(62, 5, session('empresa')->provincia, 0, 'R', 0, 0, '', '', true);


            if (session('empresa')->img_logo > ""){
                $pdf->setImageScale(1.80);
                // $pdf->SetXY(14, 5);
                // $pdf->Image($imagen, '', '', 42, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

                $f = str_replace('storage', 'public', session()->get('empresa')->img_logo);

                $file = '@'.(Storage::get($f));
                $pdf->setJPEGQuality(75);
                $pdf->Image($file, $x='5', $y='2', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
                //$pdf->Image($file, 10, 1, 40, 18, '', null, 'M', true, 150, 'L', false, false, 0, false, false, false);

            }


        });

        PDF::setFooterCallback(function($pdf) {

            PDF::SetFont('helvetica', 'R', 6);



            // $html = ('Los datos proporcionados de acuerdo con lo dispuesto en la Ley Orgánica 15/1999, de 13 de diciembre,'.
            //                             ' de Protección de Datos de Carácter Personal (LOPD). El titular presta específicamente su consentimiento '.
            //                             'para que los datos de carácter personal que se facilitan en este documento, serán tratados con la máxima '.
            //                             'confidencialidad, con la finalidad de gestionar su compraventa y el pago de la misma automáticamente e '.
            //                             'incorporados a un fichero o ficheros susceptibles de tratamiento o no, con el fin de que '.session()->get('empresa')->razon.
            //                             ' pueda hacer uso de los mismos para su información y/o ejecución de la presente compra/venta, así como para la '.
            //                             'realización de toda clase de análisis y estudios personalizados o segmentados. '.
            //                             'A los efectos previstos en la ley de Protección de Datos de Carácter Personal '.
            //                             '(Ley Orgánica 15/1999), el titular tiene derecho a acceder al fichero o ficheros que contengan '.
            //                             'sus datos personales, cuyo RESPONSABLE es '.session()->get('empresa')->razon.' a fin de ejercitar su derecho '.
            //                             'de acceso, rectificación, oposición y cancelación en los términos y condiciones estipuladas '.
            //                             'por la Ley de Protección de Datos de Carácter Personal, ante la oficina en ').session()->get('empresa')->direccion.
            //                             ' '.session()->get('empresa')->cpostal.' '.session()->get('empresa')->poblacion."\n";

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
            //$this->writeHTML($html, true, false, false, false, '');

            $pdf->SetFont('helvetica', '', 9);
            $pdf->Write($h=0, session()->get('empresa')->txtpie1, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
            $pdf->Write($h=0, session()->get('empresa')->txtpie2, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            $pdf->SetFont('helvetica', 'I', 8);

            PDF::Ln();
            PDF::Ln();

            $txt = '<span style="font-size:14pt;font-weight: bold;">'.$this->compra->tipo->nombre[0].' '.$this->compra->alb_ser.'</span> '.getFecha($this->compra->fecha_compra).
                    ' %e '.request()->user()->huella.'/'.date('d-m-Y H:i:s');
            $txt = str_replace('%e', session()->get('empresa')->titulo, $txt);

            $txt = 'Registro: '.$txt;
            //$this->Write($h=0, $txt, $link='', $fill=0, $align='L', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
            $pdf->writeHTML($txt, true, false, false, false, '');


        });


                // set document information
        PDF::SetCreator(PDF_CREATOR);
        PDF::SetAuthor($empresa->nombre);
        PDF::SetTitle('Compra');
        PDF::SetSubject('');

        // set default header data
        //PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        //PDF::SetFooterMargin(PDF_MARGIN_FOOTER);
        PDF::SetFooterMargin(52);

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



    /**
     *
     * @param Model Cliente
     *
     */
    private function setCabeceraClifrmCompra1(){

		PDF::SetFont('helvetica', '', 9, '', false);
		//PDF::setY(30);
		PDF::SetFillColor(240,240,240);
		PDF::SetFont('helvetica', '', 7, '', false);
		PDF::MultiCell(36, 8,  request()->user()->huella.'/'.date('d-m-Y H:i:s'), '1', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);

		$montep="Monte de Piedad";
		if ($this->compra->papeleta == 0){
			$this->compra->papeleta = "";
			$montep ="";
		}

		PDF::MultiCell(28, 8, $montep, 'RTB', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
		PDF::MultiCell(18, 8,  $this->compra->papeleta, 'RTB', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
		PDF::SetFont('helvetica', 'R', 9, '', false);
		PDF::MultiCell(28, 8,  "Fecha de Compra", 'RTB', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
		//PDF::SetFont('helvetica', 'B', 9, '', false);
		PDF::SetFont('helvetica', 'B', 10, '', false);
		PDF::MultiCell(28, 8,  getFecha($this->compra->fecha_compra), 'RTB', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
		PDF::SetFont('helvetica', 'R', 9, '', false);
		PDF::MultiCell(20, 8,  ("Registro Nº"), 'RTB', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
		PDF::SetFont('helvetica', 'B', 9, '', false);

		PDF::SetFont('helvetica', 'B', 10, '', false);
		PDF::MultiCell(24, 8,  $this->compra->getAlbSerAttribute(), 'RTB', 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
		PDF::SetFont('helvetica', 'R', 9, '', false);
		PDF::SetFont('helvetica', '', 9, '', false);

		PDF::Ln();

		PDF::SetFont('helvetica', '', 7, '', false);
		PDF::MultiCell(120, 4,  "Nombre y Apellidos", 'LRT', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(32, 4,  "DNI/NIE/PAS", 'RT', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(30, 4,  ("F. Validez"), 'RT', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 9, '', false);

		PDF::MultiCell(120, 4,  $this->compra->cliente->razon, 'LRB', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(32, 4,  $this->compra->cliente->dni,'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(30, 4,  getFecha($this->compra->cliente->fecha_dni),'RB', 'C', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 7, '', false);
		PDF::MultiCell(82, 4, ("Domicilio"), 'LR', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(60, 4, ("Población"), 'R', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4, ("Provincia"), 'R', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);


		PDF::SetFont('helvetica', '', 9, '', false);
		PDF::MultiCell(82, 4,  $this->compra->cliente->direccion, 'LRB', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(60, 4,  $this->compra->cliente->poblacion, 'RB', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4,  $this->compra->cliente->provincia, 'RB', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 7, '', false);
		PDF::MultiCell(102, 4, ("Lugar de Nacimiento"), 'LR', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4, ("Fecha Nacimiento"), 'R', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4, ("Teléfono"), 'R', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);


		PDF::SetFont('helvetica', '', 9, '', false);
		PDF::MultiCell(102, 4,  $this->compra->cliente->nacpob.' ('.$this->compra->cliente->nacpro.')', 'LRB', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4,  getFecha($this->compra->cliente->fecha_nacimiento),'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4,  $this->compra->cliente->telefono1.' '.$this->compra->cliente->tfmovil, 'RB', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);

    }

    /**
     *
     * @param Model Albaran
     *
     */
    private function setBodyComprafrmCompra1(){


        PDF::Ln();
		PDF::Ln();
		PDF::SetFontSize(9);

		$html=('Vendo al establecimiento arriba indicado los objetos detallados a continuación, declarando que los mismos'.
				' son de mi absoluta propiedad y que no son procedentes los mismos de mala o dudosa apropiación por mi parte. '.
				'En caso de reclamación de dichos objetos me comprometo a justificar su procedencia, respondiendo'.
				' de los perjuicios y gastos que hubiere por esta causa.');
		if ($this->compra->grupo->nombre == "Metal"){
				$html.=(' Todas las compras origen de esta factura se efectúan para su posterior fundición (excepto piedras'.
				' preciosas o piedras especiales) después de pasar el período de retención según el Decreto-ley 3.390/81'.
				' pagándose un justiprecio de acuerdo con la cotización del precio de deshechos de oro o plata en el mercado.'."\n");
				$operacon = $this->compra->grupo->nombre;
		}else{
			$operacon = $this->compra->grupo->nombre;
		}

		PDF::Write($h=0, $html, $link='', $fill=0, $align='J', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
		PDF::Ln();

		PDF::SetFontSize(9);

		$html=('<style>.negrita {font-weight: bold;}</style> <p>He recibido en esta fecha la cantidad de <span class="negrita">'.getCurrency($this->compra->imp_pres).' </span>'.
				'en concepto de venta de '.$operacon.' que a continuación detallo y declaro son de mi propiedad.</p><p></p>');

		PDF::writeHTML($html, true, false, false, false, '');

    }

    /**
     *
     * @param Model Albalins
     *
     */
    private function setLineasComfrmCompra1($lineas){



        $txt = "CONCEPTO";
		$imp = "IMPORTE";

		PDF::Cell(160, 6, $txt, 'LRTB', 0, 'L');
		PDF::Cell(20, 6, $imp, 'RTB', 0, 'L');
		PDF::Ln();

		PDF::SetFont('helvetica', 'R', 8, '', false);

		foreach ($lineas as $row) {

			if ($row->peso_gr < 0) continue;

			if ((int) $row->unidades == 0) $row->unidades = null;

			$txt=$row->unidades." ".$row->concepto." ".strtoupper($row->clase->nombre);


            $peso_gr = (!$this->compra->libro->peso_frm) ? null : getDecimal($row->peso_gr).' gr.';

			if ($row->quilates != null){
				$txt.= ' '.$row->quilates;
			}

			if ($row->colores != null)
				$txt.= ' '.$row->colores;

			if ($row->grabaciones != null)
				$txt.= " (".$row->grabaciones.") ";

			if ($peso_gr != null)
				$txt.= ' '.$peso_gr;

			$imp = getDecimal($row->importe);

			$alto=PDF::getStringHeight(160,$txt);

			PDF::MultiCell(160, $alto,$txt, 'LR', 'L', 0, 0, '', '', true);
			PDF::MultiCell(20, $alto,$imp, 'R', 'R', 0, 0, '', '', true);

			PDF::Ln();
		}

		$alto=6;

		PDF::MultiCell(160, $alto,"", 'LRB', 'L', 0, 0, '', '', true);
		PDF::MultiCell(20, $alto,"", 'RB', 'R', 0, 0, '', '', true);

		PDF::SetFont('helvetica', 'R', 9, '', false);

    }

    /**
     *
     *
     */
    private function setFirmafrmCompra1(){

        PDF::Ln();
		PDF::Ln();
		$html="Firmado: EL VENDEDOR";
        PDF::Write($h=0, $html, $link='', $fill=0, $align='L', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

        if ($this->documentacion['status']!=false){

            $y=200;
            if (PDF::getY() >= 200){
                PDF::AddPage();
                $this->setCabeceraClifrmCompra1();
                $y = 70;
            }

            if (!Empty($this->documentacion['img1'])){
                $img = base64_decode($this->documentacion['img1']);
                imagecreatefromstring($img);
                PDF::Image('@'.$img,  $x=40, $y, 50, 0, '', '', '', true, 150,'',false,false,1);
            }
            if (!Empty($this->documentacion['img2'])){
                $img2 = base64_decode($this->documentacion['img2']);
                imagecreatefromstring($img2);
                PDF::Image('@'.$img2,  $x=110, $y, 50, 0, '', '', '', true, 150,'',false,false,1);
            }


        }

        // $clidoc = Clidoc::cliente($this->compra->cliente_id)->first();
        // if ($clidoc != false){
        //     $y=200;
        //     if (PDF::getY() >= 200){
        //         PDF::AddPage();
        //         $this->setCabeceraClifrmCompra1();
        //         $y = 70;
        //     }



        //     if (Storage::disk('docs')->exists($clidoc->file1)){
        //         $img = base64_decode(str_replace('data:image/jpg;base64,','',$this->documentacion['img1']));
        //         imagecreatefromstring($img);

        //         $file1 = '@'.$img;
        //         PDF::Image($file1,  $x=40, $y, 50, 0, '', '', '', true, 150,'',false,false,1);
        //     }
        //     if (Storage::disk('docs')->exists($clidoc->file2)){
        //         $img = base64_decode(str_replace('data:image/jpg;base64,','',$this->documentacion['img2']));
        //         imagecreatefromstring($img);

        //         $file2 = '@'.$img;
        //         PDF::Image($file2,  $x=110, $y, 50, 0, '', '', '', true, 150,'',false,false,1);
        //     }



        // }

        // $clidoc = Clidoc::cliente($this->compra->cliente_id)->first();
        // if ($clidoc != false){
        //     $y=200;
        //     if (PDF::getY() >= 200){
        //         PDF::AddPage();
        //         $this->setCabeceraClifrmCompra1();
        //         $y = 70;
        //     }
        //     if (Storage::disk('docs')->exists($clidoc->file1)){
        //         $file1 = '@'.Storage::disk('docs')->get($clidoc->file1);
        //         PDF::Image($file1,  $x=40, $y, 50, 0, '', '', '', true, 150,'',false,false,1);
        //     }
        //     if (Storage::disk('docs')->exists($clidoc->file1)){
        //         $file2 = '@'.Storage::disk('docs')->get($clidoc->file2);
        //         PDF::Image($file2,  $x=110, $y, 50, 0, '', '', '', true, 150,'',false,false,1);
        //     }

        // }

    }

    private function setBodyRecomprafrmCompra1(){

		PDF::SetFontSize(9);

		$html="<style>.negrita {font-weight: bold;} p{text-align: justify;text-indent: 5pt;}.subraya{text-decoration: underline; font-size: 12pt;}</style>";
		$html.= ('<p>La empresa '.session('empresa')->razon.' con CIF.:'.session('empresa')->cif.
			   ' se compromete a reservar para su venta el lote con Número de asiento '.$this->compra->alb_ser.
			   ' comprado el día arriba indicado y descrito en el libro oficial de registro de conformidad'.
			   ' al Real Decreto 197/1988 de 22 de febrero, compuesto los objetos más abajo detallados.</p>');

		PDF::SetXY(20, 66);
		PDF::writeHTML($html, true, false, false, false, '');
		PDF::Ln();


		PDF::SetXY(15, 82);
		PDF::SetFont('helvetica', 'R', 7, '', false);
		PDF::MultiCell(44, 5,  ("Fecha tope recuperación"), "LRT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(44, 5,  ("Importe de recuperación"), "RT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(44, 5,  ("Importe de renovación"), "RT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(48, 5,  ("Importe de la Compra"), "RT", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
		PDF::SetFont('helvetica', 'R', 7, '', false);

		PDF::SetFont('helvetica', 'B', 9, '', false);
		PDF::MultiCell(44, 5,  getFecha($this->compra->fecha_renovacion), 'LRB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(44, 5,  getDecimal($this->compra->imp_recu), 'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(44, 5,  getDecimal($this->compra->importe_renovacion), 'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(48, 5,  getDecimal($this->compra->imp_pres), 'RB', 'C', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', 'R', 9, '', false);
		PDF::SetFontSize(9);
		$html="<style>.negrita {font-weight: bold;} p{text-align: justify;text-indent: 5pt;}.subraya{text-decoration: underline; font-size: 12pt;}</style>";
		$html.= ('<p>La recuperación se podrá realizar transcurridos <span class="negrita">'.($this->compra->dias_custodia+5).' días</span> '.
				'de la fecha de este contrato <span class="negrita">NUNCA ANTES y OBLIGATORIAMENTE DEBERÁN DE AVISAR con UN DÍA de ANTELACIÓN.</span>'.
				'</p>');

		$html.= ('<p>Queda de manifiesto que pasados <span class="negrita">'.$this->compra->libro->dias_cortesia.' días de la fecha tope de recuperación</span> '.
				'y de no materializarse la misma por la parte compradora se podrá disponer de el/los objeto/s reseñados '.
				'en el libro de Registro Oficial, sin perjuicio a las partes intervinientes. Se entiende por ello '.
				'la falta de interés por la <span class="negrita">RECOMPRA</span>. La empresa <span class="negrita">').
				session('empresa')->razon.('</span> le garantiza el cobro de un 100% del importe de la valoración que figura '.
				'en nuestro libro oficial de registro, en el caso de que la empresa extraviara las piezas objeto de este '.
				'contrato, siempre y cuando dicho extravío no sea ocasionado por causa mayor y usted se presentara en fechas '.
				'hábiles para su recuperación.</p>');
		$html.= ('<p>Con este pago me considero suficientemente indemnizado por la empresa ').session('empresa')->razon.'</p>';

		PDF::SetXY(20, 94);
		PDF::writeHTML($html, true, false, false, false, '');
		PDF::Ln();




    }

     /**
     *
     * @param Model Albalins
     *
     */
    private function setLineasReComprafrmCompra1($lineas){

        PDF::SetXY(15, 140);

		PDF::SetFont('helvetica', 'R', 7, '', false);
		PDF::MultiCell(180, 5,"Detalle de la compra", "LRT", 'L', 0, 1, '', '', true,0,false,true,5,'T',false);

		PDF::SetFont('helvetica', 'R', 8, '', false);

        $str = null;
		foreach ($lineas as $row) {

			if ($row->peso_gr < 0) continue;

			($row->unidades <> 0) ? $unidad = $row->unidades." " : $unidad = null;

			$str.=$unidad.$row->concepto." ".$row->clase->nombre;

			if ($row->quilates != null){
				$str.= ' '.$row->quilates;
			}

			if ($row->colores != null)
				$str.= ' '.$row->colores;

			if ($row->grabaciones != null)
				$str.= " (".$row->grabaciones.") ";


            $peso_gr = (!$this->compra->libro->peso_gr_frm) ? null : getDecimal($row->peso_gr).' gr.';


			if ($peso_gr != null){
				$str.= ' '.$peso_gr;
			}

            PDF::MultiCell(180, 5, $str."\n", "LR", 'J', 0, 1, '', '', true, 0, false, true, 60, 'T', true);
			//PDF::MultiCell(180, 5,$str, "LR", 'L', 0, 1, '', '', true,0,false,true,25,'T',false);
			$str = null;

		}

		$str = substr($str, 0, -2);
		PDF::MultiCell(180, 5,"", "LRB", 'L', 0, 1, '', '', true,0,false,true,25,'T',false);


    }

    private function setFirmaRecomprafrmCompra1($copia){

        if ($copia){
			PDF::MultiCell(30, 5,  ("AUTORIZACIÓN"), "L", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::SetFont('helvetica', 'R', 7, '', false);
			PDF::MultiCell(150, 5, ("Deberá acompañar fotocopia de la documentación de la persona que autoriza y ORIGINAL del autorizado."), 'LR', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

			PDF::SetFont('helvetica', 'R', 7, '', false);
			PDF::MultiCell(140, 5,  ("Nombre y Apellidos"), "LRT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(40, 5,  ("Dni/Nie/Pas"), "RT", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);


			PDF::MultiCell(140, 5, (""), 'LRB', 'C', 0, 0, '', '', true,0,false,true,5,'B',false);
			PDF::MultiCell(40, 5, "", 'RB', 'C', 0, 1, '', '', true,0,false,true,5,'M',false);


			PDF::MultiCell(80, 5,  "", "0", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(50, 5,  ("Firma de quien autoriza"), "LR", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(50, 5,  ("Firma de autorizado"), "LR", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

			PDF::MultiCell(80, 10,  "", "0", 'L', 0, 0, '', '', true,0,false,true,10,'M',false);
			PDF::MultiCell(50, 10,  "", "LB", 'L', 0, 0, '', '', true,0,false,true,10,'M',false);
			PDF::MultiCell(50, 10,  "", "BLR", 'L', 0, 1, '', '', true,0,false,true,10,'M',false);
		}

    }


}
