<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhrasesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        \DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('phrases', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('key');
            $table->string('type');
            $table->timestamps();

            $table->unique(['key', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('phrases');
    }
}
