<?php

namespace Database\Seeders;

use App\Models\Notice;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a store
        $store = Store::create([
            'name' => 'Store 1',
            'slug' => 'store-1',
            'created_by' => 1, // Replace with the user ID of the store owner
            'users' => json_encode(['user1@example.com', 'user2@example.com']),
            'shopify_store_public_key' => '4a01214a8c385ce27bb6cf63a722a9c2',
            'shopify_store_private_key' => 'b864115ead30786c8cdc4f7b58ef3f36',
            'notices' => null, // Set it to null for now
        ]);

        // Associate the store with a notice (you can change the notice ID accordingly)
        $notice = Notice::create([
            'title' => 'Important Notice',
            'content' => 'This is an important notice.',
        ]);

        // Update the store's notices foreign key with the notice's ID
        $store->update(['notices' => $notice->id]);
    }
}
