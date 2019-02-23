<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrentChart extends Model
{
    protected $table = 'charts_current';

    protected $hidden = ['id', 'updated_at', 'created_at'];
}
