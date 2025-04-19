<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PrivateController;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PrivateController::class, 'postLogout')]
#[CoversMethod(UserService::class, 'revokeCurrentAccessToken')]
class PostLogoutTest extends TestCase
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
    protected $path = '/auth/logout';

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
        $response = $this->postJson($this->uri());
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

        $response = $this->actingAs($user)->postJson($this->uri());
        $response->assertForbidden();
    }

    /**
     * Asserts verified user can logout.
     *
     * @return void
     */
    public function testVerifiedUserCanLogout(): void
    {
        $user = User::factory()->verified()->create();

        // Create a token for our user
        $token = $user->createToken('api');

        // Check that token exists in the database
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token->accessToken->name,
            'token'        => $token->accessToken->token,
        ]);

        // Make request with token
        $response = $this->withToken($token->plainTextToken)
            ->postJson($this->uri())
        ;

        $response->assertOk()->assertExactJson([]);

        // Token should be removed from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token->accessToken->name,
            'token'        => $token->accessToken->token,
        ]);

        // Making another request with the revoked token should fail
        Auth::forgetUser();
        $this->withToken($token->plainTextToken)
            ->postJson($this->uri())
            ->assertUnauthorized()
        ;
    }
}
