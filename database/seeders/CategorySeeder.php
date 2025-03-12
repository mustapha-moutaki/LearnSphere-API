<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Créer d'abord 50 catégories principales
        $categories = Category::factory(50)->create();

        // Maintenant, attribuer aléatoirement des parents parmi les catégories existantes
        foreach ($categories as $category) {
            // Choisir un parent existant ou null (catégorie principale)
            $parent = $categories->random();
            $category->parent_id = rand(0, 1) ? $parent->id : null; // 50% de chance d'avoir un parent
            $category->save();
        }
    }
}
