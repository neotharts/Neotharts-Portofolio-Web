<?php

namespace Database\Seeders;

use App\Models\Artwork;
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
            'email' => 'admin@neotharts.com',
            'password' => Hash::make('password'),
        ]);

        // Create Regular Users (Artists)
        $artists = User::factory(5)->create();

        // Create Artworks for each artist
        $artists->each(function ($artist) {
            Artwork::factory(3)
                ->published()
                ->create(['user_id' => $artist->id]);

            Artwork::factory(2)
                ->draft()
                ->create(['user_id' => $artist->id]);
        });

        // Create some artworks for admin too
        Artwork::factory(5)
            ->published()
            ->create(['user_id' => $admin->id]);

        // Create Visitor data for analytics
        Visitor::factory(100)->create();

        // Create some visitors for today
        Visitor::factory(15)->today()->create();

        // Output seeding info
        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin Email: admin@neotharts.com');
        $this->command->info('Admin Password: password');
    }
}

