<?php

namespace App\Imports;

use App\AirportCord;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AirportCordsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AirportCord([
            'ident' => $row['ident'],
            'latitude' => $row['latitude_deg'],
            'longitude' => $row['longitude_deg']
        ]);
    }
}
