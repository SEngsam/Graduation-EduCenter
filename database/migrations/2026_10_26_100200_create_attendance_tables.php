<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->string('room')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'starts_at']);
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('status', 10);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unique(['class_session_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('class_sessions');
    }
};
