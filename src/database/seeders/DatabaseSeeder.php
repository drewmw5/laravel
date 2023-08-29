<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Drew',
            'email' => 'drewmwilliams55@gmail.com',
            'password' => '$2y$10$73LiPrw2BmtuH/c5NfKzqOzt.pu3VTDbKCzDDMW5KGWHqGSaGYSLC',
        ]);
    }
}
