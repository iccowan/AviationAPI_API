<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrentChangeChart extends Model
{
    protected $table = 'changed_charts_current';
    protected $fillable = ['id', 'state', 'state_full', 'city', 'volume', 'airport_name', 'military', 'faa_ident', 'icao_ident', 'chart_seq', 'chart_code', 'chart_name', 'pdf_name', 'pdf_path', 'expiration', 'created_at', 'updated_at'];

}