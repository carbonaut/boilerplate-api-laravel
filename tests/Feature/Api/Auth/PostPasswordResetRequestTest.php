<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PublicController;
use App\Models\User;
use App\Notifications\User\PasswordReset;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PublicController::class, 'postPasswordResetRequest')]
#[CoversMethod(UserService::class, 'requestPasswordResetToken')]
class PostPasswordResetRequestTest extends TestCase
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
    protected $path = '/auth/password/reset/request';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /**
     * Asserts password reset request succeeds for existing user.
     *
     * @return void
     */
    public function testPasswordResetRequestSucceedsForExistingUser(): void
    {
        $user = User::factory()->verified()->create([
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseEmpty('password_reset_tokens');

        $this
            ->postJson($this->uri(), [
                'email' => $user->email,
            ])
            ->assertOk()
            ->assertExactJson([])
        ;

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(User::first(), PasswordReset::class);
    }

    /**
     * Asserts password reset request succeeds for unknown email.
     *
     * @return void
     */
    public function testPasswordResetRequestSucceedsForUnknownEmail(): void
    {
        $this->assertDatabaseEmpty('password_reset_tokens');

        $this
            ->postJson($this->uri(), [
                'email' => 'nonexistent@example.com',
            ])
            ->assertOk()
            ->assertExactJson([])
        ;

        $this->assertDatabaseEmpty('password_reset_tokens');

        Notification::assertNothingSent();
    }

    /**
     * Asserts password reset request validation errors.
     *
     * @return void
     */
    public function testPasswordResetRequestValidationErrors(): void
    {
        $this
            ->postJson($this->uri(), [])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The email field is required.',
                'message' => 'The email field is required.',
            ])
        ;
    }

    /**
     * Asserts password reset request with invalid email format fails.
     *
     * @return void
     */
    public function testPasswordResetRequestWithInvalidEmailFormatFails(): void
    {
        $this
            ->postJson($this->uri(), [
                'email' => 'invalid-email',
            ])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The email field must be a valid email address.',
                'message' => 'The email field must be a valid email address.',
            ])
        ;
    }

    /**
     * Asserts password reset request replaces existing token.
     *
     * @return void
     */
    public function testPasswordResetRequestReplacesExistingToken(): void
    {
        $this->assertDatabaseEmpty('password_reset_tokens');

        $user = User::factory()->verified()->create([
            'email' => 'test@example.com',
        ]);

        // Create an initial token
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => 'old-token',
            'created_at' => now()->subHour(),
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);

        $this
            ->postJson($this->uri(), [
                'email' => $user->email,
            ])
            ->assertOk()
            ->assertExactJson([])
        ;

        $this->assertDatabaseCount('password_reset_tokens', 1);

        // Get the latest token
        $latestToken = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first()
        ;

        $this->assertNotNull($latestToken);
        assert(property_exists($latestToken, 'token'));
        $this->assertNotEquals('old-token', $latestToken->token);
    }
}
