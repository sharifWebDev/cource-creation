<?php

// database/migrations/2024_01_01_000007_create_media_files_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaFilesTable extends Migration
{
    public function up()
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('file_type', 50)->comment('image, video, document, etc.');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size')->comment('Size in bytes');
            $table->string('disk')->default('public');
            $table->string('extension', 10);
            $table->text('caption')->nullable();
            $table->text('alt_text')->nullable();
            $table->json('metadata')->nullable()->comment('EXIF data, dimensions, etc.');
            $table->unsignedBigInteger('uploaded_by');
            $table->morphs('mediable'); // Polymorphic relationship
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uploaded_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes
            $table->index('file_type');
            $table->index('mime_type');
            $table->index('uploaded_by');
            $table->index('created_at');
            $table->index('is_public');
            $table->index('mediable_type');
            $table->index('mediable_id');
            $table->index('file_name');
            $table->index('extension');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media_files');
    }
}
