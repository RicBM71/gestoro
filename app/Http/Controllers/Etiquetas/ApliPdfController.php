<?php

namespace App\Http\Controllers\Etiquetas;

use PDF;
use App\Clase;
use App\Albalin;
use App\Etiqueta;
use App\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApliPdfController extends Controller
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

        if ($this->generarEtiquetas($data) !== false){

            Producto::where('etiqueta_id', $data['etiqueta_id'])->update(['etiqueta_id' => 5]);

            PDF::Output('apli.pdf','I');

            PDF::reset();

        }else{
            return abort(404, ' NO hay etiquetas para imprimir');
        }

    }

    private function generarEtiquetas($data){

        PDF::AddPage();

        $result = Producto::with('clase')
            ->clase($data['clase_id'])
            ->whereIn('estado_id',[1,2,3])
            ->where('etiqueta_id', $data['etiqueta_id'])
            ->whereNull('deleted_at')
            ->orderBy('referencia')
            ->get();

        if ($result->count()==0)
            return false;

		PDF::SetFont('helvetica', '', 8, '', false);
       // PDF::SetXY(4, 10);

        if ($data['fila'] != 1){
            for ($f=2;$f<=$data['fila'];$f++){ // me posiciono en la fila
                PDF::MultiCell(25, 8,  "", "0", 'L', 0, 1, '', '', true,0,false,true,8,'M',false);
                //medianil
                PDF::MultiCell(75, 4.5,  "", "0", 'L', 0, 1, '', '', true,0,false,true,4.5,'M',false);
            }
        }

        for ($c=1;$c<$data['columna'];$c++){ // me posiciono en la columna
            PDF::MultiCell(50, 8,  "", "0", 'L', 0, 0, '', '', true,0,false,true,8,'M',false);
        }

        $i=$data['columna']-1;

        $fila_pag = $data['fila'] - 1;

        $arr_nom=array("","","","");

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

                $i++;
                $text1 = ($row->referencia);

                ($row->quilates > 0) ? $metal = $row->quilates."K" : $metal=null;

                $arr_nom[$i-1]= strlen($row->nombre > 30) ? substr($row->nombre,0,30) : $row->nombre;

                if ($row->precio_venta > 0)
                    $text2 = (getDecimal($row->precio_venta,0)."€");
                elseif ($metal > '')
                        $text2 = ($metal);
                else
                    $text2 = $row->referencia;

                $hay_cuarta = false;

                if ($i%4!=0){
                    PDF::SetFont('helvetica', '', 8, '', false);
                    PDF::MultiCell(20, 8,  $text1, "0", 'L', 0, 0, '', '', true,0,false,true,8,'M',false);
                    PDF::MultiCell(5, 8,  "", "0", 'L', 0, 0, '', '', true,0,false,true,8,'M',false);
                    //PDF::SetFont('helvetica', 'B', 8, '', false);
                    PDF::MultiCell(20, 8,  $text2, "0", 'C', 0, 0, '', '', true,0,false,true,8,'M',false);
                    PDF::MultiCell(5, 8,  "", "0", 'L', 0, 0, '', '', true,0,false,true,8,'M',false);
                }
                else{ // ultima de la derecha
                    PDF::SetFont('helvetica', '', 8, '', false);
                    PDF::MultiCell(20, 8,  $text1, "0", 'L', 0, 0, '', '', true,0,false,true,8,'M',false);
                    PDF::MultiCell(5, 8,  "", "0", 'L', 0, 0, '', '', true,0,false,true,8,'M',false);
                    //PDF::SetFont('helvetica', 'B', 8, '', false);
                    PDF::MultiCell(20, 8,  $text2, "0", 'C', 0, 1, '', '', true,0,false,true,8,'M',false);

                    // medianil horizontal

                    //PDF::MultiCell(85, 4.5,  "", "", 'L', 0, 1, '', '', true,0,false,true,4.5,'M',false);

                    PDF::SetFont('helvetica', '', 6, '', false);
                    for ($i=0;$i<=3;$i++){
                        ($i==3) ? $ret = 1 : $ret = 0; // cambio linea
                        PDF::MultiCell(50, 4.5,  $arr_nom[$i], "", 'L', 0, $ret, '', '', true,0,false,true,4.5,'M',false);
                    }
                    $arr_nom=array("","","","");

                    $hay_cuarta = true;

                    $i=0;
                    $fila_pag++;
                }

                if($fila_pag >= 22){
                    PDF::AddPage();
                    $fila_pag=0;
                }
            }



        }

        if ($hay_cuarta == false){
            //fin linea
            PDF::MultiCell(20, 8,  "", "0", 'C', 0, 1, '', '', true,0,false,true,8,'M',false);

            PDF::SetFont('helvetica', '', 6, '', false);
            for ($i=0;$i<=3;$i++){
                ($i==3) ? $ret = 1 : $ret = 0; // cambio linea
                PDF::MultiCell(50, 4.5,  $arr_nom[$i], "", 'L', 0, $ret, '', '', true,0,false,true,4.5,'M',false);
            }
            $arr_nom=array("","","","");

        }

        $clase_id = $data['clase_id'];

        Producto::whereIn('estado_id',[1,2,3])
                    ->when($clase_id > 0, function ($query) use ($clase_id) {
                        return $query->where('clase_id', $clase_id);})
                    ->where('etiqueta_id', $data['etiqueta_id'])
                    ->whereNull('deleted_at')
                    ->update(['etiqueta_id' => 5]);
    }

    private function setPrepararPdf(){

        $mar_sup = 14;
        $mar_izq = 8;

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
        //PDF::SetHeaderMargin(35);
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
