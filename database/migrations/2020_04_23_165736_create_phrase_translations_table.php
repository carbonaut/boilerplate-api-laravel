<?php

use Database\Seeders\PhraseApiSeeder;
use Database\Seeders\PhraseEmailSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePhraseTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        Schema::create('phrase_translations', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('phrase_id')->index();
            $table->string('locale')->index();
            $table->text('value');
            $table->timestamps();

            $table->unique(['phrase_id', 'locale']);
            $table->foreign('phrase_id')->references('id')->on('phrases')->onDelete('cascade');
            $table->foreign('locale')->references('locale')->on('languages')->onDelete('cascade');
        });

        (new PhraseApiSeeder())->run();
        (new PhraseEmailSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('phrase_translations');
    }
}
