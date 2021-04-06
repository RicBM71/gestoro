<?php

namespace App\Http\Controllers\Etiquetas;

use PDF;
use App\Clase;
use App\Albalin;
use App\Etiqueta;
use App\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApliPdf3x7Controller extends Controller
{


    public function index(){


        if (request()->wantsJson())
            return [
                'clases'    => Clase::selGrupoClase(),
                'etiquetas' => Etiqueta::selImprimibles(),
            ];

    }


    public function submit(Request $request){


        $data = $request->validate([
            'etiqueta_id'  => ['required','integer'],
            'fila'         => ['required','integer'],
            'columna'      => ['required','integer'],
            'clase_id'     => ['nullable','integer'],
            // 'limite'       => ['required','integer'],
        ]);

        ob_end_clean();

        $this->setPrepararPdf();

        // $data = [
        //     'etiqueta_id' => 3,
        //     'fila' => 1,
        //     'columna' => 1,
        //     'clase_id' => 6
        // ];

        $etiquetas = $this->Etiquetas3x7($data);
        //$etiquetas = $this->Etiquetas3x7($data);

        if ($etiquetas !== false){

           // Producto::where('etiqueta_id', $data['etiqueta_id'])->update(['etiqueta_id' => 5]);

            PDF::Output('apli.pdf','I');

            PDF::reset();

        }else{
            return abort(404, ' NO hay etiquetas para imprimir');
        }

    }

    private function Etiquetas3x7($data){

        PDF::AddPage();

        $result = Producto::with('clase')
            ->clase($data['clase_id'])
            ->whereIn('estado_id',[1,2,3])
            ->where('etiqueta_id', $data['etiqueta_id'])
            ->whereNull('deleted_at')
     //       ->where('stock', 1)
            ->orderBy('referencia')
            ->get();

        if ($result->count()==0)
            return false;

        PDF::SetFont('helvetica', '', 8, '', false);


        $arr_nom=array("","","","");

        $style = array(
            'position' => '',
            'align' => 'L',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        $margen_sup=14;
        $margen_izq=8;

        $h = 37; // altura etiqueta
        $w = 61.5; // ancho etiquta

        $medianil_h = $h + 1.4;
        $medianil_v = $w + 4.5;

        $i=$data['fila'] - 1;
        $j=$data['columna'] - 1;


        foreach ($result as $row){

            if ($row->stock == 1)
                $total_copias = 1;
            else{

                $stock = Albalin::validarStock($row->id);
                if ($stock == false)
                    continue;
                $total_copias = $stock;
            }

            for ($copias=0; $copias < $total_copias; $copias++) {



                $y = ($margen_sup + ($i * $medianil_h));

                $x = $margen_izq + ($j * $medianil_v) + 1;

                //PDF::SetXY($x+20, $y);
                if ($row->precio_venta > 0)
                    $text = "  ".$row->nombre."\n\n"."  PVP: ".getCurrency($row->precio_venta,0);
                else
                    $text = "  ".$row->nombre."\n\n";

                PDF::write1DBarcode($row->referencia, 'C128', $x+2, $y+3, '', 14, 0.3, $style, 'N');
                PDF::SetXY($x, $y);
                PDF::MultiCell($w, $h, $text."\n\n", "0", 'L', 0, 1, '', '', true,0,false,true,$h,'B',false);
                //PDF::SetXY($x, $y-20);
                // PDF::Circle($x,$y,10, 90, 180, null, $style6);

                $j++;
                if ($j >= 3){
                    $j = 0;
                    $i++;
                }



                if($i >= 7){
                    PDF::AddPage();
                    $i=$j=0;
                }
            }



        }

        //return;

        $clase_id = $data['clase_id'];

        Producto::whereIn('estado_id',[1,2,3])
                    ->when($clase_id > 0, function ($query) use ($clase_id) {
                        return $query->where('clase_id', $clase_id);})
                    ->where('etiqueta_id', $data['etiqueta_id'])
                    ->whereNull('deleted_at')
                    ->update(['etiqueta_id' => 5]);

    }

    private function setPrepararPdf(){

        $mar_sup = 0;
        $mar_izq = 0;

        PDF::SetMargins($mar_izq, $mar_sup, 0);

                // set document information
        PDF::SetCreator(session('username'));
        PDF::SetAuthor(session('empresa')->nombre);
        PDF::SetTitle('Etiquetas Apli Ref. 10314');
        PDF::SetSubject('');



        // set margins
        //PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        PDF::SetMargins($mar_izq, $mar_sup, 0);
        //PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
        PDF::SetHeaderMargin(0);
        PDF::SetFooterMargin(0);
        //PDF::SetFooterMargin(32);

        // set auto page breaks
        //PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        PDF::SetAutoPageBreak(TRUE, 5);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            PDF::setLanguageArray($l);
        }

    }
}
