<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PublicController;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PublicController::class, 'postPasswordResetSubmit')]
#[CoversMethod(UserService::class, 'resetPassword')]
class PostPasswordResetSubmitTest extends TestCase
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
    protected $path = '/auth/password/reset/submit';

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
     * Asserts password reset submit works with valid token.
     *
     * @return void
     */
    public function testPasswordResetSubmitWorksWithValidToken(): void
    {
        $user = User::factory()->verified()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('old_password'),
        ]);

        $token = 'valid_token_' . uniqid();

        // Create password reset token
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $this
            ->postJson($this->uri(), [
                'email'        => $user->email,
                'new_password' => 'Password1!',
                'token'        => $token,
            ])
            ->assertOk()
            ->assertExactJson([])
        ;

        // Reload user from database
        $user->refresh();

        // Verify password was changed
        $this->assertTrue(Hash::check('Password1!', $user->password));

        // Verify token was deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    /**
     * Asserts password reset submit fails with invalid token.
     *
     * @return void
     */
    public function testPasswordResetSubmitFailsWithInvalidToken(): void
    {
        $user = User::factory()->verified()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('old_password'),
        ]);

        // Create password reset token
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make('valid_token'),
            'created_at' => now(),
        ]);

        $this
            ->postJson($this->uri(), [
                'email'        => $user->email,
                'new_password' => 'Password1!',
                'token'        => 'invalid_token',
            ])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'Invalid input for resetting the password.',
                'message' => 'api.ERROR.PASSWORD-RESET.INVALID-INPUT',
            ])
        ;

        // Reload user from database
        $user->refresh();

        // Verify password was not changed
        $this->assertTrue(Hash::check('old_password', $user->password));
        $this->assertFalse(Hash::check('Password1!', $user->password));

        // Verify token still exists
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Asserts password reset submit fails with expired token.
     *
     * @return void
     */
    public function testPasswordResetSubmitFailsWithExpiredToken(): void
    {
        $user = User::factory()->verified()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('old_password'),
        ]);

        $token = 'valid_token_' . uniqid();

        // Create expired password reset token
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make($token),
            'created_at' => now()->subMinutes(60), // Token expires in 1 hour (config/auth.php)
        ]);

        $this
            ->postJson($this->uri(), [
                'email'        => $user->email,
                'new_password' => 'Password1!',
                'token'        => $token,
            ])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'Invalid input for resetting the password.',
                'message' => 'api.ERROR.PASSWORD-RESET.INVALID-INPUT',
            ])
        ;

        // Reload user from database
        $user->refresh();

        // Verify password was not changed
        $this->assertTrue(Hash::check('old_password', $user->password));
        $this->assertFalse(Hash::check('Password1!', $user->password));

        // Verify token still exists
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Asserts password reset submit validation errors.
     *
     * @return void
     */
    public function testPasswordResetSubmitValidationErrors(): void
    {
        $this
            ->postJson($this->uri(), [])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The token field is required. The email field is required. The new password field is required.',
                'message' => 'The token field is required. (and 2 more errors)',
            ])
        ;
    }
}
