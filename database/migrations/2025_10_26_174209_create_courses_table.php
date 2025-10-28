<?php

// database/migrations/2024_01_01_000001_create_courses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('feature_video_path')->nullable()->comment('Stored video file path');
            $table->string('feature_video_thumbnail')->nullable();
            $table->string('slug')->unique();
            $table->string('status', 20)->default('draft'); //'draft', 'published', 'archived'
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('category_id');
            $table->index('status');
            $table->index('created_by');
            $table->index('created_at');
            $table->fulltext('title');
            $table->fulltext('description');
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
