<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Avatar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AvatarsController extends Controller
{
   public function store(User $user)
    {

    	$this->validate(request(),[
    		'avatar' => 'required|image|max:256'	//jpeg png, gif, svg
    	]);

     //    $extension = request()->file('avatar')->extension();

    	// $path = Storage::disk('public')->putFileAs(
     //        'avatars', request()->file('avatar'), $user->id.'.'.$extension
     //    );

    	//$foto = request()->file('foto');

    	//foto laravel lo convierte en un instancia de la clase uploadedfiles
    	// por lo que tenemos varios métodos, store
    	// guarda la imagen en storage/public
    			//TODO: probar con carpeta
    	//$fotoUrl = $foto->store('public');
    	//return Storage::url($fotoUrl);

    	//dejar así:
    	$foto = request()->file('avatar')->store('avatars','public');


    	$fotoUrl = Storage::url($foto);

    	// 	//insert en la tabla photos
    	$user->update([
    	 	'avatar'	=> $fotoUrl,
    	 	'id'         => $user->id
        ]);

        return ['url'=>$fotoUrl];

        // esto puede sustitur al create de arriba, quizás de momento lo veo más claro
        // cmomo está
        // $post->fotos()->create[
        //     'url' = > $fotoUrl
        // ]
    }
    public function destroy(User $user)
    {

       $fotoPath = str_replace('storage', 'public', $user->avatar);
       $user->update([
            'avatar'    =>  null,
            'id'         => $user->id
        ]);

       // dd($fotoPath);

        Storage::delete($fotoPath);

        if (request()->wantsJson())
            return ['msg' => 'Avatar eliminado'];

    }
}
