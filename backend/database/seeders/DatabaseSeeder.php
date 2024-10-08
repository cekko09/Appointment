<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory()->count(10)->has(Employee::factory())->create();
    }
}
