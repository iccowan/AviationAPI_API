<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VatController extends Model
{
    protected $table = 'vatsim_controllers';

    protected $hidden = ['id', 'updated_at', 'created_at'];
}
