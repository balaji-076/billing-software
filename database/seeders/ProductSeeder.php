<?php

namespace Database\Seeders;

use App\Models\Products;
use App\Models\Stores;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $store = Stores::where('store_code', 'KIRUBA')->first();

        if (!$store) {
            $this->command->error('KIRUBA store illa. Munnadi StoreSeeder run pannunga.');
            return;
        }

        $products = [
            ['KIR-CEM-001', 'Ultratech Cement',        'Cement',    'OPC 53 Grade, 50kg bag',          450,   420,   'bag',   100],
            ['KIR-CEM-002', 'Dalmia Cement',           'Cement',    'PPC, 50kg bag',                   430,   405,   'bag',   80],
            ['KIR-STL-001', 'TMT Steel Bar 8mm',       'Steel',     'Fe 500D TMT bar, 12m length',     520,   495,   'piece', 200],
            ['KIR-STL-002', 'TMT Steel Bar 12mm',      'Steel',     'Fe 500D TMT bar, 12m length',     1150,  1100,  'piece', 150],
            ['KIR-BRK-001', 'Red Clay Brick',          'Bricks',    'Standard size red brick',         9,     8,     'piece', 5000],
            ['KIR-BRK-002', 'Fly Ash Brick',           'Bricks',    'Fly ash brick 9x4x3 inch',        7,     6.5,   'piece', 3000],
            ['KIR-SND-001', 'M-Sand',                  'Sand',      'Manufactured sand, per unit',     4500,  4300,  'unit',  25],
            ['KIR-SND-002', 'River Sand',              'Sand',      'Filtered river sand, per unit',   6500,  6200,  'unit',  15],
            ['KIR-AGG-001', 'Blue Metal 20mm',         'Aggregate', '20mm jelly, per unit',            3800,  3600,  'unit',  30],
            ['KIR-PNT-001', 'Asian Paints Tractor Emulsion', 'Paint', 'Interior emulsion, 20 litre',   4200,  3950,  'bucket', 40],
        ];

        foreach ($products as [$sku, $name, $category, $desc, $original, $discount, $unit, $qty]) {
            Products::updateOrCreate(
                ['store_id' => $store->id, 'sku' => $sku],
                [
                    'product_name'        => $name,
                    'category'            => $category,
                    'description'         => $desc,
                    'original_price'      => $original,
                    'discount_price'      => $discount,
                    'unit'                => $unit,
                    'image_url'           => null,
                    'cloudinary_public_id'=> null,
                    'informational_qty'   => $qty,
                    'status'              => 'active',
                ]
            );
        }
    }
}