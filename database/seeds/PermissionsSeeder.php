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

        // Role::create(['name'=>'Operador']);

        // Permission::create(['name'=>'consultas','nombre'=>'Consultas']);
        // Permission::create(['name'=>'procesos','nombre'=>'Procesos']);
        // Permission::create(['name'=>'edtcaj','nombre'=>'Edita Caja']);
        // Permission::create(['name'=>'excel','nombre'=>'Excel']);
        // Permission::create(['name'=>'edtcli','nombre'=>'Dto. Clientes']);
        // Permission::create(['name'=>'edtfec','nombre'=>'Fecha Com/Ven']);
        // Permission::create(['name'=>'whatsapp','nombre'=>'WhatsApp']);

        // Permission::create(['name'=>'deldep','nombre'=>'Borra depósitos']);
        // Permission::create(['name'=>'delcob','nombre'=>'Borra cobros']);
        // Permission::create(['name'=>'edtalb','nombre'=>'Edita Albarán']);
        // Permission::create(['name'=>'edtcom','nombre'=>'Edita Compras']);

        // Permission::create(['name'=>'mail','nombre'=>'Envía mail']);

        // Permission::create(['name'=>'edtubi','nombre'=>'Cambia Ubicación']);
        // Permission::create(['name'=>'delalb','nombre'=>'Borra Albaranes']);
        // Permission::create(['name'=>'delcaj','nombre'=>'Borra Caja']);

        Permission::create(['name'=>'abono','nombre'=>'Abonos']);

    }
}
