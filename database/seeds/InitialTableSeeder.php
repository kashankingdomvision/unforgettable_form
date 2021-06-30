<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = file_get_contents(database_path() . '/seeds/sql_dump/currencies.sql');
        DB::statement($sql);
    }
}
