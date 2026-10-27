<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('registrations_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'course_id']);
        });

        $rows = DB::table('registrations')
            ->whereNull('deleted_at')
            ->whereIn('student_id', DB::table('students')->pluck('id'))
            ->whereIn('course_id', DB::table('courses')->pluck('id'))
            ->get();

        $seen = [];
        foreach ($rows as $row) {
            $key = $row->student_id . '-' . $row->course_id;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            DB::table('registrations_new')->insert([
                'student_id' => $row->student_id,
                'course_id' => $row->course_id,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        Schema::drop('registrations');
        Schema::rename('registrations_new', 'registrations');

        Schema::dropIfExists('course_students');

        foreach (['registration_id', 'course_id', 'teacher_id', 'major_id'] as $column) {
            if (Schema::hasColumn('students', $column)) {
                DB::statement('ALTER TABLE students DROP COLUMN ' . $column);
            }
        }
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable();
            $table->softDeletes();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('registration_id')->nullable();
            $table->foreignId('course_id')->nullable();
            $table->foreignId('teacher_id')->nullable();
            $table->foreignId('major_id')->nullable();
        });
    }
};
