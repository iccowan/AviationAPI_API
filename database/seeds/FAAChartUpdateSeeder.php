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
        DB::table('chart_update_cycles')->truncate();
        
        $day1 = Carbon::create(19, 1, 3);
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

            DB::table('chart_update_cycles')->insert([
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'updated' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        while($year < 99);
    }
}
