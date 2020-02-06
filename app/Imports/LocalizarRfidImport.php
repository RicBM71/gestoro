<?php

namespace App\Imports;

use App\Recuento;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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

        $data=array();
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

            $recuento = Recuento::where('producto_id',$id)->first();

            \Log::info($recuento);

            if ($recuento == null)
                continue;

            $data[]=array(
                'rfid_id'           => $recuento->rfid_id + 10,
                'username'          => session('username'),
            );

            $recuento->update($data);

            $i++;

        }

    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
}
