<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Alliance Game Distributors',
                'contact_name' => 'John Smith',
                'email' => 'orders@alliancegames.example',
                'phone' => '1-800-555-0100',
                'account_number' => 'AGD-00123',
                'credit_limit' => 10000.00,
                'payment_terms_days' => 30,
                'notes' => 'Primary distributor for Pokemon & MTG. Net 30 terms.',
            ],
            [
                'name' => 'Southern Hobby',
                'contact_name' => 'Sarah Lee',
                'email' => 'sales@southernhobby.example',
                'phone' => '1-800-555-0200',
                'account_number' => 'SH-456',
                'credit_limit' => 5000.00,
                'payment_terms_days' => 15,
                'notes' => 'Good for Yu-Gi-Oh and One Piece. Faster shipping.',
            ],
            [
                'name' => 'GTS Distribution',
                'contact_name' => 'Mike Wong',
                'email' => 'accounts@gtsdist.example',
                'phone' => '1-800-555-0300',
                'account_number' => 'GTS-789',
                'credit_limit' => 7500.00,
                'payment_terms_days' => 30,
                'notes' => 'Lorcana specialist. Order by Tuesday for Friday delivery.',
            ],
            [
                'name' => 'Local Direct Imports',
                'contact_name' => 'Tom Nguyen',
                'email' => 'tom@localimports.example',
                'phone' => '555-0401',
                'account_number' => null,
                'credit_limit' => 2000.00,
                'payment_terms_days' => 7,
                'notes' => 'Local importer for Asian market releases. Cash or 7-day terms.',
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(['name' => $data['name']], $data);
        }

        $this->command->info('Suppliers seeded.');
    }
}
