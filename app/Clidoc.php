<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Clidoc extends Model
{
    protected $fillable = [
        'cliente_id','file1', 'file2', 'username'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($clidoc){

            Storage::disk('docs')->delete($clidoc->file1);
            Storage::disk('docs')->delete($clidoc->file2);

    	});
    }
    public function scopeCliente($query, $cliente_id){

        $query->where('cliente_id', $cliente_id);

        return $query;

    }

    public function getImgEncode($file){

        if (Storage::disk('docs')->exists($file))
            $myFile = Storage::disk('docs')->get($file);
        else{
            return false;
        }

        return ($file<>'') ? 'data:image/jpeg;base64,'.base64_encode($myFile) : null;
    }

    public static function getDocumentos($cliente_id, $fecha_docu, $fecha_compra=false, $pdf=false){

        $clidoc = Clidoc::cliente($cliente_id)->first();

        // no es necesario scan docu
        if (is_null(session('empresa')->scan_doc) || session('empresa')->scan_doc=="0000-00-00"){
            return [
                'status' => 1,
                'msg'   => 'Scan desactivado',
                'delete' => false
            ];
        }

        if ($fecha_compra !== false){
            if ($fecha_compra < session('empresa')->scan_doc){
                return [
                    'status' => 1,
                    'msg'   => 'Compra anterior a fecha',
                    'delete' => false
                ];
            }
        }


//        $docu = Clidoc::cliente($cliente_id)->first();

        if (Empty($clidoc)){ //no hay documentos.
            return [
                'status' => -1,
                'msg'   => 'No hay documentación',
                'id'    => false,
                'img1'  => null,
                'img2'  => null,
                'delete' => false
            ];
        }else{
             $img1 = $clidoc->getDecript($clidoc->file1,$pdf);
             $img2 = $clidoc->getDecript($clidoc->file2,$pdf);
            //  if ($img1 === false){ // existe registro pero no ficheros, borramos registro.
            //  //    $clidoc->delete();
            //      $img1 = null;
            //  }

            // if ($img2 ===false){ // existe registro pero no ficheros, borramos registro.
            //      $img2 = null;
            // }
        }

        $delete = (esAdmin() || esPropietario($clidoc)) ? true : false;

        if (Carbon::today() > Carbon::parse($fecha_docu)->addDays(10)){
            return [
                'status' => -2,
                'msg'   => 'Documentación Caducada',
                'id'    => $clidoc->id,
                'img1'  => $img1,
                'img2'  => $img2,
                'delete' => $delete
            ];
        }

        return [
            'status' => 2,
            'msg'   => 'ok',
            'id'    => $clidoc->id,
            'img1'  => $img1,
            'img2'  => $img2,
            'delete' => $delete
        ];
    }

    private function getDecript($file,$pdf){

        if (Storage::disk('docs')->exists($file))
            $myFile = Storage::disk('docs')->get($file);
        else
            return false;


        //return ($pdf) ?($myFile) : "data:image/jpg;base64,".$myFile;
        return ($pdf) ? Crypt::decryptString($myFile) : "data:image/jpg;base64,".Crypt::decryptString($myFile);
    }


}
