<?php

namespace App\Repository;

use App\CurrentAFD;
use App\CurrentChart;
use App\CurrentChangeChart;
use Cache;

class Charts
{
    CONST CACHE_KEY = 'CHARTS';

    public function cacheByKey($key) {
        $key_store = self::CACHE_KEY . '.' . strtoupper($key);

        if(strtoupper($key) == 'CURRENTCHART') {
            $charts = CurrentChart::get();
        } elseif(strtoupper($key) == 'CURRENTCHANGECHART') {
            $charts = CurrentChangeChart::get();
        } elseif(strtoupper($key) == 'AFD') {
            $charts = CurrentAFD::get();
        }

        Cache::forget($key_store);
        Cache::forever($key_store, $charts);
    }

    public function getByKey($key) {
        $key_store = self::CACHE_KEY . '.' . strtoupper($key);

        a:
        $response = Cache::get($key_store);

        if($response == null) {
            if(strtoupper($key) == 'CURRENTCHART') {
                $charts = CurrentChart::get();
            } elseif(strtoupper($key) == 'CURRENTCHANGECHART') {
                $charts = CurrentChangeChart::get();
            } elseif(strtoupper($key) == 'AFD') {
                $charts = CurrentAFD::get();
            }
            Cache::forever($key_store, $charts);

            goto a;
        }

        return $response;
    }

    public function removeId($collection) {
        $response = array();
        $i = 0;
        foreach($collection as $c) {
            $response[$i] = $c;
            $i++;
        }

        return $response;
    }
}
