<?php

// database/migrations/2024_01_01_000004_create_course_module_contents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseModuleContentsTable extends Migration
{
    public function up()
    {
        Schema::create('course_module_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_module_id');
            $table->unsignedBigInteger('content_type_id');
            $table->string('title', 255);
            $table->json('content_data')->comment('Structured content based on type');
            $table->integer('order')->default(0)->comment('Content sequence order');
            $table->boolean('is_published')->default(true);
            $table->integer('estimated_duration')->default(0)->comment('Duration in minutes');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('course_module_id')
                ->references('id')
                ->on('course_modules')
                ->onDelete('cascade');

            $table->foreign('content_type_id')
                ->references('id')
                ->on('content_types')
                ->onDelete('restrict');

            // Indexes
            $table->index('course_module_id');
            $table->index('content_type_id');
            $table->index('order');
            $table->index('is_published');
            $table->index(['course_module_id', 'order']);
            $table->index('estimated_duration');
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_module_contents');
    }
}
