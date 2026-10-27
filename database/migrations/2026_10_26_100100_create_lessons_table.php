<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('type', 10)->default('text');
            $table->string('url')->nullable();
            $table->string('file')->nullable();
            $table->text('body')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('lesson_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['lesson_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_student');
        Schema::dropIfExists('lessons');
    }
};
