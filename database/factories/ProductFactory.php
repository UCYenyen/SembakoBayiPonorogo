<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productNames = [
            'Baby Formula Milk', 'Organic Baby Food', 'Baby Cereal', 'Baby Snacks',
            'Baby Bodysuit', 'Baby Sleepwear', 'Baby Onesie', 'Baby Jacket',
            'Soft Plush Toy', 'Baby Rattle', 'Educational Toy', 'Baby Teether',
            'Baby Diapers', 'Baby Wipes', 'Baby Shampoo', 'Baby Lotion',
            'Baby Crib', 'Baby Bottle', 'Baby Pacifier', 'Baby Monitor',
            'Newborn Formula', 'Toddler Milk', 'Baby Juice', 'Baby Water',
            'Cotton Baby Clothes', 'Baby Pants', 'Baby Socks', 'Baby Hat',
            'Wooden Toy', 'Musical Toy', 'Baby Swing', 'Baby Walker',
            'Baby Powder', 'Baby Oil', 'Diaper Cream', 'Baby Soap',
            'Baby Bedding', 'Baby Blanket', 'Baby Pillow', 'Nursing Bottle',
        ];

        $descriptions = [
            'High quality baby product made with safe materials',
            'Premium product for your little one',
            'Certified safe and comfortable for babies',
            'Trusted brand for baby care',
            'Specially designed for newborns and toddlers',
            'Hypoallergenic and gentle on baby skin',
            'Pediatrician recommended product',
            'Made from organic and natural ingredients',
            'Perfect for daily baby care routine',
            'Best value for money baby essential',
        ];

        return [
            'name' => fake()->randomElement($productNames) . ' ' . fake()->numberBetween(1, 999),
            'description' => fake()->randomElement($descriptions) . '. ' . fake()->sentence(10),
            'price' => fake()->numberBetween(10000, 500000),
            'weight' => fake()->numberBetween(200, 2000),
            'stocks' => fake()->numberBetween(0, 200),
            'is_hidden' => fake()->boolean(20),
            'discount_amount' => fake()->numberBetween(0, 50000),
            'is_on_sale' => fake()->boolean(30), 
            'image_url' => 'images/1764958064-69331f706a035.webp',
            'category_id' => Category::inRandomOrder()->first()->id,
            'brand_id' => Brand::inRandomOrder()->first()->id,
        ];
    }
}
