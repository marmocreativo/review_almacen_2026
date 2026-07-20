<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Manuel',
            'email' => 'marmocreativo@gmail.com',
            'password' => bcrypt('Angeles1#'),
        ]);

        $this->call([
            EmpresaSeeder::class,
            SedeContactoSeeder::class,
            TipoExamenSeeder::class,
            ArticuloSeeder::class,
        ]);
    }
}
