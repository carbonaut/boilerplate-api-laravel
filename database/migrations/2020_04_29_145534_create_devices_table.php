<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('user_id')->index();
            $table->string('uuid');
            $table->string('name')->nullable();
            $table->string('platform');
            $table->string('operating_system');
            $table->string('os_version');
            $table->string('manufacturer');
            $table->string('model');
            $table->string('web_view_version')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('is_virtual');
            $table->string('push_token')->nullable();
            $table->boolean('is_active');
            $table->timestamps();

            $table->unique(['user_id', 'uuid']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
