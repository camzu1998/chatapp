<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($x=0;$x<20;$x++) {
            DB::table('messages')->insert([
                'nick' => Str::random(10),
                'content' => Str::random(10),
            ]);
        }
    }
}
