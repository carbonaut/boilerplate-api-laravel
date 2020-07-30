<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        \DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('emails', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('user_id')->index()->nullable();
            $table->uuid('sent_by')->nullable()->index();
            $table->string('type');
            $table->string('status');
            $table->dateTime('scheduled_for');
            $table->dateTime('failed_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->text('mailable')->nullable();
            $table->uuid('emailable_id')->nullable();
            $table->string('emailable_type')->nullable();
            $table->string('to')->nullable()->index();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('sent_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::dropIfExists('emails');
    }
}
