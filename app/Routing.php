<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Routing extends Model
{
    protected $table = 'preferred_routes';
    protected $fillable = ['id', 'origin', 'route', 'destination', 'hours1', 'hours2', 'hours3', 'type', 'area', 'altitude', 'aircraft', 'flow', 'seq', 'd_artcc', 'a_artcc'];
}
