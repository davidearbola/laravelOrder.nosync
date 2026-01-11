<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific customers
        $customers = [
            [
                'name' => 'Mario Rossi',
                'email' => 'mario.rossi@example.com',
                'address' => 'Via Roma 123, Milano',
                'phone' => '+39 02 1234567',
            ],
            [
                'name' => 'Luigi Bianchi',
                'email' => 'luigi.bianchi@example.com',
                'address' => 'Via Garibaldi 45, Roma',
                'phone' => '+39 06 7654321',
            ],
            [
                'name' => 'Anna Verdi',
                'email' => 'anna.verdi@example.com',
                'address' => 'Corso Vittorio 78, Torino',
                'phone' => '+39 011 9876543',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create additional random customers
        Customer::factory(10)->create();
    }
}
