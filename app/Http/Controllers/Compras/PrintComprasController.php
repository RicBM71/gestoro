<?php

namespace App\Http\Controllers\Compras;

use PDF;
use App\Libro;
use App\Clidoc;
use App\Compra;
use App\Cuenta;
use App\Comline;
use App\Deposito;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PrintComprasController extends Controller
{
    protected $compra;
    protected $lineasCompra;
    protected $documentacion;
    protected $pdf_margen_footer = 35;
    protected $formulario = "GE";

    public function print($id){

        $file = false;

        $this->compra = Compra::with(['cliente','grupo','tipo','fase'])->findOrFail($id);

        if ($this->compra->fase_id <= 3)
            $this->compra->update(['fase_id' => 4,'username'=>session('username')]);

        $totales_concepto = Deposito::totalesConcepto($this->compra->id);

        //TODO: estudiar qué hacer para redirigir esto que no va bien.
        $this->documentacion = Clidoc::getDocumentos($this->compra->cliente->id,$this->compra->cliente->fecha_dni,$this->compra->fecha_compra, true);
        // if ($this->documentacion['status'] <= 0){
        //     if ($totales_concepto[1] == 0){
        //         abort(422,'La documentación no está actualizada');
        //         return redirect('home');
        //     }
        // }

        $this->lineasCompra = Comline::with('clase')->compraId($this->compra->id)->orderBy('id')->get();


        $empresa  = session()->get('empresa');

        $this->formulario = session()->get('parametros')->frm_compras;

        ob_end_clean();

        if ($this->formulario == "GE")
            $this->setPrepararComprafrmCompra1($empresa);
        else
            $this->setPrepararComprafrmCompra_klt($empresa);


        if ($this->compra->tipo_id == 1){
            $this->frmReCompra1(true);
            $this->frmReCompra1(false);

            if ($totales_concepto[1]==0) // si no hay ampliaciones imprimimos la compra.
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
        $this->setLineasRecomprafrmCompra1($this->lineasCompra, true);
        //$this->setLineasComfrmCompra1($this->lineasCompra);
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
        $this->setAutorizacionfrmCompra1($copia);
        $this->setLineasRecomprafrmCompra1($this->lineasCompra, false);

    }

    /**
     *
     * @param Model Empresa
     *
     */
    private function setPrepararComprafrmCompra1($empresa){

      //  PDF::setPageUnit('mm');

        PDF::setHeaderCallback(function($pdf) {

            // $pdf->SetFont('helvetica', 'B', 16, '', false);

            // $txt = "COMPRA-VENTA\n".session('empresa')->razon;
            // $pdf->Write($h=0, $txt, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            // $pdf->SetFont('helvetica', 'B', 10, '', false);

            // $tf = 'TEL.: '.substr(session('empresa')->telefono1,0,3).' '.
            //                 substr(session('empresa')->telefono1,3,3).' '.
            //                 substr(session('empresa')->telefono1,6,3);

            // $pdf->MultiCell(70, 5, session('empresa')->direccion, 0, 'L', 0, 0, '', '', true);
            // $pdf->MultiCell(40, 5, $tf, 0, 'C', 0, 0, '', '', true);
            // $pdf->MultiCell(62, 5, session('empresa')->provincia, 0, 'R', 0, 0, '', '', true);


            if (session('empresa')->img_logo > ""){
                $pdf->setImageScale(1.80);
                // $pdf->SetXY(14, 5);
                // $pdf->Image($imagen, '', '', 42, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

                $f = str_replace('storage', 'public', session()->get('empresa')->img_logo);

                $file = '@'.(Storage::get($f));
                $pdf->setJPEGQuality(75);

                $pdf->Image($file, $x='5', $y='4', $w=0, $h=25, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
                //$pdf->Image($file, 10, 1, 40, 18, '', null, 'M', true, 150, 'L', false, false, 0, false, false, false);

            }

            $pdf->SetFont('helvetica', 'B', 16, '', false);


            $txt = $this->compra->tipo_id == 1 ? "COMPRA-VENTA" : "DOCUMENTO DE COMPRA";

            $pdf->SetXY(4, 5);
            $pdf->Write($h=0, $txt, $link='', $fill=0, $align='R', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            $pdf->SetFont('helvetica', 'R', 9, '', false);

            $y = 28;
            $pdf->SetXY(10, $y);
            $pdf->Write($h=0,  session('empresa')->razon, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(170, $y);

            $pdf->SetFont('helvetica', 'B', 9, '', false);
            $pdf->Write($h=0, 'Tf.: '.getTelefono(session('empresa')->telefono1), '', 0, 'L', true, 0, false, true, 0);

            $pdf->SetFont('helvetica', 'R', 9, '', false);
            $pdf->SetXY(10, $y+=5);
            $pdf->Write($h=0,  session('empresa')->direccion, '', 0, 'L', true, 0, false, true, 0);
            $pdf->SetXY(10, $y+=5);
            $pdf->Write($h=0,  session('empresa')->cpostal.' '.session('empresa')->poblacion, '', 0, 'L', true, 0, false, true, 0);
            // $pdf->SetXY(16, $y+=5);
           // $pdf->Write($h=0,  'CIF.: '.session('empresa')->cif, '', 0, 'L', true, 0, false, true, 0);


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
            PDF::Ln();

            // $pdf->SetFont('helvetica', '', 9);
            // $pdf->Write($h=0, session()->get('empresa')->txtpie1, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
            // $pdf->Write($h=0, session()->get('empresa')->txtpie2, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            // $pdf->SetFont('helvetica', 'I', 8);

            $txt = $this->compra->tipo->nombre[0].' '.$this->compra->alb_ser.' '.getFecha($this->compra->fecha_compra).
                    ' %e '.request()->user()->huella.'/'.date('d-m-Y H:i:s');
            $txt = str_replace('%e', session()->get('empresa')->titulo, $txt);
            $pdf->SetFont('helvetica', 'RB', 12);
            $pdf->MultiCell($w=160, $h, $txt, $border='', $align='L', $fill=0, $ln=false, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            //$pdf->Write($h=0, $txt, $link='', $fill=0, $align='L', $ln=false, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

            $pdf->SetFont('helvetica', 'R', 8);
            $pdf->MultiCell($w=40, $h, $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            //$pdf->Cell(0, 10, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');


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
        PDF::SetFooterMargin($this->pdf_margen_footer);

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

        if ($this->formulario == "KL"){
            $this->setCabeceraClifrmCompra_klt();
            return;
        }


        $fecha =  getFecha($this->compra->fecha_compra);
        $num_doc = $this->compra->alb_ser;

        //PDF::SetFillColor(235, 235, 235);
        PDF::SetFillColor(215, 235, 255);

        PDF::SetFont('helvetica', 'R',11, '', false);
        PDF::setXY(122,14);
        PDF::MultiCell(40, 8, $fecha,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

        PDF::setXY(165,14);
        PDF::MultiCell(36, 8,  $num_doc,'', 'C', 1, 1, '', '', true,0,false,true,8,'M',false);

        PDF::Ln();
        PDF::Ln();
        PDF::Ln();

		PDF::SetFont('helvetica', '', 7, '', false);
		PDF::MultiCell(120, 4,  "Nombre y Apellidos", 'LRT', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(32, 4,  "DNI/NIE/PAS", 'RT', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(30, 4,  ("F. Validez"), 'RT', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 9, '', false);

        $fecha_dni = $this->compra->cliente->fecha_dni==null ? "" : getFecha($this->compra->cliente->fecha_dni);

		PDF::MultiCell(120, 4,  $this->compra->cliente->razon, 'LRB', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(32, 4,  $this->compra->cliente->dni,'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(30, 4,  $fecha_dni,'RB', 'C', 0, 1, '', '', true,0,false,true,5,'M',false);

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
		PDF::MultiCell(40, 4,  getTelefono($this->compra->cliente->telefono1).' '.getTelefono($this->compra->cliente->tfmovil), 'RB', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

    }

    /**
     *
     * @param Model Albaran
     *
     */
    private function setBodyComprafrmCompra1(){


        PDF::Ln();
		PDF::Ln();
		PDF::SetFontSize(8);

		$txt=("  Vendo al establecimiento arriba indicado los objetos detallados a continuación, declarando que los mismos".
				" son de mi absoluta propiedad y que no son procedentes los mismos de mala o dudosa apropiación por mi parte. ".
				"En caso de reclamación de dichos objetos me comprometo a justificar su procedencia, respondiendo".
				" de los perjuicios y gastos que hubiere por esta causa.\n");
		if ($this->compra->grupo->nombre == "Metal"){
				$txt.=("\n Todas las compras origen de esta factura se efectúan para su posterior fundición (excepto piedras".
				" preciosas o piedras especiales) después de pasar el período de retención según el Decreto-ley 3.390/81".
				" pagándose un justiprecio de acuerdo con la cotización del precio de deshechos de oro o plata en el mercado."."\n");
				$operacon = $this->compra->grupo->leyenda;
		}else{
			$operacon = $this->compra->grupo->leyenda;
		}

		PDF::Write($h=0, $txt, $link='', $fill=0, $align='J', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
		PDF::Ln();

		$txt=("  He recibido en esta fecha la cantidad de ".getCurrency($this->compra->imp_pres)." ".
              "en concepto de venta de ".$operacon." que a continuación detallo y declaro son de mi propiedad.\n");

        if ($this->compra->retencion > 0){
            $txt.="\nEsta operación está sujeta al Impuesto sobre Transmisiones Patrimoniales y por tanto un ".getDecimal($this->compra->retencion)."% (".getCurrency($this->compra->imp_ret).") de la misma, serán liquidados a favor de la Agencia Tributaria.\n";
        }

        PDF::MultiCell($w=180, $h=0, $txt, $border='', $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

    }

    /**
     *
     * @param Model Albalins
     *
     */
    private function setLineasComfrmCompra1($lineas){


        // $txt = "CONCEPTO";
		// $imp = "IMPORTE";

        // PDF::Ln();
        // PDF::MultiCell(160, 6,"", 'LRTB', 'L', 0, 0, '', '', true);
		// PDF::MultiCell(20, 6,"", 'RTB', 'L', 0, 0, '', '', true);

		// PDF::SetFont('helvetica', 'R', 8, '', false);

		// foreach ($lineas as $row) {

		// 	if ($row->peso_gr < 0) continue;

		// 	if ((int) $row->unidades == 0) $row->unidades = null;

		// 	$txt=$row->unidades." ".$row->concepto." ".strtoupper($row->clase->nombre);


        //     $peso_gr = (!$this->compra->libro->peso_frm) ? null : getDecimal($row->peso_gr).' gr.';

		// 	if ($row->quilates != null){
		// 		$txt.= ' '.$row->quilates;
		// 	}

		// 	if ($row->colores != null)
		// 		$txt.= ' '.$row->colores;

		// 	if ($row->grabaciones != null)
		// 		$txt.= " (".$row->grabaciones.") ";

		// 	if ($peso_gr != null)
		// 		$txt.= ' '.$peso_gr;

		// 	$imp = getDecimal($row->importe);

		// 	$alto=PDF::getStringHeight(160,$txt);

		// 	PDF::MultiCell(160, $alto,$txt, 'LR', 'L', 0, 0, '', '', true);
		// 	PDF::MultiCell(20, $alto,$imp, 'R', 'R', 0, 0, '', '', true);

		// 	PDF::Ln();
		// }

		// $alto=6;

		// PDF::MultiCell(160, $alto,"", 'LRB', 'L', 0, 0, '', '', true);
		// PDF::MultiCell(20, $alto,"", 'RB', 'R', 1, 0, '', '', true);

		// PDF::SetFont('helvetica', 'R', 9, '', false);

    }

    /**
     *
     *
     */
    private function setFirmafrmCompra1(){

        PDF::Write($h=6, "", $link='', $fill=0, $align='L', $ln=1, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
        $txt="Firmado: EL VENDEDOR";
        PDF::SetFont('helvetica', 'B', 9, '', false);
        PDF::Write($h=2, $txt, $link='', $fill=0, $align='L', $ln=1, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

        if ($this->documentacion['status']==2){

            $y=220;
            if (PDF::getY() >= 200){
                PDF::AddPage();
                $this->setCabeceraClifrmCompra1();
                $y = 70;
            }

            if (!Empty($this->documentacion['img1'])){
                $img = base64_decode($this->documentacion['img1']);
                imagecreatefromstring($img);
                PDF::Image('@'.$img,  $x=40, $y, 45, 0, '', '', '', true, 150,'',false,false,0);
            }
            if (!Empty($this->documentacion['img2'])){
                $img2 = base64_decode($this->documentacion['img2']);
                imagecreatefromstring($img2);
                PDF::Image('@'.$img2,  $x=110, $y, 45, 0, '', '', '', true, 150,'',false,false,0);
            }


        }

    }

    private function setBodyRecomprafrmCompra1(){

        if ($this->formulario == "KL"){
            $this->setBodyRecomprafrmCompra_klt();
            return;
        }

        try {
            $libro = Libro::where('grupo_id', $this->compra->grupo_id)
                            ->where('ejercicio', getEjercicio($this->compra->fecha_compra))
                            ->firstOrFail();

            $dias_cortesia = $libro->dias_cortesia;
        } catch (\Exception $e) {
            $dias_cortesia = 7;
        }



		PDF::SetFontSize(9);


        //PDF::SetXY(15, 58);


        PDF::SetFont('helvetica', 'R', 7, '', false);
        PDF::MultiCell(40, 5,  ("Papeleta"), "LRT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(34, 5,  ("Fecha tope recuperación"), "RT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5,  ("Importe de recuperación"), "RT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5,  ("Importe de renovación"), "RT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5,  ("Importe de la Compra"), "RT", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

        $papeleta = (is_null($this->compra->papeleta)) ? "" : $this->compra->papeleta;
        PDF::SetFont('helvetica', 'B', 9, '', false);
        PDF::MultiCell(40, 5, $papeleta, 'LRB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(34, 5, getFecha($this->compra->fecha_renovacion), 'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5, getDecimal($this->compra->imp_recu), 'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5, getDecimal($this->compra->importe_renovacion), 'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5, getDecimal($this->compra->imp_pres), 'RB', 'C', 0, 1, '', '', true,0,false,true,5,'M',false);

        PDF::SetFont('helvetica', 'R', 9, '', false);

        PDF::SetXY(15, 84);
		$txt = ("  La empresa ".session("empresa")->razon." con CIF.:".session("empresa")->cif.
        " se compromete a reservar para su venta el lote con Número de asiento ".$this->compra->alb_ser.
        " comprado el día arriba indicado y descrito en el libro oficial de registro de conformidad".
        " al Real Decreto 197/1988 de 22 de febrero, compuesto los objetos más abajo detallados.\n\n");
        PDF::MultiCell($w=180, $h=0, $txt, $border='', $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

		$txt = (" La recuperación se podrá realizar transcurridos ".($this->compra->dias_custodia)." días ".
				"de la fecha de este contrato NUNCA ANTES y OBLIGATORIAMENTE DEBERÁN DE AVISAR con UN DÍA de ANTELACIÓN.".
				"\n\n");

		$txt.= ("  Queda de manifiesto que pasados ".$dias_cortesia." días de la fecha tope de recuperación ".
				"y de no materializarse la misma por la parte compradora se podrá disponer de el/los objeto/s reseñados ".
				"en el libro de Registro Oficial, sin perjuicio a las partes intervinientes. Se entiende por ello ".
				"la falta de interés por la RECOMPRA. La empresa ").
				session("empresa")->razon.(" le garantiza el pago de un 20% del importe de la valoración que figura ".
				"en nuestro libro oficial de registro, en el caso de que la empresa extraviara las piezas objeto de este ".
				"contrato, siempre y cuando dicho extravío no sea ocasionado por causa mayor y usted se presentara en fechas ".
				"hábiles para su recuperación. ");
		$txt.= ("Con este pago me considero suficientemente indemnizado por la empresa ").session("empresa")->razon.".\n";

//

        PDF::MultiCell($w=180, $h=0, $txt, $border='', $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

    }

     /**
     *
     * @param Model Albalins
     *
     */
    private function setLineasReComprafrmCompra1($lineas, $es_compra){


        $this->cabeLin();
        $i=0;

        $total=0;
		foreach ($lineas as $row) {

            $txt = $row->concepto.' '.$row->clase->nombre;
			if ($row->quilates != null){
				$txt.= ' '.$row->quilates.'KT';
			}

			if ($row->colores != null)
				$txt.= ' '.$row->colores;

			if ($row->grabaciones != null)
                $txt.= " (".$row->grabaciones.") ";

            if (session('empresa')->getFlag(7))
                $peso = ($row->peso_gr > 0) ? " ".getDecimal($row->peso_gr).' gr.' : null;
            else
                $peso = ($row->peso_gr > 0 && !$es_compra) ? " ".getDecimal($row->peso_gr).' gr.' : null;

            $txt.=$peso;


            $importe = ($this->compra->factura == '') ? $row->importe : $row->importe_venta;

			$total += $row->importe;

            $h = round(PDF::getStringHeight(160,$txt));
            // $xx = PDF::getPageWidth();

            $y = round(PDF::getY());

            // $txt .= " **PW: ".$y." H: ".$h;
            if ($y >= 210){
                PDF::AddPage();
                $this->setCabeceraClifrmCompra1();
                $y = round(PDF::getY());

                // $txt .= " *Y:".$y;

                $this->cabeLin();
            }


            PDF::MultiCell($w=160, $h, $txt, $border='R', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
            PDF::MultiCell($w=20, $h, getDecimal($importe), $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

            //PDF::setCellHeightRatio(2);
        }


        // PDF::SetXY(15, 140);

		// PDF::SetFont('helvetica', 'R', 7, '', false);
		// PDF::MultiCell(180, 5,"Detalle de la compra", "LRT", 'L', 0, 1, '', '', true,0,false,true,5,'T',false);

		// PDF::SetFont('helvetica', 'R', 8, '', false);

        // $str = null;
		// foreach ($lineas as $row) {

		// 	if ($row->peso_gr < 0) continue;

		// 	($row->unidades <> 0) ? $unidad = $row->unidades." " : $unidad = null;

		// 	$str.=$unidad.$row->concepto." ".$row->clase->nombre;

		// 	if ($row->quilates != null){
		// 		$str.= ' '.$row->quilates;
		// 	}

		// 	if ($row->colores != null)
		// 		$str.= ' '.$row->colores;

		// 	if ($row->grabaciones != null)
		// 		$str.= " (".$row->grabaciones.") ";


        //     $peso_gr = (!$this->compra->libro->peso_gr_frm) ? null : getDecimal($row->peso_gr).' gr.';


		// 	if ($peso_gr != null){
		// 		$str.= ' '.$peso_gr;
		// 	}

        //     PDF::MultiCell(180, 5, $str."\n", "LR", 'J', 0, 1, '', '', true, 0, false, true, 60, 'T', true);
		// 	//PDF::MultiCell(180, 5,$str, "LR", 'L', 0, 1, '', '', true,0,false,true,25,'T',false);
		// 	$str = null;

		// }

		// $str = substr($str, 0, -2);
		// PDF::MultiCell(180, 5,"", "LRB", 'L', 0, 1, '', '', true,0,false,true,25,'T',false);


    }

    private function cabeLin(){


        PDF::MultiCell(30, 4,"", "", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
        $txt = "CONCEPTO";
		$imp = "IMPORTE";

        //PDF::SetXY(15, 102);
        PDF::SetFont('helvetica', 'B', 8, '', false);

        if ($this->formulario == "GE"){
            PDF::MultiCell($w=160, 8, $txt, $border='TBR', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=8,'M');
            PDF::MultiCell($w=20, 8, $imp, $border='TB', $align='L', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=8,'M');
        }else{
            PDF::MultiCell($w=160, 8, $txt, $border='BR', $align='L', $fill=0, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=8,'M');
            PDF::MultiCell($w=20, 8, $imp, $border='B', $align='L', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=8,'M');
        }



		PDF::SetFont('helvetica', 'R', 8, '', false);
    }

    private function setAutorizacionfrmCompra1($copia){

        if ($this->formulario == "KL"){
            $this->setAutorizacionfrmCompra_klt($copia);
            return;
        }

        if ($copia){
            PDF::MultiCell(30, 4,"", "", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(30, 5,  ("AUTORIZACIÓN"), "LT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::SetFont('helvetica', 'R', 7, '', false);
			PDF::MultiCell(152, 5, ("Deberá acompañar fotocopia de la documentación de la persona que autoriza y ORIGINAL del autorizado."), 'LRT', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

			PDF::SetFont('helvetica', 'R', 7, '', false);
			PDF::MultiCell(70, 5,  ("Nombre y Apellidos"), "LRT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(36, 5,  ("DNI/NIE/Pas"), "RT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(38, 5,  ("Firma de quien autoriza"), "LRT", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(38, 5,  ("Firma de autorizado"), "LRT", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);


			PDF::MultiCell(70, 7, (""), 'LRB', 'C', 0, 0, '', '', true,0,false,true,5,'B',false);
			PDF::MultiCell(36, 7, "", 'RB', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(38, 7,  "", "LB", 'L', 0, 0, '', '', true,0,false,true,10,'M',false);
            PDF::MultiCell(38, 7,  "", "BLR", 'L', 0, 1, '', '', true,0,false,true,10,'M',false);

            if (session('empresa')->getFlag(9) && $this->compra->cliente->notificar_iban == TRUE){
                try {
                    PDF::SetFont('helvetica', 'R', 9, '', false);
                    $cuenta = Cuenta::defecto()->firstOrFail();
                    $txt = "* Puede realizar su renovación a través del siguiente número de cuenta IBAN: ".getIbanPrint($cuenta->iban).", indicando en el concepto de la transferencia: ".session('parametros')->tag_renovar." ".$this->compra->alb_ser.".";
                    PDF::MultiCell(180, 5,  $txt, "0", 'L', 0, 1, '', '', true,0,false,true,15,'M',false);
                } catch (\Exception $e) {
                }
            }

			// PDF::MultiCell(80, 5,  "", "0", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);

			// PDF::MultiCell(80, 10,  "", "0", 'L', 0, 0, '', '', true,0,false,true,10,'M',false);
		}

    }

     /**
     *
     * @param Model Empresa
     *
     */
    private function setPrepararComprafrmCompra_klt($empresa){

        //  PDF::setPageUnit('mm');

          PDF::setHeaderCallback(function($pdf) {



              if (session('empresa')->img_logo > ""){
                  $pdf->setImageScale(1.80);
                  // $pdf->SetXY(14, 5);
                  // $pdf->Image($imagen, '', '', 42, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

                  $f = str_replace('storage', 'public', session()->get('empresa')->img_logo);

                  $file = '@'.(Storage::get($f));
                  //$pdf->setJPEGQuality(75);

                  $pdf->Image($file, $x='10', $y='4', $w=0, $h=25, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
                  //$pdf->Image($file, 10, 1, 40, 18, '', null, 'M', true, 150, 'L', false, false, 0, false, false, false);

              }

                $pdf->SetFont('helvetica', 'R', 9, '', false);

                $y = 10;
                $pdf->SetXY(150, $y);
                $pdf->Write($h=0,  session('empresa')->razon, '', 0, 'L', true, 0, false, true, 0);
                $pdf->SetXY(150, $y+=5);
                $pdf->Write($h=0,  session('empresa')->direccion, '', 0, 'L', true, 0, false, true, 0);
                $pdf->SetXY(150, $y+=5);
                $pdf->Write($h=0,  session('empresa')->cpostal.' '.session('empresa')->poblacion, '', 0, 'L', true, 0, false, true, 0);

                $pdf->SetXY(150, $y+=5);
                $pdf->SetFont('helvetica', 'B', 9, '', false);
                $pdf->Write($h=0,  'Tf.: '.getTelefono(session('empresa')->telefono1), '', 0, 'L', true, 0, false, true, 0);
            //     $pdf->SetXY(150, $y+=5);
            //    $pdf->Write($h=0,  'CIF.: '.session('empresa')->cif, '', 0, 'L', true, 0, false, true, 0);

              $pdf->SetFont('helvetica', 'B', 16, '', false);

                $pdf->Ln();


              $txt = $this->compra->tipo_id == 1 ? "COMPRA-VENTA" : "C O M P R A";

            //   $pdf->SetXY(4, 5);
              $pdf->Write($h=0, $txt, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);



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
              PDF::Ln();

              // $pdf->SetFont('helvetica', '', 9);
              // $pdf->Write($h=0, session()->get('empresa')->txtpie1, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
              // $pdf->Write($h=0, session()->get('empresa')->txtpie2, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

              // $pdf->SetFont('helvetica', 'I', 8);

              $txt = $this->compra->tipo->nombre[0].' '.$this->compra->alb_ser.' '.getFecha($this->compra->fecha_compra).
                      ' %e '.request()->user()->huella.' '.date('d-m-Y H:i:s');
              $txt = str_replace('%e', session()->get('empresa')->titulo, $txt);
              $pdf->SetFont('helvetica', 'I', 9);
              $pdf->MultiCell($w=160, $h, $txt, $border='', $align='L', $fill=0, $ln=false, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
              //$pdf->Write($h=0, $txt, $link='', $fill=0, $align='L', $ln=false, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

              $pdf->SetFont('helvetica', 'R', 8);
              $pdf->MultiCell($w=40, $h, $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), $border='', $align='R', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);
              //$pdf->Cell(0, 10, 'Page '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');


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
          PDF::SetFooterMargin($this->pdf_margen_footer);

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
    private function setCabeceraClifrmCompra_klt(){


        $fecha =  getFecha($this->compra->fecha_compra);
        //$num_doc = str_replace('-','.',$this->compra->alb_ser);
        $num_doc = $this->compra->alb_ser;

        // //PDF::SetFillColor(215, 235, 235);
        //PDF::SetFillColor(234,234,234);


        PDF::setXY(140,40);
        PDF::SetFont('helvetica', 'B', 12, '', false);
        PDF::MultiCell(60, 6, $fecha.' '.$num_doc,'', 'R', 0, 0, '', '', true,0,false,true,6,'M',false);
        // PDF::SetFont('helvetica', '', 7, '', false);
        // PDF::MultiCell(118, 4,  "Nº de asiento", 'T', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);

        // PDF::setXY(165,14);
        // PDF::MultiCell(36, 8,  $num_doc,'', 'C', 0, 1, '', '', true,0,false,true,8,'M',false);

        // PDF::Ln();
        // PDF::Ln();
        PDF::setXY(14,48);


		PDF::SetFont('helvetica', '', 9, '', false);
        $fecha_dni = $this->compra->cliente->fecha_dni==null ? "" : getFecha($this->compra->cliente->fecha_dni);
		PDF::MultiCell(120, 4,  $this->compra->cliente->razon, '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(32, 4,  $this->compra->cliente->dni,'', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(30, 4,  $fecha_dni,'', 'C', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 7, '', false);
        PDF::MultiCell(118, 4,  "Nombre y Apellidos", 'T', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 4,  "", '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(30, 4,  "DNI/NIE/PAS", 'T', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 4,  "", '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(30, 4,  ("F. Validez"), 'T', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 9, '', false);
        PDF::MultiCell(82, 4,  $this->compra->cliente->direccion, '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(60, 4,  $this->compra->cliente->poblacion, '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4,  $this->compra->cliente->provincia, '', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 7, '', false);
		PDF::MultiCell(80, 4, ("Domicilio"), 'T', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 4,  "", '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(58, 4, ("Población"), 'T', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 4,  "", '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(40, 4, ("Provincia"), 'T', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 9, '', false);
		PDF::MultiCell(102, 4,  $this->compra->cliente->nacpob.' ('.$this->compra->cliente->nacpro.')', '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4,  getFecha($this->compra->cliente->fecha_nacimiento),'', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4,  getTelefono($this->compra->cliente->telefono1).' '.getTelefono($this->compra->cliente->tfmovil), '', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

		PDF::SetFont('helvetica', '', 7, '', false);
        PDF::MultiCell(100, 4, ("Lugar de Nacimiento"), 'T', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 4,  "", '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(38, 4, ("Fecha Nacimiento"), 'T', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 4,  "", '', 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(40, 4, ("Teléfono"), 'T', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
    }


    private function setBodyRecomprafrmCompra_klt(){

        try {
            $libro = Libro::where('grupo_id', $this->compra->grupo_id)
                            ->where('ejercicio', getEjercicio($this->compra->fecha_compra))
                            ->firstOrFail();

            $dias_cortesia = $libro->dias_cortesia;
        } catch (\Exception $e) {
            $dias_cortesia = 7;
        }



		PDF::SetFontSize(9);


        PDF::SetXY(15, 76);



        $papeleta = (is_null($this->compra->papeleta)) ? "" : $this->compra->papeleta;
        PDF::SetFont('helvetica', 'B', 9, '', false);
        PDF::MultiCell(34, 5, getFecha($this->compra->fecha_renovacion), '', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5, getDecimal($this->compra->imp_recu), '', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5, getDecimal($this->compra->importe_renovacion), '', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
		PDF::MultiCell(36, 5, getDecimal($this->compra->imp_pres), '', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(40, 5, $papeleta, '', 'C', 0, 1, '', '', true,0,false,true,5,'M',false);

        PDF::SetFont('helvetica', 'R', 7, '', false);
        PDF::MultiCell(32, 5,  ("Fecha tope recuperación"), "T", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 5,  "", "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(34, 5,  ("Importe de recuperación"), "T", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 5,  "", "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(34, 5,  ("Importe de renovación"), "T", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 5,  "", "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(34, 5,  ("Importe de la Compra"), "T", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(2, 5,  "", "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
        PDF::MultiCell(40, 5,  ("Papeleta"), "T", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

        PDF::SetFont('helvetica', 'R', 9, '', false);

        PDF::Ln();
        //PDF::SetXY(15, 68);
		$txt = ("  La empresa ".session("empresa")->razon." con CIF.:".session("empresa")->cif.
        " se compromete a reservar para su venta el lote con Número de asiento ".$this->compra->alb_ser.
        " comprado el día arriba indicado y descrito en el libro oficial de registro de conformidad".
        " al Real Decreto 197/1988 de 22 de febrero, compuesto los objetos más abajo detallados.\n");
        PDF::MultiCell($w=180, $h=0, $txt, $border='', $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

		$txt = ("  La recuperación se podrá realizar transcurridos ".($this->compra->dias_custodia)." días ".
				"de la fecha de este contrato NUNCA ANTES y OBLIGATORIAMENTE DEBERÁN DE AVISAR con UN DÍA de ANTELACIÓN.".
				"\n\n");

		$txt.= ("  Queda de manifiesto que pasados ".$dias_cortesia." días de la fecha tope de recuperación ".
				"y de no materializarse la misma por la parte compradora se podrá disponer de el/los objeto/s reseñados ".
				"en el libro de Registro Oficial, sin perjuicio a las partes intervinientes. Se entiende por ello ".
				"la falta de interés por la RECOMPRA. La empresa ").
				session("empresa")->razon.(" le garantiza el pago de un 20% del importe de la valoración que figura ".
				"en nuestro libro oficial de registro, en el caso de que la empresa extraviara las piezas objeto de este ".
				"contrato, siempre y cuando dicho extravío no sea ocasionado por causa mayor y usted se presentara en fechas ".
				"hábiles para su recuperación. ");
		$txt.= ("Con este pago me considero suficientemente indemnizado por la empresa ").session("empresa")->razon.".\n";

//

        PDF::MultiCell($w=180, $h=0, $txt, $border='', $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0);

    }

    private function setAutorizacionfrmCompra_klt($copia){


        if ($copia){
            PDF::SetFont('helvetica', 'B', 7, '', false);
            PDF::MultiCell(30, 4,"", "", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(22, 5,  ("AUTORIZACIÓN:"), "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::SetFont('helvetica', 'C', 7, '', false);
			PDF::MultiCell(152, 5, ("Deberá acompañar fotocopia de la documentación de la persona que autoriza y ORIGINAL del autorizado."), '', 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

			PDF::SetFont('helvetica', 'R', 7, '', false);


			PDF::MultiCell(70, 7, "", '', 'C', 0, 0, '', '', true,0,false,true,5,'B',false);
			PDF::MultiCell(36, 7, "", '', 'C', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(38, 7,  "", "", 'L', 0, 0, '', '', true,0,false,true,10,'M',false);
			PDF::MultiCell(38, 7,  "", "", 'L', 0, 1, '', '', true,0,false,true,10,'M',false);

            PDF::MultiCell(68, 5,  "Nombre y Apellidos", "T", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(2, 5,  "", "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(34, 5,  ("DNI/NIE/Pas"), "T", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(2, 5,  "", "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(36, 5,  ("Firma de quien autoriza"), "T", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
            PDF::MultiCell(2, 5,  "", "", 'L', 0, 0, '', '', true,0,false,true,5,'M',false);
			PDF::MultiCell(38, 5,  ("Firma de autorizado"), "T", 'L', 0, 1, '', '', true,0,false,true,5,'M',false);

            if (session('empresa')->getFlag(9) && $this->compra->cliente->notificar_iban == TRUE){
                try {
                    PDF::SetFont('helvetica', 'R', 9, '', false);
                    $cuenta = Cuenta::defecto()->firstOrFail();
                    $txt = "* Puede realizar su renovación a través del siguiente número de cuenta IBAN: ".getIbanPrint($cuenta->iban).", indicando en el concepto de la transferencia: RENOVAR ".$this->compra->alb_ser.".";
                    PDF::MultiCell(180, 5,  $txt, "0", 'L', 0, 1, '', '', true,0,false,true,15,'M',false);
                } catch (\Exception $e) {
                }
            }
		}

    }


}
