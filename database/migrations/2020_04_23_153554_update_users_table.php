<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('profile_id')->nullable()->index();
            $table->uuid('language_id')->nullable()->index();

            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('set null');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_id');
            $table->dropColumn('language_id');
        });
    }
}
