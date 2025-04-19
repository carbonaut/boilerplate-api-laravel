<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PrivateController;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PrivateController::class, 'postPasswordChange')]
#[CoversMethod(UserService::class, 'changePassword')]
class PostPasswordChangeTest extends TestCase
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
    protected $path = '/auth/password/change';

    /**
     * The current password used for testing.
     *
     * @var string
     */
    protected string $currentPassword = 'Password1!';

    /**
     * The new password used for testing.
     *
     * @var string
     */
    protected string $newPassword = 'NewPassword1!';

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
        $response = $this->postJson($this->uri(), [
            'current_password' => $this->currentPassword,
            'new_password'     => $this->newPassword,
        ]);

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

        $response = $this->actingAs($user)->postJson($this->uri(), [
            'current_password' => $this->currentPassword,
            'new_password'     => $this->newPassword,
        ]);

        $response->assertForbidden();
    }

    /**
     * Asserts password change fails with wrong current password.
     *
     * @return void
     */
    public function testPasswordChangeFailsWithWrongCurrentPassword(): void
    {
        $user = User::factory()->verified()->create([
            'password' => Hash::make($this->currentPassword),
        ]);

        $response = $this->actingAs($user)->postJson($this->uri(), [
            'current_password' => 'wrong-password',
            'new_password'     => $this->newPassword,
        ]);

        $response
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The password is incorrect.',
                'message' => 'The password is incorrect.',
            ])
        ;
    }

    /**
     * Asserts password change fails with invalid new password.
     *
     * @return void
     */
    public function testPasswordChangeFailsWithInvalidNewPassword(): void
    {
        $user = User::factory()->verified()->create([
            'password' => Hash::make($this->currentPassword),
        ]);

        $response = $this->actingAs($user)->postJson($this->uri(), [
            'current_password' => $this->currentPassword,
            'new_password'     => '123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The new password field must be at least 8 characters. The new password field must contain at least one uppercase and one lowercase letter. The new password field must contain at least one letter. The new password field must contain at least one symbol.',
                'message' => 'The new password field must be at least 8 characters. (and 3 more errors)',
            ])
        ;
    }

    /**
     * Asserts verified user can change their password.
     *
     * @return void
     */
    public function testVerifiedUserCanChangePassword(): void
    {
        $user = User::factory()->verified()->create([
            'password' => Hash::make($this->currentPassword),
        ]);

        // Create multiple tokens
        $token1 = $user->createToken('api');
        $token2 = $user->createToken('api');

        $this->assertDatabaseCount('personal_access_tokens', 2);

        // Make request with token1
        $response = $this
            ->withToken($token1->plainTextToken)
            ->postJson($this->uri(), [
                'current_password' => $this->currentPassword,
                'new_password'     => $this->newPassword,
            ])
        ;

        $response->assertOk()->assertExactJson([]);

        // Check that the password was updated
        $user->refresh();
        $this->assertTrue(Hash::check($this->newPassword, $user->password));

        // Only the current token should remain
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => $token1->accessToken->name,
            'token'        => $token1->accessToken->token,
        ]);
    }
}
