<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$artworks = App\Models\Artwork::all();
foreach($artworks as $artwork) {
    echo 'ID: ' . $artwork->id . ', Title: ' . $artwork->title . ', Image: ' . $artwork->image . ', Published: ' . ($artwork->is_published ? 'Yes' : 'No') . PHP_EOL;
}