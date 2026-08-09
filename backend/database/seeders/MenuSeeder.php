<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [

            // ================= DIMSUM =================

            [
                'category_id' => 1,
                'name' => 'Dimsum Sedang (6 pcs)',
                'description' => 'Dimsum Original',
                'price' => 10000,
                'stock' => 100,
                'image' => 'dimsum-original.jpg',
                'status' => true,
            ],

            [
                'category_id' => 1,
                'name' => 'Dimsum Besar (3 pcs)',
                'description' => 'Dimsum Original',
                'price' => 15000,
                'stock' => 100,
                'image' => 'dimsum-besar.jpg',
                'status' => true,
            ],

            [
                'category_id' => 1,
                'name' => 'Dimsum Besar (6 pcs)',
                'description' => 'Dimsum Original',
                'price' => 21000,
                'stock' => 100,
                'image' => 'dimsum-besar6.jpg',
                'status' => true,
            ],

            [
                'category_id' => 1,
                'name' => 'Dimsum Mentai',
                'description' => '10 pcs',
                'price' => 25000,
                'stock' => 100,
                'image' => 'mentai.jpg',
                'status' => true,
            ],

            [
                'category_id' => 1,
                'name' => 'Dimsum Keju Lumer',
                'description' => '10 pcs',
                'price' => 25000,
                'stock' => 100,
                'image' => 'keju.jpg',
                'status' => true,
            ],

            [
                'category_id' => 1,
                'name' => 'Dimsum Hot Lava',
                'description' => '10 pcs',
                'price' => 25000,
                'stock' => 100,
                'image' => 'hotlava.jpg',
                'status' => true,
            ],

            [
                'category_id' => 1,
                'name' => 'Dimsum Saos Bangkok',
                'description' => '10 pcs',
                'price' => 25000,
                'stock' => 100,
                'image' => 'bangkok.jpg',
                'status' => true,
            ],

            // ================= MINUMAN =================

            [
                'category_id' => 2,
                'name' => 'Americano',
                'description' => 'Premium Coffee',
                'price' => 8000,
                'stock' => 100,
                'image' => 'americano.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Kopi Susu',
                'description' => 'Premium Coffee',
                'price' => 12000,
                'stock' => 100,
                'image' => 'kopisusu.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Kopi Susu Gula Aren',
                'description' => 'Premium Coffee',
                'price' => 12000,
                'stock' => 100,
                'image' => 'gulaaren.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Matchaves',
                'description' => 'Premium Drink',
                'price' => 12000,
                'stock' => 100,
                'image' => 'matcha.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Redves',
                'description' => 'Premium Drink',
                'price' => 12000,
                'stock' => 100,
                'image' => 'redvelvet.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Taro Dimana',
                'description' => 'Premium Drink',
                'price' => 12000,
                'stock' => 100,
                'image' => 'taro.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Good Day Freeze',
                'description' => 'Minuman',
                'price' => 6000,
                'stock' => 100,
                'image' => 'goodday.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Dancow Cokelat',
                'description' => 'Minuman',
                'price' => 6000,
                'stock' => 100,
                'image' => 'dancow.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Zee Vanila',
                'description' => 'Minuman',
                'price' => 6000,
                'stock' => 100,
                'image' => 'zee.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Chocolatos Dark Choco',
                'description' => 'Minuman',
                'price' => 6000,
                'stock' => 100,
                'image' => 'choco.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Nutrisari Orange',
                'description' => 'Minuman',
                'price' => 6000,
                'stock' => 100,
                'image' => 'orange.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Nutrisari Blewah',
                'description' => 'Minuman',
                'price' => 6000,
                'stock' => 100,
                'image' => 'blewah.jpg',
                'status' => true,
            ],

            [
                'category_id' => 2,
                'name' => 'Aquviva',
                'description' => 'Air Mineral',
                'price' => 6000,
                'stock' => 100,
                'image' => 'aquviva.jpg',
                'status' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}