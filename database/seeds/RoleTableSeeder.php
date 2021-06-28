<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $_roles = ['Admin', 'Sales Agent', 'Corporation', 'Manager', 'Supervisor'];
        foreach ($_roles as $role) {
             Role::create(['name' => $role, 'slug' => Str::slug($role)]);
        }
    }
}
