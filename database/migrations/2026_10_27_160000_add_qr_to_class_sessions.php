<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrToClassSessions extends Migration
{
    public function up()
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('qr_secret')->nullable();
            $table->boolean('qr_open')->default(false);
        });
    }

    public function down()
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn(['qr_secret', 'qr_open']);
        });
    }
}
