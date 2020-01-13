<?php

namespace App\Imports;

use App\Banco;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BancosImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Banco([
            'entidad'   => $row[0],
            'nombre'    => $row[1],
            'bic'       => $row[2]  ,
        ]);
    }
}
