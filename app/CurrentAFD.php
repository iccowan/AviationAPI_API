<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrentAFD extends Model
{
    protected $table = 'afd_current';

    protected $hidden = ['id', 'updated_at', 'created_at'];
}
