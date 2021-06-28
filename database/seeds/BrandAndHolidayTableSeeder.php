<?php

use Illuminate\Database\Seeder;
use App\Brand;
use App\HolidayType;

class BrandAndHolidayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     
    public function getArray()
    {
        return [
            'Unforgettable Cruise' => [
                    'UCruises: Bali',
                    'UCruises: Barbados & Grenadines',
                    'UCruises: Cape Verde',
                    'UCruises: Costa Rica & Panama',
                    'UCruises: Croatia',
                    'UCruises: Cuba',
                    'UCruises: Egypt',
                    'UCruises: Greece',
                    'UCruises: Iceland',
                    'UCruises: Maldives',
                    'UCruises: Mekong',
                    'UCruises: Seychelles',
                    'UCruises: Spain & Portugal',
            ],
            'Unforgettable Croatia' => [
                'UCroatia: Activity Holiday',
                'UCroatia: Cruise',
                'UCroatia: Cruise & Stay',
                'UCroatia: Escorted Tour',
                'UCroatia: Single Destination',
                'UCroatia: Tailor Made',
            ],
            'Unforgettable Greece' => [
                'UGCruise',
                'UGCruiseStay',
                'UGTailormade',
            ],
            'Unforgettable Travel' => [
                'Africa Safaris',
                'Cultural & Heritage',
                'Family Adventures',
            ],
        ];
    }
     
    public function run()
    {
        foreach ($this->getArray() as $key => $value) {
            $brand = Brand::create(['name' => $key]);
            foreach ($value as $holiday) {
                HolidayType::create([
                    'brand_id'  => $brand->id,
                    'name'      => $holiday,
                ]);
            }
        }
    }
}
