<?php

namespace Tests\Feature\Api\Auth;

use App\Enums\Language;
use App\Http\Controllers\Api\Auth\PrivateController;
use App\Http\Resources\Models\UserResource;
use App\Models\User;
use App\Services\UserService;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PrivateController::class, 'patchUser')]
#[CoversMethod(UserService::class, 'patchUser')]
#[CoversMethod(UserResource::class, 'toArray')]
class PatchUserTest extends TestCase
{
    /**
     * The route subdomain.
     *
     * @var null|string
     */
    protected $subdomain = 'api';

    /**
     * The route path.
     *
     * @var string
     */
    protected $path = '/auth/user';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Asserts unauthenticated access is denied.
     *
     * @return void
     */
    public function testUnauthenticatedAccessDenied(): void
    {
        $response = $this->patchJson($this->uri(), []);

        $response->assertUnauthorized();
    }

    /**
     * Asserts unverified user access is denied.
     *
     * @return void
     */
    public function testUnverifiedUserAccessDenied(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->patchJson($this->uri(), []);

        $response->assertForbidden();
    }

    /**
     * Asserts verified user can update their name.
     *
     * @return void
     */
    public function testVerifiedUserCanUpdateTheirName(): void
    {
        $user = User::factory()->verified()->create([
            'name' => 'Original Name',
        ]);

        $newName = 'New Name';

        $this->actingAs($user)
            ->patchJson($this->uri(), [
                'name' => $newName,
            ])
            ->assertOk()
            ->assertExactJson([
                'user_id'        => $user->id,
                'name'           => $newName,
                'email'          => $user->email,
                'language'       => $user->language,
                'email_verified' => true,
            ])
        ;

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => $newName,
        ]);
    }

    /**
     * Asserts verified user can update their language.
     *
     * @return void
     */
    public function testVerifiedUserCanUpdateTheirLanguage(): void
    {
        $user = User::factory()->verified()->create([
            'language' => Language::English->value,
        ]);

        $newLanguage = Language::BrazilianPortuguese->value;

        $this->actingAs($user)
            ->patchJson($this->uri(), [
                'language' => $newLanguage,
            ])
            ->assertOk()
            ->assertExactJson([
                'user_id'        => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'language'       => $newLanguage,
                'email_verified' => true,
            ])
        ;

        $this->assertDatabaseHas('users', [
            'id'       => $user->id,
            'language' => $newLanguage,
        ]);
    }

    /**
     * Asserts verified user can update multiple fields.
     *
     * @return void
     */
    public function testVerifiedUserCanUpdateMultipleFields(): void
    {
        $user = User::factory()->verified()->create([
            'name'     => 'Original Name',
            'language' => Language::English->value,
        ]);

        $newName = 'New Name';
        $newLanguage = Language::BrazilianPortuguese->value;

        $this->actingAs($user)
            ->patchJson($this->uri(), [
                'name'     => $newName,
                'language' => $newLanguage,
            ])
            ->assertOk()
            ->assertExactJson([
                'user_id'        => $user->id,
                'name'           => $newName,
                'email'          => $user->email,
                'language'       => $newLanguage,
                'email_verified' => true,
            ])
        ;

        $this->assertDatabaseHas('users', [
            'id'       => $user->id,
            'name'     => $newName,
            'language' => $newLanguage,
        ]);
    }

    /**
     * Asserts verified user cannot update their email.
     *
     * @return void
     */
    public function testVerifiedUserCannotUpdateTheirEmail(): void
    {
        $originalEmail = 'original@example.com';
        $newEmail = 'new@example.com';

        $user = User::factory()->verified()->create([
            'email' => $originalEmail,
        ]);

        $this->actingAs($user)
            ->patchJson($this->uri(), [
                'email' => $newEmail,
            ])
            ->assertOk()
            ->assertExactJson([
                'user_id'        => $user->id,
                'name'           => $user->name,
                'email'          => $originalEmail,
                'language'       => $user->language,
                'email_verified' => true,
            ])
        ;

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'email' => $originalEmail,
        ]);
    }
}
