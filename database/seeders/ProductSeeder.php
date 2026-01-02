<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product::factory()->count(100)->create();
        // Category 6 -> Camilan Bayi

        $products = [
            ['name' => 'Yummy Bites Cracker 6m+ Original 25gr', 'description' => 'Yummy Bites Cracker 6m+ Original 25gr Adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil. 
1) Terbuat dari bahan organik. 
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.
7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 17000, 'weight' => 25, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'YummyBitesCracker6m+Original25gr.webp', 'category_id' => 6, 'brand_id' => 1],

            ['name' => 'Yummy Bites Cracker 6m+ Pisang 25gr', 'description' => 'Yummy Bites Cracker 6m+ Pisang 25gr Adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil. 
1) Terbuat dari bahan organik.
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.
7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 17000, 'weight' => 25, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'YummyBitesCracker6m+Pisang25gr.webp', 'category_id' => 6, 'brand_id' => 1],

            ['name' => 'Yummy Bites Cracker 6m+ Apel 25gr', 'description' => 'Yummy Bites Cracker 6m+ Apel 25gr adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil.
1) Terbuat dari bahan organik.
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 17000, 'weight' => 25, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'YummyBitesCracker6m+Apel25gr.webp', 'category_id' => 6, 'brand_id' => 1],

            ['name' => 'Yummy Bites Cracker 6m+ Strawberry 25gr', 'description' => 'Yummy Bites Cracker 6m+ Strawberry 25gr adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil.
1) Terbuat dari bahan organik.
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.
7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 17000, 'weight' => 25, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'straw25.webp', 'category_id' => 6, 'brand_id' => 1],

            ['name' => 'Yummy Bites Cracker 6M+ Blueberry 25gr', 'description' => 'Yummy Bites Cracker 6M+ Blueberry 25gr adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil.
1) Terbuat dari bahan organik.
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.
7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 17000, 'weight' => 25, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'Blueberry25.webp', 'category_id' => 6, 'brand_id' => 1],

            ['name' => 'Yummy Bites Cracker 6m+ Original 50gr', 'description' => 'Yummy Bites Cracker 6m+ Original 50gr adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil.
1) Terbuat dari bahan organik.
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.
7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 30000, 'weight' => 50, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'YummyBitesCracker6m+Original50gr.webp', 'category_id' => 8, 'brand_id' => 1],

            ['name' => 'Yummy Bites Cracker 6m+ Pisang 50gr', 'description' => 'Yummy Bites Cracker 6m+ Pisang 50gr adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil.
1) Terbuat dari bahan organik.
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.
7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 30000, 'weight' => 50, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'Pisang50.webp', 'category_id' => 8, 'brand_id' => 1],

            ['name' => 'Yummy Bites Cracker 6m+ Apel 50gr', 'description' => 'Yummy Bites Cracker 6m+ Apel 50gr adalah makanan ringan untuk santapan nutrisi si kecil mulai dari usia 6 bulan. Makanan renyah yang enak dan nikmat saat disantap si kecil agar dapat nutrisi yang lebih.Rasa nikmat bagi si kecil.
1) Terbuat dari bahan organik.
2) Kaya Vitamin dan Mineral.
3) Baik digunakan untuk si Kecil mulai dari usia 6 bulan.
4) Rice Crackers mudah lumer dimulut.
5) Allergen Free (jagung, susu, gandum, telur, kacang, ikan, kedelai, kerang, biji wijen).
6) Halal.
7) Tanpa pengawet maupun perasa buatan. Bebas Gluten.', 'price' => 30000, 'weight' => 50, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'Apel50.webp', 'category_id' => 8, 'brand_id' => 1],

            ['name' => 'Beberoll 9M+ Chocolate 40gr', 'description' => 'Yummy Bites Beberoll adalah camilan yang dibuat dari 8 macam padi-padian Korea, memiliki bentuk yang mudah digenggam si Kecil.
1). Teksturnya cocok untuk si Kecil mulai dari usia 9 bulan.
2). Beberoll dibuat dari 8 macam padi-padian Korea.
3). Tidak digoreng melainkan dipanggang.
4). Bentuknya mudah digenggam, sehingga dapat melatih perkembangan motorik si Kecil.
5). Tinggi akan kalsium dan kaya akan vitamin E.
6). Tanpa pengawet, pewarna maupun perasa buatan', 'price' => 35000, 'weight' => 40, 'stocks' => 100, 'is_hidden' => false, 'discount_amount' => 0, 'is_on_sale' => false, 'image_url' => 'BEBEROLLCOKLAT.webp', 'category_id' => 7, 'brand_id' => 1],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
