<?php

namespace Database\Seeders;

use App\Helpers\Slug;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Manager',
            'email' => 'manager@gmail.com',
            'password' => bcrypt('123456789'),
            'role' => 'manager',
            'username' => Slug::makeUser(new User(), 'Manager'),
        ]);
    }
}
