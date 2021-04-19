<?php

use App\Categoria;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KlCategoriasSeeder extends Seeder
{
    public function run()
    {

        DB::statement('UPDATE klt_productos set categoria_id = NULL');

        // $this->update();
        // return;

        Categoria::truncate();

        $cat = [
            'Pendientes',
            'Cadenas',
            'Collares',
            'Colgantes',
            'Piercing',
            'Pulseras',
            'Pasamanos',
            'Sortijas',
            'Sellos',
            'Alianzas',
            'Tobilleras',
            'Novedades',
            'Relojes',
            'Bolígrafos',
            'Gemelos',
            'Otros',
        ];

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

        DB::statement('UPDATE klt_productos set categoria_id = 13 WHERE categoria_id IS NULL AND nombre LIKE "%RELOJ%"');
        //DB::statement('UPDATE klt_productos set categoria_id = 13 WHERE categoria_id IS NULL AND nombre LIKE "%BROCHE%"');
        DB::statement('UPDATE klt_productos set categoria_id = 4  WHERE categoria_id IS NULL AND nombre LIKE "%COLGANTE%"');
        DB::statement('UPDATE klt_productos set categoria_id = 4  WHERE categoria_id IS NULL AND nombre LIKE "%CHAPA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 4 WHERE categoria_id IS NULL AND nombre LIKE "%MEDALLA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 4 WHERE categoria_id IS NULL AND nombre LIKE "%CRUZ%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%COLLAR%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3 WHERE categoria_id IS NULL AND nombre LIKE "%COLLAR%"');
        DB::statement('UPDATE klt_productos set categoria_id = 4 WHERE categoria_id IS NULL AND nombre LIKE "%ROSArIO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 3  WHERE categoria_id IS NULL AND nombre LIKE "%GARGANTILLA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 2 WHERE categoria_id IS NULL AND nombre LIKE "%CADENA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 2 WHERE categoria_id IS NULL AND nombre LIKE "%CORDON%"');
        DB::statement('UPDATE klt_productos set categoria_id = 15 WHERE categoria_id IS NULL AND nombre LIKE "%GEMELO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 6 WHERE categoria_id IS NULL AND nombre LIKE "%PULSERA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 6 WHERE categoria_id IS NULL AND nombre LIKE "%PASAMAN%"');
        DB::statement('UPDATE klt_productos set categoria_id = 6 WHERE categoria_id IS NULL AND nombre LIKE "%ESCLAVA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 8  WHERE categoria_id IS NULL AND nombre LIKE "%ANILLO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 8  WHERE categoria_id IS NULL AND nombre LIKE "%SORTIJA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 8  WHERE categoria_id IS NULL AND nombre LIKE "%TU Y YO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 9 WHERE categoria_id IS NULL AND nombre LIKE "%SELLO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 9 WHERE categoria_id IS NULL AND nombre LIKE "%SOLITARIO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 9 WHERE categoria_id IS NULL AND nombre LIKE "%SORTIJA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 9 WHERE categoria_id IS NULL AND nombre LIKE "%TRESILLO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 9 WHERE categoria_id IS NULL AND nombre LIKE "%CINQUILLO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 9 WHERE categoria_id IS NULL AND nombre LIKE "%SEPTILLO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 10 WHERE categoria_id IS NULL AND nombre LIKE "%ALIANZA%"');
        DB::statement('UPDATE klt_productos set categoria_id = 1 WHERE categoria_id IS NULL AND nombre LIKE "%PENDIENTE%"');
        DB::statement('UPDATE klt_productos set categoria_id = 1 WHERE categoria_id IS NULL AND nombre LIKE "%ARETE%"');
        DB::statement('UPDATE klt_productos set categoria_id = 5 WHERE categoria_id IS NULL AND nombre LIKE "%PIERCING%"');
        DB::statement('UPDATE klt_productos set categoria_id = 14 WHERE categoria_id IS NULL AND nombre LIKE "%BOLIGRAFO%"');
        DB::statement('UPDATE klt_productos set categoria_id = 14 WHERE categoria_id IS NULL AND nombre LIKE "%PLUMA%"');

        $c = DB::table('productos')->whereNull('categoria_id')->whereNull('deleted_at')->whereIn('estado_id',[1,2,3])->count();

    }
}
