<?php

namespace App\Http\Controllers\Exportar;

use PDF;
use App\Libro;
use App\Compra;
use Illuminate\Http\Request;
use App\Rules\RangoFechaRule;
use App\Exports\LibroPolExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

/**
 * LibroPol.vue
 */

class PrintLibroController extends Controller
{
    protected $page=0;
    protected $encabezado=false;

    public function index()
    {
        if (!hasConsultas()){
            return abort(403,auth()->user()->name.' NO tiene permiso de acceso - Consultas');
        }

        if (request()->wantsJson())
            return [
                'libros' => Libro::selLibrosAbiertos()
            ];
    }

    public function excel(Request $request){

        $data = $request->validate([
            'libro_id' => ['required','integer'],
            'fecha_d'  => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'  => ['required','date'],
            ]);

        $libro = Libro::findOrfail($data['libro_id']);

        if ($this->hayLotesAbiertos($libro->grupo_id, $data['fecha_d'], $data['fecha_h']) > 0){
            return abort(403, 'Hay Lotes abiertos! NO se puede enviar el libro.');
        }

        $data['codigo_pol']=$libro->codigo_pol;
        $data['grupo_id']=$libro->grupo_id;
        $data['establecimiento']=$libro->establecimiento;

        if ($libro->plantilla_excel == null)
            $data['plantilla_excel'] = 'libropol';
        else
            $data['plantilla_excel'] = 'libropol_'.$libro->plantilla_excel;

        return Excel::download(new LibroPolExport($data), 'libro.csv');

    }

    public function portada(Request $request){

        $data = $request->validate([
            'libro_id' => ['required','integer'],
            ]);

        $libro = Libro::findOrfail($data['libro_id']);

        ob_end_clean();
        $this->portadaLibroBlanco($libro->codigo_pol);


        PDF::Output('portada.pdf','I');
        PDF::reset();

    }

    public function blanco(Request $request){

        $data = $request->validate([
            'primera_pagina' => ['required','integer'],
            'paginas'        => ['required','integer'],
        ]);


        ob_end_clean();
        $this->encabezado = true;


        $this->setPrepararLibro(true);
        $this->crearLibroBlanco($data['primera_pagina'], $data['paginas']);

        PDF::Output('libro.pdf','I');

        PDF::reset();

    }

    /**
     * Genera PDF con encabezados y detalle.
     *
     * @param Request $request
     * @return void
     */
    public function completo(Request $request){

        $data = $request->validate([
            'libro_id'        => ['required','integer'],
            'fecha_d'         => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'         => ['required','date'],
            'primera_pagina'  => ['required','integer'],
            'primer_registro' => ['required','integer'],
            'ultima'          => ['required', 'boolean']
        ]);

        $this->encabezado = true;

        ob_end_clean();

        $this->setPrepararLibro(true);

        $this->generarLibro($data['libro_id'],$data['fecha_d'],$data['fecha_h'],$data['primer_registro'],$data['primera_pagina'], $data['ultima']);

        PDF::Output('libro.pdf','I');

        PDF::reset();

    }

     /**
     * Genera PDF con solo con líneas detalle.
     *
     * @param Request $request
     * @return void
     */
    public function detalle(Request $request){

        $data = $request->validate([
            'libro_id'        => ['required','integer'],
            'fecha_d'         => ['required','date', new RangoFechaRule($request->fecha_d, $request->fecha_h)],
            'fecha_h'         => ['required','date'],
            'primer_registro' => ['required','integer'],
            'ultima'          => ['required','boolean'],
        ]);

        $this->encabezado = false;

        ob_end_clean();

        $this->setPrepararLibro(false);

        $this->generarLibro($data['libro_id'],$data['fecha_d'],$data['fecha_h'],$data['primer_registro'],0, $data['ultima']);

        PDF::Output('libro.pdf','I');

        PDF::reset();

    }

    private function generarLibro($libro_id, $d, $h, $primer_registro, $pagina, $ultima){

        define("ALTOPAGINA", 205);

        $this->page = $pagina;

        $libro = Libro::findOrfail($libro_id);


        if ($this->hayLotesAbiertos($libro->grupo_id, $d, $h) > 0){
            return abort(403, 'Hay Lotes abiertos! NO se puede enviar el libro.');
        }


        $compras = Compra::with(['cliente','comlines','comlines.clase'])
            ->where('grupo_id', $libro->grupo_id)
            ->whereDate('fecha_compra', '>=', $d)
            ->whereDate('fecha_compra', '<=', $h)
            ->where('albaran', '>=', $primer_registro)
            ->orderBy('albaran')
            ->get();

        $this->newPagina();

		$altofil = 10;
		$altofil1 = 10;
		$altofilmax = 170;
		$curlinea = $reg= 0;

		$pagina_ant=0;
		foreach ($compras as $row) {

			$reg++;

			//((float) $w, (float) $h, (string) $txt, (mixed) $border, (string) $align, (boolean) $fill, (int) $ln, (float) $x, (float) $y, (boolean) $reseth, (int) $stretch, (boolean) $ishtml, (boolean) $autopadding, (float) $maxh, (string) $valign, (boolean) $fitcell)
			$nomape=($row->cliente->apellidos.', '.ucfirst(strtolower($row->cliente->nombre)));

			//$pdf2 = $pdf;
			$inicio_linea=10;

			$lineas = $row->comlines;

            $direccion = $row->cliente->direccion.' - '.$row->cliente->poblacion;

			// if (trim($row->nacpais) != trim($row->cliente->poblacion))
			// 	$provincia = $row->cliente->poblacion." (".$row->cliente->nacpais.")";
			// else
			$provincia = $row->cliente->nacpais;

			$altofil = $this->altoFilaOk($nomape,10,28,false);
			$altofil = $this->altoFilaOk($direccion,$altofil,19,false);
			$altofil = $this->altoFilaOk($provincia,$altofil,20,$lineas);

			if (PDF::GetY() + $altofil > ALTOPAGINA){
				$this->newPagina();
			}

			PDF::MultiCell(14, $altofil,  $libro->codigo_pol, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
			//PDF::MultiCell(20, $altofil,  PDF::GetY()." alto ".$altofil, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);

			PDF::MultiCell(12, $altofil,  $row->albaran, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
			PDF::MultiCell(16, $altofil,  getFecha($row->fecha_compra), 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
			//PDF::MultiCell(28, $altofil,  ($row->apellidos.', '.$row->nombre).$altofil, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
			PDF::MultiCell(28, $altofil,  $nomape, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);

			PDF::MultiCell(20, $altofil,  $row->cliente->dni, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
			PDF::MultiCell(20, $altofil,  $direccion, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
			PDF::MultiCell(20, $altofil,  $provincia, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);

			$linea = 0;
			$altofil2=$altofil;
			foreach ($lineas as $lin) {

				if ($lin->grabaciones == null) $lin->grabaciones="";
				if ($lin->colores == null) $lin->colores="";

				// if ($lin->tipo == "R")
				// 	$lin->peso=0;

				if ($lin->clase_id == 1){ // oro
					if ($lin->colores <> "")
						$quilatescol = $lin->quilates." K ".$lin->colores;
					else
						$quilatescol = $lin->quilates." K";
				}else{
					$quilatescol = $lin->colores;
				}


				if (PDF::GetY() + $altofil2 > ALTOPAGINA){
					$this->newPagina();
				}

				//$lin->concepto = $lin->concepto."###";

				if ($linea > 0){
					$inicio_linea = 5;
					$altofil2 = $this->altoFilaOk($lin->concepto,$inicio_linea,50,false);
					$altofil2 = $this->altoFilaOk($lin->grabaciones,$altofil2,22,false);
					$altofil2 = $this->altoFilaOk($quilatescol,$altofil2,20,false);

					if (PDF::GetY() + $altofil2 > ALTOPAGINA - 1){
						$this->newPagina();
					}


					PDF::MultiCell(10, $altofil2,  "", 0, 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
					PDF::MultiCell(14, $altofil2,  "", 0, 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
					//PDF::MultiCell(14, $altofil2,  $row->getAlbaran(), 0, 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
					PDF::MultiCell(104, $altofil2,  "", 0, 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
				}
				PDF::MultiCell(50, $altofil2,  $lin->concepto, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
				PDF::MultiCell(16, $altofil2,  getDecimal($lin->peso_gr), 'T', 'R', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
				PDF::MultiCell(10, $altofil2,  $lin->clase->nombre, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
				PDF::MultiCell(22, $altofil2,  $lin->grabaciones, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
				PDF::MultiCell(20, $altofil2,  $quilatescol, 'T', 'L', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);

				//PDF::MultiCell(0, 0, $txt, 0, 'J', false, 1, '', '', true, 0, false, true, 0, 'T', false);

				PDF::MultiCell(16, $altofil2,  getDecimal($lin->importe), 'T', 'R', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);

				if ($row->papeleta == 0) $row->papeleta= "-";

				if ($linea==0){
					PDF::MultiCell(20, $altofil2,  $row->papeleta, 'T', 'C', 0, 0, '', '', true,0,false,true,$altofilmax,'T',false);
					PDF::MultiCell(6, $altofil2,  "", 'T', 'L', 0, 1, '', '', true,0,false,true,$altofilmax,'T',false);
				}else{
					PDF::MultiCell(26, $altofil2,  "", 'T', 'L', 0, 1, '', '', true,0,false,true,$altofilmax,'T',false);
				}

				$linea++;
			}

        }
        $f = Carbon::parse($h);

        // si no es 31.12 borra la página
		//if (!($f->format('m') == 12 && $f->format('d') == 31)){
        if ($ultima == true){
			$pagina = PDF::PageNo();
			PDF::deletePage($pagina);
		}

    }

    private function newPagina(){


		PDF::AddPage('L', 'A4');
		// if ($encabezado)
		// 	$this->encabezado($pdf);

        $this->page++;

		PDF::SetFont('helvetica', 'R', 7, '', false);

	}

	private function altoFilaOk($texto,$altofil=0,$ancho,$lineas){

		$altofil = $this->verificarAlto($texto, $altofil, $ancho);

		if ($lineas !== false){
			//$lineas = $this->albalin->getLineasByIdAlb($id);
			foreach ($lineas as $value) {

				if ($value->grabaciones == null) $value->grabaciones="";
				if ($value->colores == null) $value->colores="";


				$texto = $value->concepto;
				$altofil = $this->verificarAlto($texto, $altofil, 40);

				$texto = $value->grabaciones;
				$altofil = $this->verificarAlto($texto, $altofil, 22);
				break;
			}
		}
		return $altofil;
	}

	private function verificarAlto($texto,$altofil=0,$ancho){

		$altofil2 = PDF::getStringHeight($ancho,$texto);

		if ($altofil2 > $altofil)
			$altofil = $altofil2;
		return round($altofil,0);
	}


    private function setPrepararLibro($cabecera){

        PDF::setHeaderCallback(function($pdf) {


            PDF::SetFont('helvetica', 'B', 8, '', false);
            if ($this->encabezado){
                PDF::SetXY(6, 5);
                PDF::Cell(75, 5, ("LIBRO REGISTRO DE BIENES: Metales preciosos"), '0', 0, 'L', false);
                PDF::SetXY(265, 5);
                PDF::Cell(22, 5, ("Página - ").$this->page, '0', 0, 'R', false);
                PDF::Ln();

                $altofil = 10;
                PDF::SetFont('helvetica', 'B', 7, '', false);
                PDF::SetFillColor(240,240,240);

                PDF::MultiCell(14, $altofil,  "C/TI.", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(12, $altofil,  "N. ORDEN", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(14, $altofil,  "FECHA", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(28, $altofil,  "APELLIDOS, NOMBRE", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);

                PDF::MultiCell(20, $altofil,  "DNI PASAPORTE", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(20, $altofil,  "DOMICILIO", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(20, $altofil,  "PROVINCIA", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(55, $altofil,  "CLASE OBJETO", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(12, $altofil,  "PESO", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(12, $altofil,  "METAL", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);


                PDF::MultiCell(18, $altofil,  "GRABA- CIONES", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::SetFont('helvetica', 'R', 6, '', false);
                PDF::MultiCell(22, $altofil,  "CLASE-PIEDRA COLOR-KT", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(15, $altofil,  "PRECIO", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(15, $altofil,  "PAPELETA", 'T', 'L', 1, 0, '', '', true,0,false,true,$altofil,'M',false);
                PDF::MultiCell(11, $altofil,  "F.VENTA", 'T', 'L', 1, 1, '', '', true,0,false,true,$altofil,'M',false);
            }

        });


                // set document information
        PDF::SetCreator(session('username'));
        PDF::SetAuthor(session('empresa')->nombre);
        PDF::SetTitle('Libro');
        PDF::SetSubject('');

        // set default header data
        //PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        // PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        // PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        // PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        //PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        //PDF::SetHeaderMargin(35);
        if ($cabecera){
            PDF::SetMargins(5, 20, 5);
            //PDF::SetFooterMargin(0);
        }else{
            PDF::setPrintFooter(false);
            PDF::SetMargins(5,22,5,true);
        }
        //PDF::SetFooterMargin(32);

        // set auto page breaks
        //PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        PDF::SetAutoPageBreak(TRUE, 5);

        // set image scale factor
        PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            PDF::setLanguageArray($l);
        }



        // ---------------------------------------------------------

    }

    // private function setCabPieLibroBlanco(&$pdf,$cabecera){

    //     $pdf->setFontSubsetting(false);
    //     $pdf->setHeaderMargin(5);
    //     $pdf->setFooterMargin(5);
    //     $pdf->setPrintFooter(false);
    //     $pdf->setPrintHeader($cabecera);

    //     if ($cabecera){
    //         //set margins
    //         $pdf->SetMargins(5,11,5,true);
    //     }else{
    //         $pdf->setPrintFooter(false);
    //         $pdf->SetMargins(5,22,5,true);
    //     }

    //     //set auto page breaks
    //     $pdf->SetAutoPageBreak(TRUE, 5);
    //     $pdf->AcceptPageBreak();


    // }


    private function crearLibroBlanco($primera_pagina,$paginas){

        $this->page = $primera_pagina;

		for ($i=1;$i<=$paginas;$i++){
            PDF::AddPage('L', 'A4');
            $this->page++;
        }
    }



    private function portadaLibroBlanco($codigo_pol){

		$altofil = 6;
		$anchocol = 120;

		//$this->setCabPieLibroBlanco(false);


		PDF::AddPage('L', 'A4');
		PDF::SetFont('helvetica', 'B', 14, '', false);

		PDF::SetXY(40, 20);
		PDF::Cell(100, 12, "LIBRO DE REGISTRO DE BIENES", '0', 0, 'L', false);

		for ($i=1;$i<=4;$i++)
			PDF::Ln();

        //PDF::SetXY(60, 40);
        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::Cell($anchocol, $altofil, "ESTABLECIMIENTO DE METALES PRECIOSOS:", '0', 0, 'R', false);
        PDF::SetFont('helvetica', 'B', 10, '', false);
        PDF::Cell($anchocol+20, $altofil, session('empresa')->nombre, '0', 0, 'L', false);
        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::Cell($anchocol, $altofil, "SITO EN:", '0', 0, 'R', false);
        PDF::SetFont('helvetica', 'B', 10, '', false);
        PDF::Cell($anchocol+20, $altofil, session('empresa')->direccion." (".session('empresa')->poblacion.")", '0', 0, 'L', false);
        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::Cell($anchocol, $altofil, "SOCIEDAD:", '0', 0, 'R', false);
        PDF::SetFont('helvetica', 'B', 10, '', false);
        PDF::Cell($anchocol+20, $altofil, session('empresa')->razon, '0', 0, 'L', false);
        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::Cell($anchocol, $altofil, "CIF:", '0', 0, 'R', false);
        PDF::SetFont('helvetica', 'B', 10, '', false);
        PDF::Cell($anchocol+20, $altofil, session('empresa')->cif, '0', 0, 'L', false);
        PDF::Ln();

        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::Cell($anchocol, $altofil, "TELEFONO:", '0', 0, 'R', false);
        PDF::SetFont('helvetica', 'B', 10, '', false);
        PDF::Cell($anchocol+20, $altofil,session('empresa')->telefono, '0', 0, 'L', false);
        PDF::Ln();

        // PDF::SetFont('helvetica', 'R', 10, '', false);
        // PDF::Cell($anchocol, $altofil, "RESPONSABLE:", '0', 0, 'R', false);
        // PDF::SetFont('helvetica', 'B', 10, '', false);
        // PDF::Cell($anchocol+20, $altofil, session('empresa')->contacto, '0', 0, 'L', false);
        // PDF::Ln();

        // PDF::SetFont('helvetica', 'R', 10, '', false);
        // PDF::Cell($anchocol, $altofil, "DNI:", '0', 0, 'R', false);
        // PDF::SetFont('helvetica', 'B', 10, '', false);
        // PDF::Cell($anchocol+20, $altofil, '', '0', 0, 'L', false);
        // PDF::Ln();

        // PDF::SetFont('helvetica', 'R', 10, '', false);
        // PDF::Cell($anchocol, $altofil, "FECHA Y LUGAR DE NACIMIENTO:", '0', 0, 'R', false);
        // PDF::SetFont('helvetica', 'B', 10, '', false);
        // PDF::Cell($anchocol+20, $altofil,'', '0', 0, 'L', false);
        // PDF::Ln();

        // PDF::SetFont('helvetica', 'R', 10, '', false);
        // PDF::Cell($anchocol, $altofil, "NOMBRE DE LOS PADRES:", '0', 0, 'R', false);
        // PDF::SetFont('helvetica', 'B', 10, '', false);
        // PDF::Cell($anchocol+20, $altofil,'', '0', 0, 'L', false);
        // PDF::Ln();


        PDF::SetFont('helvetica', 'R', 10, '', false);
        PDF::Cell($anchocol, $altofil, ("CÓDIGO IDENTIFICACIÓN GRUPO XI:"), '0', 0, 'R', false);
        PDF::SetFont('helvetica', 'B', 10, '', false);
        PDF::Cell($anchocol+20, $altofil, $codigo_pol, '0', 0, 'L', false);
        PDF::Ln();

    }

    /**
     * Undocumented function
     *
     * @param [type] $grupo_id
     * @param date $d
     * @param date $h
     * @return void
     */
    private function hayLotesAbiertos($grupo_id, $d, $h){

        return Compra::where('grupo_id', $grupo_id)
                        ->whereDate('fecha_compra','>=',$d)
                        ->whereDate('fecha_compra','<=',$h)
                        ->where('fase_id','<=',3)
                        ->get()
                        ->count();
    }


}
