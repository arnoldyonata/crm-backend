<?php

namespace Tests\Feature\Api;

use App\Models\File;
use App\Models\FileCategory;
use App\Models\FileRelationType;
use App\Models\User;
use Database\Seeders\FileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_upload_file()
    {
        Storage::fake('local');
        $this->seed(FileSeeder::class);
        $user = User::factory()->create();
        $file_category = FileCategory::first();
        $fake_file = UploadedFile::fake()->image('image.jpg');

        Sanctum::actingAs($user);
        $this->assertDatabaseCount('file', 0);
        $this->assertDatabaseCount('file_relation', 0);

        $response = $this->postJson('/api/files', [
            'file' => $fake_file,
            'relation_id' => 1,
            'relation_type_id' => FileRelationType::CUSTOMER,
            'file_category_id' => $file_category->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount('file', 1);
        $this->assertDatabaseCount('file_relation', 1);
        $new_file = File::first();
        $response->assertJsonPath('id', $new_file->id);
        Storage::disk('local')
            ->assertExists('/photos/'.$fake_file->hashName());
    }

    public function test_users_can_retrieve_file()
    {
        Storage::fake('local');
        $this->seed(FileSeeder::class);
        $user = User::factory()->create();
        $file_category = FileCategory::first();
        $fake_file = UploadedFile::fake()->image('image.jpg');
        Sanctum::actingAs($user);
        $this->postJson('/api/files', [
            'file' => $fake_file,
            'relation_id' => 1,
            'relation_type_id' => FileRelationType::CUSTOMER,
            'file_category_id' => $file_category->id,
        ])->assertCreated();

        $new_file = File::first();
        $this->getJson('/api/files/'.$new_file->id)
            ->assertOk()
            ->assertJsonStructure([
                'url',
            ]);
    }
}
