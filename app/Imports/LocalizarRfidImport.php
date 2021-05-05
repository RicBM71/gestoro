<?php

namespace App\Imports;

use App\Recuento;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class LocalizarRfidImport implements ToCollection, WithCustomCsvSettings
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $i=0;
        $localizadas = 0;

        $data=array();
        $nuevas = array();
        foreach ($collection as $row)
        {

            if ($row[0] == '99')
                break;

            if ($row[0] <> '02')
                return abort(403, "El formato del fichero no es correcto ".$row[0]);

            if ($i==0){
                $i++;
                continue;
            }


            $id = (int) str_replace('#!','',$row[1]);

            try {


                $recuento = Recuento::where('producto_id',$id)->firstOrFail();

                $rfid_id = (int) $recuento->rfid_id + 10;

                $data=[
                    'rfid_id'           => $rfid_id,
                    'username'          => session('username'),
                ];

                $recuento->update($data);
                ++$localizadas;


            } catch (\Exception $e) {

                $nuevas[]=array(
                    'empresa_id'        => session('empresa_id'),
                    'fecha'             => date('Y-m-d'),
                    'producto_id'       => $id,
                    'estado_id'         => null,
                    'rfid_id'           => 2,
                    'username'          => session('username'),
                    'updated_at'        => Carbon::now(),
                    'created_at'        => Carbon::now(),
                );

            }

            $i++;

        }

        DB::table('recuentos')->insert($nuevas);

    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
}
