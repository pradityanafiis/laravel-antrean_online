<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Bengkel'],
            ['name' => 'Cuci Kendaraan'],
            ['name' => 'Kecantikan'],
            ['name' => 'Kesehatan'],
            ['name' => 'Makanan'],
            ['name' => 'Pelayanan Publik'],
            ['name' => 'Pendidikan'],
            ['name' => 'Perbankan'],
            ['name' => 'Umum'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
