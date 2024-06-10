<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 10) as $index) {
            DB::table('recipes')->insert([
                'user_id' => $faker->numberBetween(1, 10), // Assurez-vous que ces utilisateurs existent
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'ingredients' => $faker->text,
                'steps' => $faker->text,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
