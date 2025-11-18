<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Role::factory()->count(10)->create();
        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'Admin',
            'created_by' => '1',
            'updated_by' => '1',
            'created_at' => '2025-11-18 04:35:10',
            'updated_at' => '2025-11-18 04:35:10',
        ]);
    }
}
