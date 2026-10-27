<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscussionTables extends Migration
{
    public function up()
    {
        Schema::create('course_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamps();
        });

        Schema::create('question_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_replies');
        Schema::dropIfExists('course_questions');
    }
}
