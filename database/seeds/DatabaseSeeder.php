<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // eliminar resticciones foreign-keys
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call(UsersTableSeeder::class);
        $this->call(GenericasSeeder::class);
        $this->call(ContadoresSeeder::class);
        $this->call(ClientesTableSeeder::class);
        $this->call(FasesTableSeeder::class);
        $this->call(ConceptosTableSeeder::class);
        $this->call(BancosTableSeeder::class);
        $this->call(ProductosTableSeeder::class);

        $this->call(KltEmpresaSeeder::class);
        $this->call(KltUsuariosSeeder::class);
        $this->call(KltLibrosTableSeeder::class);
        $this->call(KltClientesSeeder::class);
        $this->call(KltComprasSeeder::class);
        $this->call(KltComlinesSeeder::class);
        $this->call(KltDepositosSeeder::class);
        $this->call(KltProductosSeeder::class);
        $this->call(KltContadoresSeeder::class);
        $this->call(KltAlbaranesSeeder::class);
        $this->call(KltAlbalinSeeder::class);
        $this->call(KltCobrosSeeder::class);
        $this->call(KltCajaSeeder::class);

        // $this->call(EoEmpresasSeeder::class);
        // $this->call(EoUsuariosSeeder::class);
        // $this->call(EoLibrosSeeder::class);
        // $this->call(EoContadoresSeeder::class);
        // $this->call(EoClientesSeeder::class);
        // $this->call(EoComprasSeeder::class);
        // $this->call(EoComlinesSeeder::class);
        // $this->call(EoDepositosSeeder::class);
        // $this->call(EoAlbaranesSeeder::class);
        // $this->call(EoAlbalinSeeder::class);
        // $this->call(EoCobrosSeeder::class);
        // $this->call(EoProductosSeeder::class);
        // $this->call(EoCajaSeeder::class);

        // $this->call(GdEmpresasSeeder::class);
        // $this->call(GdUsuariosSeeder::class);
        // $this->call(GdLibrosSeeder::class);
        // $this->call(GdContadoresSeeder::class);
        // $this->call(GdClientesSeeder::class);
        // $this->call(GdComlinesSeeder::class);
        // $this->call(GdComprasSeeder::class);
        // $this->call(GdDepositosSeeder::class);
        // $this->call(GdProductosSeeder::class);
        // $this->call(GdAlbaranesSeeder::class);
        // $this->call(GdAlbalinSeeder::class);
        // $this->call(GdCobrosSeeder::class);
        // $this->call(GdCajasSeeder::class);

        // $this->call(YjEmpresasSeeder::class);
        // $this->call(YjUsuariosSeeder::class);
        // $this->call(YjLibrosSeeder::class);
        // $this->call(YjContadoresSeeder::class);
        // $this->call(YjClientesSeeder::class);
        // $this->call(YjComlinesSeeder::class);
        // $this->call(YjComprasSeeder::class);
        // $this->call(YjDepositosSeeder::class);
        // $this->call(YjProductosSeeder::class);
        // $this->call(YjAlbaranesSeeder::class);
        // $this->call(YjAlbalinSeeder::class);
        // $this->call(YjCobrosSeeder::class);
        // $this->call(YjCajaSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
