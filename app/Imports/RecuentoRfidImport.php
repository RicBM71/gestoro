<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class RecuentoRfidImport implements ToCollection, WithCustomCsvSettings
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $i=0;

        $data=array();
        foreach ($collection as $row)
        {

            if ($row[0] == '99')
                break;

            if ($row[0] != '02')
                return abort(403, "El formato del fichero no es correcto ".$row[0]);

            if ($i==0){
                DB::table('recuentos')->where('empresa_id', session('empresa_id'))->delete();
                $i++;
                continue;
            }

            $data[]=array(
                'empresa_id'        => session('empresa_id'),
                'fecha'             => date('Y-m-d'),
                'rfid_id'           => 1,
                'rfid_producto_id'  => str_replace('#!','',$row[1]),
                'username'          => session('username'),
                'updated_at'        => Carbon::now(),
                'created_at'        => Carbon::now(),
            );

            $i++;

        }

        DB::table('recuentos')->insert($data);

    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
}
