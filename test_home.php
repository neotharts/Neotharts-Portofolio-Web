<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$artworks = App\Models\Artwork::where('is_published', true)->orderByDesc('published_at')->take(3)->get();
echo 'Found ' . $artworks->count() . ' artworks:' . PHP_EOL;
foreach($artworks as $artwork) {
    echo '- ' . $artwork->title . ' (' . $artwork->image . ')' . PHP_EOL;
}