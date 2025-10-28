<?php
// database/migrations/2024_01_01_000005_create_course_categories_table.php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image_path')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Self-referencing for sub-categories
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('course_categories')
                  ->onDelete('cascade');

            // Indexes
            $table->index('parent_id');
            $table->index('is_active');
            $table->index('slug');
            $table->index('sort_order');
            $table->index('created_at');
            $table->fulltext('name');
            $table->fulltext('description');
        });

        // Insert default categories - CORRECTED VERSION
        DB::table('course_categories')->insert([
            [
                'name' => 'Programming',
                'slug' => 'programming',
                'description' => 'Programming and development courses',
                'parent_id' => null,
                'image_path' => null,
                'meta_title' => null,
                'meta_description' => null,
                'is_active' => true,
                'sort_order' => 1,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'Design and creative courses',
                'parent_id' => null,
                'image_path' => null,
                'meta_title' => null,
                'meta_description' => null,
                'is_active' => true,
                'sort_order' => 2,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business and entrepreneurship courses',
                'parent_id' => null,
                'image_path' => null,
                'meta_title' => null,
                'meta_description' => null,
                'is_active' => true,
                'sort_order' => 3,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Marketing and sales courses',
                'parent_id' => null,
                'image_path' => null,
                'meta_title' => null,
                'meta_description' => null,
                'is_active' => true,
                'sort_order' => 4,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('course_categories');
    }
}
