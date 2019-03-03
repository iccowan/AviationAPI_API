<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AirportCord extends Model
{
    protected $table = 'airport_coords';
    protected $fillable = ['ident', 'latitude', 'longitude'];
}
