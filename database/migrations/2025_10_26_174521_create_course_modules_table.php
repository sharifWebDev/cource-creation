<?php

// database/migrations/2024_01_01_000002_create_course_modules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseModulesTable extends Migration
{
    public function up()
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->integer('order')->default(0)->comment('Module sequence order');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');

            // Indexes
            $table->index('course_id');
            $table->index('order');
            $table->index('is_published');
            $table->index(['course_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_modules');
    }
}
