<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrentChangeChart extends Model
{
    protected $table = 'changed_charts_current';

    protected $hidden = ['id', 'updated_at', 'created_at'];
}
