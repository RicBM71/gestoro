<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Ipuser;
use App\Empresa;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Events\UsuarioFueCreado;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->authorize('view', new User);

        $users = User::select('users.*')->Permitidos()->get();
        //$users = User::get();

        if (request()->wantsJson())
            return $users;

        return redirect()->route('home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $this->authorize('create', new User);

        $roles = Role::with('permissions')->get(); // para listar también los permisos
        $permisos = Permission::pluck('name','id');


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $this->authorize('create', new User);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        // $data['password']= str_random(8);
        //$data = $request->validated();


        if (isset($data['password']))
            $data['password'] = Hash::make($data['password']);
       else
            $data['password'] = Hash::make(Str::random(8));

        $data['username_umod'] = $request->user()->username;

        $user = User::create($data);

        //$user->assignRole('Usuario');

        //$user->givePermissionTo($request->permissions);

        // enviar email
        //UsuarioFueCreado::dispatch($user, $data['password']);
        if (request()->wantsJson())
            return ['status'=>'C','user'=>$user, 'msg' => 'EL Usuario ha sido creado'];


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //$this->authorize('view', $user);

        //return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

        $this->authorize('update', $user->load('empresas'));

        $roles = Role::with('permissions')->get(); // para listar también los permisos
        //$permisos = Permission::pluck('name','id');
        $permisos = Permission::get();

        $role_user=[];
        $data = User::find($user->id)->roles()->get();
        foreach($data as $role){
            $role_user[]=$role->name;
        }

        $permisos_user=[];
        $data = User::find($user->id)->permissions()->get();
        foreach($data as $permiso){
            $permisos_user[]=$permiso->name;
        }

        $emp_user = $user->empresas->pluck('id');

        if (session('aislar_empresas') && !esRoot())
            $empresas_mostrar = auth()->user()->empresas;
        else
            $empresas_mostrar = Empresa::flag(0)->get();

        if (request()->wantsJson())
            return [
                'user'          => $user,
                'role_user'     => $role_user,
                'permisos'      => $permisos,
                'permisos_user' => $permisos_user,
                'emp_user'      => $emp_user,
                'empresas'      => $empresas_mostrar,
                'ips'           => Ipuser::selIps($user->id)
            ];

        return redirect()->route('home');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        //return $request;
        $this->authorize('update', $user);

        $data = $request->validated();

        if($data['blocked'] && !isset($data['blocked_at']))
            $data['blocked_at'] = Carbon::now()->toDateTimeString();
        elseif($data['blocked']===false){
            $data['blocked_at'] = null;
        }



        if (isset($data['password']))
           $data['password'] = Hash::make($data['password']);

        $data['username_umod'] = $request->user()->username;

        $user->update($data);

        //$user->syncEmpresas($request->get('empresas'));

        if (request()->wantsJson())
            return ['status'=>'U','user'=>$user, 'msg' => 'EL Usuario ha sido modificado'];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user de esta manera inyectamos el modelo y de esta forma
     *          laravel busca el usuario automáticamente
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //abort(404, 'dfasdfa');

        $this->authorize('delete', $user);

        $user->delete();

        $msg = 'El usuario ha sido eliminado';

        if (request()->wantsJson()){
            return response()->json(User::Permitidos()->get());
        }


    }

    public function updatePassword(Request $request)
	{


		$rules = [
			'new_password'         => 'min:6|required|password',
			'password_confirmation' => 'required|same:new_password'
		];

		$this->validate($request, $rules);

        $user = $request->user();

        $user->username_umod = session('username');
        $user->password = Hash::make($request->input('new_password'));
        $user->fecha_expira = date('Y-m-d');
		$user->saveOrFail();

        return response('Se ha modificado correctamente la password', 200);
		//return response()->json(compact('user'));
    }

    public function updateEmpresa(Request $request, User $user){

        if ($request->empresa_id > 0){
            //$user->update(['empresa_id' => $request->empresa_id]);

            DB::table('empresa_user')->where('user_id', $user->id)
                 ->update(['activa' => false]);

            DB::table('empresa_user')->where('user_id', $user->id)
                        ->where('empresa_id', $request->empresa_id)
                        ->update(['activa' => true]);


            session(['empresa' => Empresa::find($request->empresa_id)]);

            // $usr = session()->get('user');
            // $usr['empresa_id'] = $request->empresa_id;

            // return [
            //     'user' => $usr
            // ];

        }
        else
            $user->update(['empresa_id' => 0]);

    }

    public function reset(Request $request, User $user)
    {

        $this->authorize('update', $user);

        $data['password'] = Hash::make(date('dmY'));
        $data['fecha_expira'] = null;

        $user->update($data);

        if (request()->wantsJson())
            return ['user'=>$user,'msg' => 'Password restablecida a: '.date('dmY')];

    }



}
