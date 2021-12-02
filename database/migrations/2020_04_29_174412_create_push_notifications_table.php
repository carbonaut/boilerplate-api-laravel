<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('push_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('device_id')->index();
            $table->string('type');
            $table->string('status');
            $table->string('title');
            $table->string('body');
            $table->boolean('coldstart')->nullable();
            $table->boolean('foreground')->nullable();
            $table->datetime('scheduled_for');
            $table->datetime('failed_at')->nullable();
            $table->datetime('opened_at')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('push_notifications');
    }
}
