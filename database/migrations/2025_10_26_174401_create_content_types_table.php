<?php
// database/migrations/2024_01_01_000003_create_content_types_table.php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTypesTable extends Migration
{
    public function up()
    {
        Schema::create('content_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->comment('Icon class or path');
            $table->string('color', 20)->default('#6b7280')->comment('Hex color for UI');
            $table->json('schema')->nullable()->comment('JSON schema for content validation');
            $table->json('validation_rules')->nullable()->comment('Laravel validation rules');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->boolean('has_media')->default(false)->comment('If this content type requires media files');
            $table->boolean('has_url')->default(false)->comment('If this content type requires URLs');
            $table->boolean('has_text')->default(false)->comment('If this content type requires text content');
            $table->timestamps();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->index('is_active');
            $table->index('sort_order');
        });

        DB::table('content_types')->insert([
            [
                'name' => 'Text Content',
                'slug' => 'text',
                'description' => 'Plain text or rich text content',
                'icon' => 'fas fa-file-alt',
                'color' => '#3b82f6',
                'has_media' => false,
                'has_url' => false,
                'has_text' => true,
                'schema' => json_encode(['content' => 'required|string']),
                'validation_rules' => json_encode(['content' => 'required|string']),
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Image Content',
                'slug' => 'image',
                'description' => 'Image content with optional caption',
                'icon' => 'fas fa-image',
                'color' => '#10b981',
                'has_media' => true,
                'has_url' => false,
                'has_text' => true,
                'schema' => json_encode(['image_path' => 'required|string', 'caption' => 'nullable|string']),
                'validation_rules' => json_encode(['image_path' => 'required|string', 'caption' => 'nullable|string']),
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Video Content',
                'slug' => 'video',
                'description' => 'Video content with optional thumbnail and duration',
                'icon' => 'fas fa-video',
                'color' => '#ef4444',
                'has_media' => true,
                'has_url' => false,
                'has_text' => true,
                'schema' => json_encode(['video_path' => 'required|string', 'thumbnail' => 'nullable|string', 'duration' => 'nullable|integer']),
                'validation_rules' => json_encode(['video_path' => 'required|string', 'thumbnail' => 'nullable|string', 'duration' => 'nullable|integer']),
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Document Content',
                'slug' => 'document',
                'description' => 'Document files like PDF, Word, etc.',
                'icon' => 'fas fa-file-pdf',
                'color' => '#f59e0b',
                'has_media' => true,
                'has_url' => false,
                'has_text' => true,
                'schema' => json_encode(['file_path' => 'required|string', 'file_name' => 'required|string']),
                'validation_rules' => json_encode(['file_path' => 'required|string', 'file_name' => 'required|string']),
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Link Content',
                'slug' => 'link',
                'description' => 'External link or URL content',
                'icon' => 'fas fa-link',
                'color' => '#8b5cf6',
                'has_media' => false,
                'has_url' => true,
                'has_text' => true,
                'schema' => json_encode(['url' => 'required|url', 'title' => 'required|string']),
                'validation_rules' => json_encode(['url' => 'required|url', 'title' => 'required|string']),
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Quiz Content',
                'slug' => 'quiz',
                'description' => 'Interactive quiz or assessment',
                'icon' => 'fas fa-question-circle',
                'color' => '#ec4899',
                'has_media' => false,
                'has_url' => false,
                'has_text' => true,
                'schema' => json_encode(['questions' => 'required|array']),
                'validation_rules' => json_encode(['questions' => 'required|array']),
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('content_types');
    }
}
