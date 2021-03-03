<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Role::create(['name'=>'Operador']);

        Permission::create(['name'=>'consultas','nombre'=>'Consultas']);
        Permission::create(['name'=>'procesos','nombre'=>'Procesos']);
        Permission::create(['name'=>'edtcaj','nombre'=>'Edita Caja']);
        Permission::create(['name'=>'excel','nombre'=>'Excel']);
        Permission::create(['name'=>'edtcli','nombre'=>'Dto. Clientes']);
        Permission::create(['name'=>'edtfec','nombre'=>'Fecha Com/Ven']);
        Permission::create(['name'=>'whatsapp','nombre'=>'WhatsApp']);

    }
}
