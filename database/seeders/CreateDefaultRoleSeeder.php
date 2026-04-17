<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateDefaultRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('roles')->insert([
            'name' => 'Super Admin',
            'is_delete_allowed' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('roles')->insert([
            'name' => 'Astro',
            'is_delete_allowed' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('roles')->insert([
            'name' => 'User',
            'is_delete_allowed' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \DB::table('roles')->insert([
            'name' => 'Employee',
            'is_delete_allowed' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}