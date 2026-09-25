<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageFileFallbackTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_uploaded_image_is_served_when_the_storage_link_is_missing(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('services/photo.png', 'png-bytes');

        $this->get('/storage/services/photo.png')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->get('/public/storage/services/photo.png')->assertOk();
    }

    public function test_missing_files_and_unknown_types_are_not_found(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('notes.txt', 'secret');

        $this->get('/storage/services/nope.png')->assertNotFound();
        $this->get('/storage/notes.txt')->assertNotFound();
    }

    public function test_path_traversal_is_blocked(): void
    {
        Storage::fake('public');

        $this->get('/storage/..%2F..%2F..%2F.env')->assertNotFound();
        $this->get('/storage/../../.env')->assertNotFound();
    }
}
