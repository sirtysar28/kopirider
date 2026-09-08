<?php

namespace Database\Seeders;

use App\Models\DateStatus;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Admin account ----
        User::updateOrCreate(
            ['email' => 'admin@kopirider.id'],
            ['name' => 'Kopi Rider Admin', 'password' => bcrypt('kopirider123')],
        );

        // ---- Settings ----
        $defaults = [
            'whatsapp_number' => '6281234567890',
            'instagram_url' => 'https://instagram.com/kopirider',
            'tiktok_url' => 'https://tiktok.com/@kopirider',
            'hero_status' => 'open today · Pererenan',
            'contact_email' => 'hello@kopirider.id',
            'midtrans_enabled' => '0',
            'midtrans_environment' => 'sandbox',
            'bank_transfer_details' => "Bank BCA\nAccount no: 1234567890\nAccount name: PT Bangun Berkah Abadi",
        ];
        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
        \Illuminate\Support\Facades\Cache::forget('settings.all');

        // ---- Packages (paid) ----
        $packages = [
            [
                'name' => 'Essential',
                'slug' => 'essential',
                'description' => 'The truck, two baristas, unlimited coffee — everything a good event needs.',
                'starting_price' => 4500000,
                'currency' => 'IDR',
                'features' => [
                    'Full truck + two baristas',
                    'Unlimited coffee for all guests',
                    '3 hours of service',
                    'Setup & cleanup included',
                    'Espresso, cappuccino, latte, cold brew',
                ],
                'audience' => 'paid',
                'sort_order' => 1,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Longer service, more baristas, and treats on the rooftop deck.',
                'starting_price' => 7500000,
                'currency' => 'IDR',
                'features' => [
                    'Everything in Essential',
                    'Three baristas + waiter',
                    '5 hours of service',
                    'Rooftop deck open for photos',
                    'Sandwiches & cake add-on',
                    'Signature drink designed for your event',
                ],
                'audience' => 'paid',
                'sort_order' => 2,
            ],
            [
                'name' => 'Festival & Market (Free option)',
                'slug' => 'festival-market-free',
                'description' => 'We come at no charge and sell coffee. Organiser guarantee covers the minimum.',
                'starting_price' => 0,
                'currency' => 'IDR',
                'features' => [
                    'No upfront fee',
                    'Minimum guarantee agreed beforehand',
                    'We handle coffee, staff & power',
                    'Perfect for markets & community events',
                ],
                'audience' => 'free',
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(['slug' => $pkg['slug']], $pkg + ['is_active' => true]);
        }

        // ---- A few example calendar statuses for the next weeks ----
        $samples = [
            [now()->addDays(6)->toDateString(), 'booked', 'Wedding — deposit paid'],
            [now()->addDays(13)->toDateString(), 'enquiry', 'Corporate party asking'],
            [now()->addDays(20)->toDateString(), 'booked', 'Canggu market'],
            [now()->addDays(27)->toDateString(), 'enquiry', 'Birthday party asking'],
        ];
        foreach ($samples as [$date, $status, $note]) {
            DateStatus::updateOrCreate(['date' => $date], ['status' => $status, 'note' => $note]);
        }
    }
}
