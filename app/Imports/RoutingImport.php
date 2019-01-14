<?php

namespace App\Imports;

use App\Routing;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RoutingImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Routing([
            'origin' => $row['orig'],
            'route' => $row['route_string'],
            'destination' => $row['dest'],
            'hours1' => $row['hours1'],
            'hours2' => $row['hours2'],
            'hours3' => $row['hours3'],
            'type' => $row['type'],
            'area' => $row['area'],
            'altitude' => $row['altitude'],
            'aircraft' => $row['aircraft'],
            'flow' => $row['direction'],
            'seq' => $row['seq'],
            'd_artcc' => $row['dcntr'],
            'a_artcc' => $row['acntr']
        ]);
    }
}
