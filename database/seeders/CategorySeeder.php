<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryTitles = [
            'Food' => 'Outcome',
            'Car' => 'Outcome',
            'Accommodation' => 'Outcome',
            'Gifts' => 'Outcome',
            'Salary' => 'Income',
        ];
        foreach ($categoryTitles as $title => $type) {
            $category = Category::where('title', $title)->first();
            if (null === $category) {
                Category::create([
                    'title' => $title,
                    'type' => $type,
                ]);
            }
        }
    }
}
