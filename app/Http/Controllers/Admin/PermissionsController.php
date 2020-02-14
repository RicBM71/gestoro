<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function index()
    {

        $this->authorize('view',new Permission);

        $permisos = Permission::all();


    	return compact('permisos');

    }

 	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

       	// $this->authorize('view',new Permission);
    	// $permission = new Permission;

        // return view('admin.permissions.create', compact('permisos','permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		//$this->authorize('view',new Permission);

		 $data = $request->validate([
	    	'name'  => 'required|unique:permissions',
	      ]);

        $permission = Permission::create($data);

       //return redirect()->route('admin.permissions.edit', $permission)->withFlash('Permiso creado');
    }

    public function edit(Permission $permission){

        //$this->authorize('view',new Permission);

        if (request()->wantsJson())
            return compact('permission');


    }

    public function update(Request $request, Permission $permission)
    {
    	$this->authorize('view',new Permission);
    	$data = $request->validate(['name'=>'required']);

    	$permission->update($data);
    	return redirect()->route('admin.permissions.edit',$permission)->withFlash('Actualizado');
    }

/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {

        $this->authorize('view',new Permission);

        $permission->delete();

         return redirect()->route('admin.permissions.index')->withFlash('Permiso borrado!');

    }
}
