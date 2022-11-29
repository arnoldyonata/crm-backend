<?php

namespace Tests\Feature\Api;

use App\Models\File;
use App\Models\FileBulkImsi;
use App\Models\FileBulkStarterPack;
use App\Models\FileCategory;
use App\Models\FileRelationType;
use App\Models\Imsi;
use App\Models\Number;
use App\Models\Pack;
use App\Models\RowStatus;
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
        Storage::fake('s3');
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
        Storage::disk('s3')
            ->assertExists($new_file->filepath);
    }

    public function test_users_can_retrieve_file()
    {
        Storage::fake('s3');
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

    public function test_upload_bulk_success()
    {
        Storage::fake('s3');
        $this->seed([
            \Database\Seeders\ImsiTypeStatusSeeder::class,
            FileSeeder::class,
            \Database\Seeders\RowStatusSeeder::class,
        ]);
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $file_factory = new \Illuminate\Http\Testing\FileFactory();
        $content = <<<'EOD'
        id,imsi,pin,puk_1,puk_2,ki,network
        1,123456789012340,12345,123456,123456,ABCDEF012345,4G
        2,123456789012341,12345,123456,123456,ABCDEF012345,4G
        
        EOD;
        $file = $file_factory->createWithContent('test.csv', $content);
        $this->assertEquals(0, FileBulkImsi::query()->count());
        $this->assertEquals(0, Imsi::query()->count());
        $this->postJson('/api/files', [
            'file' => $file,
            'file_category_id' => FileCategory::BULK_IMSI,
        ])->assertStatus(201);
        $this->assertEquals(2, FileBulkImsi::query()->count());
        $this->assertEquals(2, Imsi::query()->count());
        $this->assertEquals(2, FileBulkImsi::query()->where('row_status_id', RowStatus::SUCCESS)->count());
        // 2nd file, imsi count should be only one
        $this->postJson('/api/files', [
            'file' => $file,
            'file_category_id' => FileCategory::BULK_IMSI,
        ])->assertStatus(201);
        $this->assertEquals(4, FileBulkImsi::query()->count());
        $this->assertEquals(2, Imsi::query()->count());
        $this->assertEquals(2, FileBulkImsi::query()->where('row_status_id', RowStatus::FAIL)->count());

        // 3rd file, different imsi, imsi count should be two
        $content_3 = <<<'EOD'
        id,imsi,pin,puk_1,puk_2,ki,network
        1,123456789012343,12345,123456,123456,ABCDEF012345,5G
        
        EOD;
        $file_3 = $file_factory->createWithContent('test.csv', $content_3);
        $this->postJson('/api/files', [
            'file' => $file_3,
            'file_category_id' => FileCategory::BULK_IMSI,
        ])->assertStatus(201);
        $this->assertEquals(5, FileBulkImsi::query()->count());
        $this->assertEquals(3, Imsi::query()->count());
        $this->assertEquals(3, FileBulkImsi::query()->where('row_status_id', RowStatus::SUCCESS)->count());
    }

    public function test_invalid_file()
    {
        \Illuminate\Support\Facades\Log::debug(__METHOD__);
        Storage::fake('s3');
        $this->seed([
            \Database\Seeders\ImsiTypeStatusSeeder::class,
            \Database\Seeders\NumberTypeStatusCategorySeeder::class,
            \Database\Seeders\RowStatusSeeder::class,
            \Database\Seeders\PackTypeSeeder::class,
            \Database\Seeders\ProductProfileNetworkSeeder::class,
            \Database\Seeders\ProductSeeder::class,

            FileSeeder::class,
        ]);

        $fake_file = UploadedFile::fake()->image('image.jpg');
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->assertEquals(0, FileBulkStarterPack::query()->count());
        $this->assertEquals(0, Imsi::query()->count());
        $this->postJson('/api/files', [
            'file' => $fake_file,
            'file_category_id' => FileCategory::BULK_STARTER_PACK,
        ])->assertStatus(422);
    }

    public function test_starter_pack()
    {
        \Illuminate\Support\Facades\Log::debug(__METHOD__);
        Storage::fake('s3');
        $this->seed([
            \Database\Seeders\ImsiTypeStatusSeeder::class,
            \Database\Seeders\NumberTypeStatusCategorySeeder::class,
            \Database\Seeders\RowStatusSeeder::class,
            \Database\Seeders\PackTypeSeeder::class,
            \Database\Seeders\ProductProfileNetworkSeeder::class,
            \Database\Seeders\ProductSeeder::class,

            FileSeeder::class,
        ]);
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $file_factory = new \Illuminate\Http\Testing\FileFactory();
        $content = <<<'EOD'
        id,imsi,pin,puk_1,puk_2,ki,network,number,product
        1,123456789012340,12345,123456,123456,ABCDEF012345,4G,7299250,Easi 4G
        2,123456789012341,12345,123456,123456,ABCDEF012345,4G,7299251,Easi 4G
        
        EOD;
        $file = $file_factory->createWithContent('pack.csv', $content);
        $this->assertEquals(0, FileBulkStarterPack::query()->count());
        $this->assertEquals(0, Imsi::query()->count());
        $this->postJson('/api/files', [
            'file' => $file,
            'file_category_id' => FileCategory::BULK_STARTER_PACK,
        ])->assertStatus(201);
        $this->assertEquals(2, FileBulkStarterPack::count());
        $this->assertEquals(2, Imsi::count());
        $this->assertEquals(2, Number::count());
        $this->assertEquals(2, Pack::count());

        // uploading same file pack number should be the same
        $this->postJson('/api/files', [
            'file' => $file,
            'file_category_id' => FileCategory::BULK_STARTER_PACK,
        ])->assertStatus(201);
        $this->assertEquals(4, FileBulkStarterPack::count());
        $this->assertEquals(2, Imsi::count());
        $this->assertEquals(2, Number::count());
        $this->assertEquals(2, Pack::count());

        // uploading same file pack number should be the same
        $same_number = <<<'EOD'
        id,imsi,pin,puk_1,puk_2,ki,network,number,product
        1,123456789012342,12345,123456,123456,ABCDEF012345,4G,7299250,Easi 4G
        2,123456789012343,12345,123456,123456,ABCDEF012345,4G,7299251,Easi 4G
        
        EOD;
        $file_same_number = $file_factory->createWithContent('pack.csv', $same_number);
        $this->postJson('/api/files', [
            'file' => $file_same_number,
            'file_category_id' => FileCategory::BULK_STARTER_PACK,
        ])->assertStatus(201);
        $this->assertEquals(2, Pack::count());
    }

    public function test_users_can_update_uploaded_file()
    {
        //upload file
        Storage::fake('s3');
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
            'relation_type_id' => 1,
            'file_category_id' => $file_category->id,
        ]);
        $old_file = File::first();

        //update file
        Storage::fake('s3');
        $new_file = UploadedFile::fake()->image('image2.jpg');

        $update_upload_response = $this->postJson('/api/files', [
            'file' => $new_file,
            'relation_id' => 1,
            'relation_type_id' => 1,
            'file_category_id' => 1,
            'file_id' => $old_file->id,
            '_method' => 'PATCH',
        ]);

        $update_upload_response->assertCreated();
        $this->assertDatabaseCount('file', 1);
        $this->assertDatabaseCount('file_relation', 1);

        $new_file = File::first();
        Storage::disk('s3')->assertExists($new_file->filepath);
        $this->assertNotEquals($old_file->filename, $new_file->filename);
    }
}
