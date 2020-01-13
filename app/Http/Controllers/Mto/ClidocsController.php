<?php

namespace App\Http\Controllers\Mto;

use App\Clidoc;
use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ClidocsController extends Controller
{

    public function create($cliente_id, $compra_id=0){

        if (request()->wantsJson())
            return [
                'cliente'=>Cliente::findOrFail($cliente_id),
                'compra_id' => $compra_id,
            ];
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => ['required', 'integer',Rule::unique('clidocs')],
            'img1' => ['string','nullable'],
            'img2' => ['string','nullable'],
        ]);

        $data['username'] = $request->user()->username;

        $subcarpeta = "data".intdiv($data['cliente_id']+1000,1000) * 1000;

        // $data['file1']=$subcarpeta.'/'.$data['cliente_id'].'A.jpg';

        // $img = str_replace('data:image/jpeg;base64,','',$data['img1']);
        // Storage::disk('docs')->put($data['file1'], base64_decode($img));

        // if(!is_null($data['img2'])){
        //     $img = str_replace('data:image/jpeg;base64,','',$data['img2']);
        //     $data['file2']=$subcarpeta.'/'.$data['cliente_id'].'R.jpg';
        //     Storage::disk('docs')->put($data['file2'], base64_decode($img));

        // }

        $path1 = $subcarpeta.'/'.$data['cliente_id'].'A.dat';
        $path2 = $subcarpeta.'/'.$data['cliente_id'].'R.dat';

        $this->crearImagen($data['img1'], $path1);
        $this->crearImagen($data['img2'], $path2);

        $data['file1']=$path1;
        $data['file2']=$path2;

        $reg = Clidoc::create($data);

        if (request()->wantsJson())
            return ['clidoc'=>$reg, 'message' => 'EL registro ha sido creado'];
    }

    public function crearImagen($dataUrl,$file){

		if (is_null($dataUrl)) return null;

		$dataUrlParts = explode( ",", $dataUrl);

		$imgdata =  $this->setEncryptImg($dataUrlParts[1]);

        Storage::disk('docs')->put($file, $imgdata);

		return;

    }

    // lo desactivo de momento
	public function setEncryptImg($img){

		if (is_null($img)) return null;

		//return $img;

		return Crypt::encryptString($img);
	}



     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clidoc $clidoc)
    {
        $clidoc->delete();

        if (request()->wantsJson()){
            return [
                'message' =>  'documentación borrada',
            ];
        }
    }

}
