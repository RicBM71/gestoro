<?php

use App\Contador;
use Illuminate\Database\Seeder;

class ContadoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contador::truncate();

        $data = [
            'empresa_id'    => 1,
            'tipo_id'       => 3,
            'ejercicio'     => 2020,
            'ult_albaran'   => 0,
            'serie_albaran' => 'R',
            'ult_factura'   => 0,
            'serie_factura' => 'F',
            'ult_factura_auto'=> 0,
            'serie_factura_auto'=> 'FA',
            'serie_factura_abono'=> 'RR',
            'ult_factura_abono' => 0,
            'cerrado' => false,
        ];
        Contador::create($data);

        $data = [
            'empresa_id'    => 1,
            'tipo_id'       => 4,
            'ejercicio'     => 2020,
            'ult_albaran'   => 0,
            'serie_albaran' => 'A',
            'ult_factura'   => 0,
            'serie_factura' => 'G',
            'ult_factura_auto'=> 0,
            'serie_factura_auto'=> 'GA',
            'serie_factura_abono'=> 'GR',
            'ult_factura_abono' => 0,
            'cerrado' => false,
        ];
        Contador::create($data);

        $data = [
            'empresa_id'    => 1,
            'tipo_id'       => 5,
            'ejercicio'     => 2020,
            'ult_albaran'   => 0,
            'serie_albaran' => 'T',
            'ult_factura'   => 0,
            'serie_factura' => 'TF',
            'ult_factura_auto'=> 0,
            'serie_factura_auto'=> 'TG',
            'serie_factura_abono'=> 'TR',
            'ult_factura_abono' => 0,
            'cerrado' => false,
        ];
        Contador::create($data);



    }
}
