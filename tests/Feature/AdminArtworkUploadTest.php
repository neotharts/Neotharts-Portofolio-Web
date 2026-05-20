<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminArtworkUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_artwork_from_dashboard(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        Service::create([
            'name' => 'Headshot',
            'description' => 'Bust portrait commission',
            'starting_price' => 100000,
            'type' => 'komisi',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.artworks.store'), [
            'title' => 'Upload Test Artwork',
            'description' => 'Artwork uploaded from the dashboard test.',
            'images' => [
                UploadedFile::fake()->image('upload-test-1.png', 640, 640),
                UploadedFile::fake()->image('upload-test-2.png', 800, 600),
            ],
            'type' => 'komisi',
            'list_service' => ['Headshot'],
            'art_for' => 'myself',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.artworks.index'));

        $artwork = Artwork::firstOrFail();

        $this->assertSame('headshot', $artwork->form);
        $this->assertSame(['Headshot'], $artwork->list_service);
        $this->assertCount(2, $artwork->images);
        $this->assertSame($artwork->images[0], $artwork->image);
        $this->assertTrue($artwork->is_published);
        $this->assertNotNull($artwork->published_at);
        Storage::disk('public')->assertExists($artwork->images[0]);
        Storage::disk('public')->assertExists($artwork->images[1]);
    }

    public function test_admin_artwork_detail_shows_carousel_navigation(): void
    {
        $admin = User::factory()->admin()->create();

        $artwork = Artwork::create([
            'user_id' => $admin->id,
            'title' => 'Multi Image Artwork',
            'description' => 'Artwork with multiple gallery images.',
            'image' => 'artworks/first.webp',
            'images' => ['artworks/first.webp', 'artworks/second.webp'],
            'type' => 'komisi',
            'form' => 'headshot',
            'list_service' => ['Headshot'],
            'art_for' => 'client',
            'is_published' => true,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.artworks.show', $artwork));

        $response->assertOk();
        $response->assertSee('Multi Image Artwork');
        $response->assertSee('/ 2');
        $response->assertSee('carousel-thumbnails');
        $response->assertSee('data-carousel-next', false);
    }

    public function test_public_artwork_overlay_receives_gallery_images(): void
    {
        $admin = User::factory()->admin()->create();

        Artwork::create([
            'user_id' => $admin->id,
            'title' => 'Chibi Carousel Artwork',
            'description' => 'Published chibi artwork with multiple images.',
            'image' => 'artworks/chibi-first.webp',
            'images' => ['artworks/chibi-first.webp', 'artworks/chibi-second.webp'],
            'type' => 'komisi',
            'form' => 'chibi',
            'list_service' => ['Chibi'],
            'art_for' => 'client',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('artworks'));

        $response->assertOk();
        $response->assertSee('artworks/chibi-first.webp');
        $response->assertSee('artworks\\/chibi-second.webp');
        $response->assertSee('modal-carousel-next');
    }

    public function test_home_latest_art_excludes_chibi_artworks(): void
    {
        $admin = User::factory()->admin()->create();

        Artwork::create([
            'user_id' => $admin->id,
            'title' => 'Latest Chibi Artwork',
            'description' => 'This should not appear on home latest art.',
            'image' => 'artworks/latest-chibi.webp',
            'images' => ['artworks/latest-chibi.webp'],
            'type' => 'komisi',
            'form' => 'chibi',
            'list_service' => ['Chibi'],
            'art_for' => 'client',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Artwork::create([
            'user_id' => $admin->id,
            'title' => 'Latest Headshot Artwork',
            'description' => 'This should appear on home latest art.',
            'image' => 'artworks/latest-headshot.webp',
            'images' => ['artworks/latest-headshot.webp'],
            'type' => 'komisi',
            'form' => 'headshot',
            'list_service' => ['Headshot'],
            'art_for' => 'client',
            'is_published' => true,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Latest Chibi Artwork');
        $response->assertSee('Latest Headshot Artwork');
    }

    public function test_public_artworks_follow_custom_sort_order(): void
    {
        $admin = User::factory()->admin()->create();

        Artwork::create([
            'user_id' => $admin->id,
            'title' => 'Second Ordered Artwork',
            'description' => 'Appears second.',
            'image' => 'artworks/second-ordered.webp',
            'images' => ['artworks/second-ordered.webp'],
            'type' => 'personal',
            'form' => 'headshot',
            'list_service' => ['Headshot'],
            'art_for' => 'myself',
            'is_published' => true,
            'sort_order' => 20,
            'published_at' => now(),
        ]);

        Artwork::create([
            'user_id' => $admin->id,
            'title' => 'First Ordered Artwork',
            'description' => 'Appears first.',
            'image' => 'artworks/first-ordered.webp',
            'images' => ['artworks/first-ordered.webp'],
            'type' => 'personal',
            'form' => 'headshot',
            'list_service' => ['Headshot'],
            'art_for' => 'myself',
            'is_published' => true,
            'sort_order' => 10,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('artworks'));

        $response->assertOk();
        $response->assertSeeInOrder(['First Ordered Artwork', 'Second Ordered Artwork']);
    }
}
