<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FAAChartUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $day1 = Carbon::create(18, 12, 6);
        do {
            $next = $day1->addDays(28);
            $year = substr($next->year, -2);
            $day = $next->day;
            if(strlen($day) == 1) {
                $day = '0'.$day;
            }
            $month = $next->month;
            if(strlen($month) == 1) {
                $month = '0'.$month;
            }
            print($month.'/'.$day.'/'.$year . "\n");
        }
        while($year < 99);
    }
}
