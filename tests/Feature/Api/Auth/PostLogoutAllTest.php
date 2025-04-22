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
#[CoversMethod(PrivateController::class, 'postLogoutAll')]
#[CoversMethod(UserService::class, 'revokeAllAccessTokens')]
class PostLogoutAllTest extends TestCase
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
    protected $path = '/auth/logout/all';

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
     * Asserts verified user can logout from all devices.
     *
     * @return void
     */
    public function testVerifiedUserCanLogoutFromAllDevices(): void
    {
        $user = User::factory()->verified()->create();

        // Create multiple tokens for our user
        $token1 = $user->createToken('token-1');
        $token2 = $user->createToken('token-2');
        $token3 = $user->createToken('token-3');

        // Check that tokens exist in the database
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token1->accessToken->name,
            'token'        => $token1->accessToken->token,
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token2->accessToken->name,
            'token'        => $token2->accessToken->token,
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token3->accessToken->name,
            'token'        => $token3->accessToken->token,
        ]);

        // Make request with one of the tokens
        $response = $this->withToken($token1->plainTextToken)
            ->postJson($this->uri())
        ;

        $response->assertOk()->assertExactJson([]);

        // All tokens should be removed from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token1->accessToken->name,
            'token'        => $token1->accessToken->token,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token2->accessToken->name,
            'token'        => $token2->accessToken->token,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token3->accessToken->name,
            'token'        => $token3->accessToken->token,
        ]);

        // Making another request with any of the tokens should fail
        Auth::forgetUser();

        $this->withToken($token1->plainTextToken)
            ->postJson($this->uri())
        ;

        $this->withToken($token2->plainTextToken)
            ->postJson($this->uri())
        ;

        $this->withToken($token3->plainTextToken)
            ->postJson($this->uri())
        ;
    }

    /**
     * Asserts that logging out from all devices doesn't affect other users' tokens.
     *
     * @return void
     */
    public function testLogoutAllDoesntAffectOtherUsers(): void
    {
        $user1 = User::factory()->verified()->create();
        $user2 = User::factory()->verified()->create();

        // Create tokens for both users
        $token1 = $user1->createToken('token-1');
        $token2 = $user2->createToken('token-2');

        // Check that tokens exist in the database
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user1->id,
            'name'         => $token1->accessToken->name,
            'token'        => $token1->accessToken->token,
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user2->id,
            'name'         => $token2->accessToken->name,
            'token'        => $token2->accessToken->token,
        ]);

        // User 1 logs out from all devices
        $this->withToken($token1->plainTextToken)
            ->postJson($this->uri())
            ->assertOk()
        ;

        // User 1's token should be removed
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user1->id,
            'name'         => $token1->accessToken->name,
            'token'        => $token1->accessToken->token,
        ]);

        // User 2's token should still exist
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user2->id,
            'name'         => $token2->accessToken->name,
            'token'        => $token2->accessToken->token,
        ]);

        // User 2 should still be able to use their token
        Auth::forgetUser();
        $this->withToken($token2->plainTextToken)
            ->postJson($this->uri())
            ->assertOk()
        ;
    }
}
