<?php

namespace App\Http\Controllers;

use App\User;
use App\Albaran;
use App\Empresa;
use App\Traspaso;
use App\Parametro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Scopes\AislarEmpresaScope;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendUpdateProductosOnline;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

         return view('home');
    }

    public function dash(Request $request)
    {

        $authUser = $request->user();

        // $admin = ($request->user()->hasRole('Root') || $request->user()->hasRole('Admin'));

        $role_user=[];
        $data = User::find($authUser->id)->roles()->get();
        foreach($data as $role){
            $role_user[]=$role->name;
        }

        $permisos_user=[];
        //$data = User::find($authUser->id)->permissions()->get();
        $data = auth()->user()->getAllPermissions();
        foreach($data as $permiso){
            $permisos_user[]=$permiso->name;
        }

        $empresas_usuario = collect();
        foreach ($authUser->empresas as $empresa){
            if ($empresa->flags[0] == false)
                continue;

            $empresa_id = $empresa->id;

            $empresas_usuario->push($empresa->id);
            $empresas[] = [
                'value' => $empresa->id,
                'text' => $empresa->titulo
            ];
        }


        $parametros = Parametro::find(1);

        // empresa seleccionada
        $e = DB::table('empresa_user')->select('empresa_id')
                                    ->where('user_id', $authUser->id)
                                    ->where('activa', true)
                                    ->get()->first();
        if ($e != null)
            $empresa_id = $e->empresa_id;
        else{
            DB::table('empresa_user')->where('user_id', $authUser->id)
                        ->where('empresa_id', $empresa_id)
                        ->update(['activa' => true]);
        }

        $empresa = Empresa::findOrFail($empresa_id);

        $user = [
            'id'            => $authUser->id,
            'name'          => $authUser->name,
            'username'      => $authUser->username,
            'avatar'        => $authUser->avatar,
            'empresa_id'    => $empresa_id,
            'empresa_nombre'=>$empresa->titulo,
            'roles'         => $role_user,
            'permisos'      => $permisos_user,
            'empresas'      => $empresas,
            'parametros'    =>$parametros,
            'img_fondo'     => $empresa->img_fondo,
            'aislar_empresas'  => $parametros->aislar_empresas
        ];

        // envio mail de modificación de productos
        $this->productosOnline($parametros->email_productos_online, $empresa->razon);


       // de momento no quito filtros, ya veremos.
        $this->unloadSession($request);

        $jobs  = DB::table('jobs')->count();

        session([
            'empresa_id'       => $empresa_id,
            'empresa'          => Empresa::find($empresa_id),
            'username'         => $authUser->username,
            'empresas_usuario' => $empresas_usuario,
            'parametros'       => $parametros,
            'aislar_empresas'  => $parametros->aislar_empresas
            ]);

        if (request()->wantsJson())
            return [
                'user'      => $user,
                'expired'   => $this->verificarExpired($request),
                'authuser'  => $authUser,
                'jobs'      => $jobs,
                'traspasos' => Traspaso::where('proveedora_empresa_id', session('empresa_id'))
                                        ->where('situacion_id',1)->get()->count()
            ];
    }

    public function avatar(Request $request){

        $request->validate([
    		'avatar' => 'required|image|max:256'	//jpeg png, gif, svg
    	]);

        $user = $request->user();

    	$foto = request()->file('avatar')->store('avatars','public');


    	$fotoUrl = Storage::url($foto);

    	// 	//insert en la tabla photos
    	$user->update([
    	 	'avatar'	=> $fotoUrl,
    	 	'id'         => $user->id
        ]);

        return ['url'=>$fotoUrl];

    }

    public function destroy(Request $request)
    {

        $user = $request->user();

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

    /**
     *  Descarga todos los filtros al pasar por inicio
     */
    private function unloadSession($request){
        $data = $request->session()->all();
        foreach ($data as $key => $value){
            if (strstr($key, '_', true)=='filtro'){
                $request->session()->forget($key);
            }
        }
    }

    public function expired(){
    }

    public function verificarExpired($request){

        if ($request->user()->expira != 0 || is_null($request->user()->fecha_expira)){

            $f = Carbon::parse($request->user()->fecha_expira);
            $dias = $f->diffInDays(Carbon::now());

            if ($dias > ($request->user()->expira)  || is_null($request->user()->fecha_expira))
                return true;
        }
        return false;
    }

    private function productosOnline($email_productos_online, $razon)
    {

        if ($email_productos_online == null)
            return 0;

        $hoy = Carbon::now();

        $select=DB::getTablePrefix().'productos.referencia,'.DB::getTablePrefix().'productos.nombre, albaran, serie_albaran,'.DB::getTablePrefix().'estados.nombre AS estado';

        $albaranes = DB::table('albaranes')->select(DB::raw($select))
                        ->join('albalins','albalins.albaran_id','=','albaranes.id')
                        ->join('productos','albalins.producto_id','=','productos.id')
                        ->join('estados','productos.estado_id','=','estados.id')
                        ->where('albaranes.tipo_id', 3)
                        ->whereDate('albaranes.updated_at', '<', $hoy)
                        ->where('productos.online', 1)
                        ->where('albaranes.online', 0)
                        ->whereNull('albaranes.deleted_at')
                        ->orderBy('referencia')
                        ->get();

        if ($albaranes->count() > 0){

            $from = config('mail.from.address');
            $from = str_replace('info','noreply', $from);

            $data = [
                'razon'=> $razon,
                'to'   => $email_productos_online,
                'from' => $from,
                'albaranes' => $albaranes
            ];

            // con esto previsualizamos el mail
            //return new Factura($data);


            // $data_alb=['online' => 1];

            // DB::table('albaranes')->where('albaranes.tipo_id', 3)
            //                     ->whereDate('albaranes.updated_at', '<', $hoy)
            //                     ->where('albaranes.online', 0)
            //                     ->whereNull('albaranes.deleted_at')
            //                     ->update($data_alb);

            dispatch(new SendUpdateProductosOnline($data));

            return $albaranes;
        }
        else{
            return 0;
        }


    }
}
