<?php

namespace Database\Seeders;

use App\Models\Stores;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'store_code'    => 'VP',
                'store_name'    => 'V.P CONSTRUCTION',
                'gstin'         => '33EGFPS1835E1ZL',
                'phone_numbers' => ['9942019660', '8838853784'],
                'email'         => 'vpconstructionrjpm@gmail.com',
                'address'       => '',
            ],
            [
                'store_code'    => 'KIRUBA',
                'store_name'    => 'KIRUBA CONSTRUCTION',
                'gstin'         => '33KXHPK7192B1ZB',
                'phone_numbers' => ['8973239384'],
                'email'         => 'mkaruppu1997@gmail.com',
                'address'       => '2/245 Srivilliputhur Main Road, Eriyan Vilakku, Sivakasi, Virudhunagar District - 626124',
            ],
            [
                'store_code'    => 'VISHNU',
                'store_name'    => 'VISHNU ENTERPRISES',
                'gstin'         => '33BAFFPV0089Q1Z1',
                'phone_numbers' => ['9159838821'],
                'email'         => null,
                'address'       => 'Namaskarithan Patti, Near Petrol Bulk, Sivakasi - 626130',
            ],
        ];

        foreach ($stores as $store) {
            Stores::updateOrCreate(
                ['store_code' => $store['store_code']],
                $store
            );
        }
    }
}