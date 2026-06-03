<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\Message;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Neotharts',
            'username' => 'admin',
            'email' => 'admin@neotharts.com',
            'password' => Hash::make('password'),
        ]);

        $services = collect([
            [
                'name' => 'Headshot',
                'description' => 'Commission portrait kepala hingga bahu.',
                'starting_price' => 75000,
                'type' => 'komisi',
                'features' => json_encode(['High resolution', 'Simple background', 'Personal use']),
                'sort_order' => 1,
            ],
            [
                'name' => 'Halfbody',
                'description' => 'Commission karakter setengah badan.',
                'starting_price' => 125000,
                'type' => 'komisi',
                'features' => json_encode(['High resolution', 'Detailed rendering', 'Personal use']),
                'sort_order' => 2,
            ],
            [
                'name' => 'Fullbody',
                'description' => 'Commission karakter full body.',
                'starting_price' => 200000,
                'type' => 'komisi',
                'features' => json_encode(['Full character', 'High resolution', 'Personal use']),
                'sort_order' => 3,
            ],
            [
                'name' => 'Chibi',
                'description' => 'Commission karakter chibi.',
                'starting_price' => 60000,
                'type' => 'komisi',
                'features' => json_encode(['Cute style', 'Simple background', 'Personal use']),
                'sort_order' => 4,
            ],
        ])->map(fn ($service) => Service::updateOrCreate(
            ['name' => $service['name']],
            $service + ['is_active' => true]
        ));

        SiteSetting::setValue('tos', '<p>Terms of Service default. Silakan edit melalui admin panel.</p>');

        // No dummy artists or artworks seeded so the user can populate them manually.

        // Create Visitor data for analytics
        Visitor::factory(100)->create();

        // Create some visitors for today
        Visitor::factory(15)->today()->create();

        Message::create([
            'name' => 'Sample Client',
            'email' => 'client@example.com',
            'subject' => 'Commission Inquiry',
            'message' => 'Halo, saya ingin bertanya tentang slot commission.',
            'attachments' => [],
        ]);

        // Output seeding info
        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Username: admin');
        $this->command->info('Admin Email: admin@neotharts.com');
        $this->command->info('Admin Password: password');
    }
}
