<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AirportData extends Model
{
    protected $table = 'airport_data';

    protected $hidden = ['id', 'updated_at', 'created_at'];
}
