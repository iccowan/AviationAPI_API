<?php

namespace App\Repository;

use App\VatPilot;
use App\VatController;
use Cache;

class Vatsim
{
    CONST CACHE_KEY = 'VAT';

    public function cacheAll() {
        $p_key = self::CACHE_KEY . '.PILOTS';
        $c_key = self::CACHE_KEY . '.CONTROLLERS';

        $pilots = VatPilot::get();
        $controllers = VatController::get();

        Cache::forget($p_key);
        Cache::forever($p_key, $pilots);

        Cache::forget($c_key);
        Cache::forever($c_key, $controllers);
    }

    public function getByKey($key) {
        $key = self::CACHE_KEY . '.' . $key;

        $response = Cache::get($key);

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
