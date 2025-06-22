<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        // Kalau belum ada kategori, seeder ini batal jalan
        if ($categories->count() === 0) {
            $this->command->warn('Tidak ada kategori, jalankan CategorySeeder dulu.');
            return;
        }

        $data = [
            ['name' => 'Laptop ASUS ROG', 'photo' => 'asus.jpg', 'description' => 'Laptop gaming high-end'],
            ['name' => 'Meja Kayu', 'photo' => 'meja.jpg', 'description' => 'Meja kantor minimalis'],
            ['name' => 'Pulpen Pilot', 'photo' => 'pulpen.jpg', 'description' => 'Pulpen enak dipakai'],
            ['name' => 'Jaket Hoodie', 'photo' => 'jaket.jpg', 'description' => 'Cocok untuk cuaca dingin'],
        ];

        foreach ($data as $item) {
            Item::create([
                'name' => $item['name'],
                'photo' => $item['photo'],
                'description' => $item['description'],
                'category_id' => $categories->random()->id, // ambil random kategori
            ]);
        }
    }
}
