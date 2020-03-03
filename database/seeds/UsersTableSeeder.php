<?php

use App\User;
use App\Avatar;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Permission::truncate();
        Role::truncate();
        User::truncate();

        $rootRole = Role::create(['name'=>'Root']);
        // Admin: permite acceso a usuarios
        $adminRole = Role::create(['name'=>'Admin']);
        // Supervisor:
        // permite ampliaciones en fecha menores a las del día.
        $supRole = Role::create(['name'=>'Supervisor']);

        $gestRole = Role::create(['name'=>'Gestor']);

        Permission::create(['name'=>'addcom','nombre'=>'Crea Compras']);
        Permission::create(['name'=>'addven','nombre'=>'Crea Ventas']);
        Permission::create(['name'=>'delcom','nombre'=>'* Borra Compras/Edt Fecha']);
        Permission::create(['name'=>'reacom','nombre'=>'* Reabre Compras']);
        Permission::create(['name'=>'salefe','nombre'=>'* Saltar Límite Efectivo']);
        Permission::create(['name'=>'liquidar','nombre'=>'Puede Liquidar']);
        Permission::create(['name'=>'edtpro','nombre'=>'Edita Productos']);
        Permission::create(['name'=>'factura','nombre'=>'Facturación']);
        Permission::create(['name'=>'authtras','nombre'=>'Traspasos']);
        Permission::create(['name'=>'harddel','nombre'=>'* Hard Delete']);
        Permission::create(['name'=>'scan','nombre'=>'Scanea Docu']);
        Permission::create(['name'=>'users','nombre'=>'Usuarios']);
        Permission::create(['name'=>'edtfac','nombre'=>'*Edita Facturas']);
        Permission::create(['name'=>'desloc','nombre'=>'*Deslocalizar']);

    //     $user = new User;

    //     $user->name = "Ric";
	// 	$user->email = "info@sanaval.com";
    //     $user->username = "ricardo.bm";
    //     $user->huella = "RBM";
    //     $user->fecha_expira = date('Y-m-d');
	// 	$user->password = Hash::make('123');
    //     $user->save();

    //     $user->givePermissionTo('reacom');
    //     $user->givePermissionTo('liquidar');
    //     $user->givePermissionTo('addcom');
    //     $user->givePermissionTo('addven');
    //     $user->givePermissionTo('users');
    //     $user->givePermissionTo('edtpro');
    //     $user->assignRole($rootRole);
    //     $user->assignRole($adminRole);
    //     $user->assignRole($supRole);

    //     $user = new User;

    //     $user->name = "User 1";
	// 	$user->email = "info2@sanaval.com";
    //     $user->username = "usuario.1";
    //     $user->huella = "ABC";
	// 	$user->password = Hash::make('123');
    //     $user->save();

    //     $supRole->givePermissionTo('addcom');
    //     $supRole->givePermissionTo('authtras');

        //$per_edit->assingPermission();


        // for ($i=2; $i <= 10  ; $i++) {

        //     $user = new User;

        //     $user->name = "User ".$i;
        //     $user->email = "user".$i."@rr.es";
        //     $user->username = "User".$i;
        //     $user->empresa_id = 1;
        //     $user->password = Hash::make('123');
        //     $user->save();
        //     $user->assignRole($userRole);
        // }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
