<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qb_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('qb_courses')->nullOnDelete();
            $table->foreignId('topic_tag_id')->nullable()->constrained('question_tags')->nullOnDelete();
            $table->string('topic_name')->nullable();
            $table->integer('status')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qb_topics');
    }
};
