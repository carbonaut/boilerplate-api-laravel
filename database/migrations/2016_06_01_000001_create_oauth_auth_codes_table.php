<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthAuthCodesTable extends Migration
{
    /**
     * The database schema.
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create('oauth_auth_codes', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->uuid('user_id')->index();
            $table->uuid('client_id');
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema->dropIfExists('oauth_auth_codes');
    }

    /**
     * Get the migration connection name.
     *
     * @return null|string
     */
    public function getConnection()
    {
        return config('passport.storage.database.connection');
    }
}
