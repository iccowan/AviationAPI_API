<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VatPilot extends Model
{
    protected $table = 'vatsim_pilots';

    protected $hidden = ['id', 'updated_at', 'created_at'];
}
