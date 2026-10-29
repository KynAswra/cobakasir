<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['name' => 'Bakwan Jagung',  'price' => 2000],
            ['name' => 'Tahu Isi',       'price' => 2500],
            ['name' => 'Pisang Goreng',  'price' => 3000],
            ['name' => 'Tempe Mendoan',  'price' => 2000],
            ['name' => 'Cireng',         'price' => 1500],
            ['name' => 'Risoles',        'price' => 3500],
            ['name' => 'Mie Goreng',     'price' => 5000],
            ['name' => 'Tahu Gejrot',    'price' => 2000],
        ];

        foreach ($menus as $menu) {
            DB::table('menus')->insert([
                ...$menu,
                'image'      => null,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
