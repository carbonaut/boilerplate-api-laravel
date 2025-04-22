<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PrivateController;
use App\Models\User;
use App\Notifications\User\EmailVerification;
use App\Services\UserService;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PrivateController::class, 'postEmailVerification')]
#[CoversMethod(UserService::class, 'verifyEmail')]
class PostEmailVerificationTest extends TestCase
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
    protected $path = '/auth/email/verification';

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
     * Asserts validation errors when code is not provided.
     *
     * @return void
     */
    public function testValidationErrors(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->postJson($this->uri())
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The email verification code field is required.',
                'message' => 'The email verification code field is required.',
            ])
        ;
    }

    /**
     * Asserts a verified user cannot verify email again.
     *
     * @return void
     */
    public function testVerifiedUserCannotVerifyEmailAgain(): void
    {
        $user = User::factory()->verified()->create();

        $this->actingAs($user)
            ->postJson($this->uri(), [
                'email_verification_code' => 123456,
            ])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'Email already verified.',
                'message' => 'api.ERROR.EMAIL.ALREADY_VERIFIED',
            ])
        ;

        Notification::assertNothingSent();
    }

    /**
     * Asserts an unverified user gets an error with expired code and a new code is sent.
     *
     * @return void
     */
    public function testUnverifiedUserWithExpiredCode(): void
    {
        $user = User::factory()->unverified()->create([
            'email_verification_code_expires_at' => now()->subHour(),
        ]);

        $this->actingAs($user)
            ->postJson($this->uri(), [
                'email_verification_code' => $user->email_verification_code,
            ])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'Verification code expired. A new code was sent.',
                'message' => 'api.ERROR.EMAIL.VERIFICATION_CODE_EXPIRED',
            ])
        ;

        // Verify that a new verification code was generated and email was sent
        Notification::assertSentTo(User::first(), EmailVerification::class);
    }

    /**
     * Asserts an unverified user gets an error with invalid code.
     *
     * @return void
     */
    public function testUnverifiedUserWithInvalidCode(): void
    {
        $user = User::factory()->unverified()->create([
            'email_verification_code'            => 123456,
            'email_verification_code_expires_at' => now()->addHour(),
        ]);

        $this->actingAs($user)
            ->postJson($this->uri(), [
                'email_verification_code' => 654321,
            ])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'Invalid verification code.',
                'message' => 'api.ERROR.EMAIL.VERIFICATION_CODE_MISMATCH',
            ])
        ;

        Notification::assertNothingSent();
    }

    /**
     * Asserts an unverified user can verify email with valid code.
     *
     * @return void
     */
    public function testUnverifiedUserCanVerifyEmail(): void
    {
        $verificationCode = 123456;

        $user = User::factory()->unverified()->create([
            'email_verification_code'            => $verificationCode,
            'email_verification_code_expires_at' => now()->addHour(),
        ]);

        $this->assertNull($user->email_verified_at);

        $this->actingAs($user)
            ->postJson($this->uri(), [
                'email_verification_code' => $verificationCode,
            ])
            ->assertOk()
            ->assertExactJson([])
        ;

        // Refresh user from database
        $user->refresh();

        // Verify that email was marked as verified
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_code);
        $this->assertNull($user->email_verification_code_expires_at);

        Notification::assertNothingSent();
    }
}
