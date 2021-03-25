<?php

use App\Categoria;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EOCategoriasSeeder extends Seeder
{
    public function run()
    {

        DB::statement('UPDATE klt_productos set categoria_id = NULL');

        // $this->update();
        // return;

        Categoria::truncate();

        $cat = ['Anillos y Alianzas','Broches','Cadenas y Colgantes','Gemelos','Pendientes','Pulseras','Relojes','Otros'];

        $dt = Carbon::now();

        $data = array();
        foreach ($cat as $row){
            $data[] = [
                'nombre' => strtoupper($row),
                'username' => 'sys',
                'updated_at' => $dt,
                'created_at' => $dt,
            ];
        }

        DB::table('categorias')->insert($data);

        DB::statement('UPDATE klt_productos set categoria_id = NULL');

        $this->update();



    }

    private function update(){

        DB::statement('UPDATE klt_productos set categoria_id = 7 WHERE categoria_id IS NULL AND nombre LIKE "%RELOJ%"');
        DB::statement('UPDATE klt_productos set categoria_id = 2 WHERE categoria_id IS NULL AND nombre LIKE "%BROCHE%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%COLGANTE%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%MEDALLA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%CRUZ%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%COLLAR%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%CORDON%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%GARGANTILLA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%CADENA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 4 WHERE categoria_id IS NULL AND nombre LIKE "%GEMELO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 6 WHERE categoria_id IS NULL AND nombre LIKE "%PULSERA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 1 WHERE categoria_id IS NULL AND nombre LIKE "%ANILLO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 1 WHERE categoria_id IS NULL AND nombre LIKE "%SORTIJA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 1 WHERE categoria_id IS NULL AND nombre LIKE "%SELLO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 1 WHERE categoria_id IS NULL AND nombre LIKE "%SOLITARIO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 1 WHERE categoria_id IS NULL AND nombre LIKE "%ALIANZA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 5 WHERE categoria_id IS NULL AND nombre LIKE "%PENDIENTE%"');
        DB::statement('UPDATE klt_productos set categoria_id = 5 WHERE categoria_id IS NULL AND nombre LIKE "%ARETE%"');

        $c = DB::table('productos')->whereNull('categoria_id')->whereNull('deleted_at')->whereIn('estado_id',[1,2,3])->count();

    }
}
