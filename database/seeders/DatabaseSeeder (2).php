<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // ServiceSeeder::class,
            // SubServiceSeeder::class,
            // UserSeeder::class,
            BarcodesTableSeeder::class,
            PermissionsTableSeeder::class,
            CurrenciesTableSeeder::class,
            MakeSeeder::class,
            VehicleModelSeeder::class,
            // MfgYearSeeder::class,
            // VehicleSeeder::class,
            // ProductCategorySeeder::class,
            // ProductSeeder::class,
            // ProductRatingSeeder::class,
            // OrderSeeder::class,
            // OrderProductSeeder::class,
            // QuestionSeeder::class,
            // AnswerSeeder::class,
            // AddressSeeder::class,
            // BookingSeeder::class,
            // BookingSubServiceSeeder::class,
            // InventorySeeder::class,
            // NotificationSeeder::class,
            // DeliverySeeder::class,
            // ScheduleSeeder::class,
            // LogSeeder::class,
        ]);
    }
}