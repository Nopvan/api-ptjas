<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Elektronik', 'Furniture', 'Alat Tulis', 'Pakaian', 'Lainnya'];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
