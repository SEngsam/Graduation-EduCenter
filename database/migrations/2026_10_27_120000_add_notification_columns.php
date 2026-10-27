<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('email_settings')->nullable();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->timestamp('reminded_at')->nullable();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('reminded_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_settings');
        });
    }
};
