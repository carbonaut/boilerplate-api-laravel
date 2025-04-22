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
#[CoversMethod(PrivateController::class, 'getEmailVerification')]
#[CoversMethod(UserService::class, 'requestEmailVerificationCode')]
class GetEmailVerificationTest extends TestCase
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
        $response = $this->getJson($this->uri());
        $response->assertUnauthorized();
    }

    /**
     * Asserts a verified user cannot request a verification code.
     *
     * @return void
     */
    public function testVerifiedUserCannotRequestVerificationCode(): void
    {
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)->getJson($this->uri());

        $response
            ->assertStatus(422)
            ->assertExactJson([
                'error'   => 'Email already verified.',
                'message' => 'api.ERROR.EMAIL.ALREADY_VERIFIED',
            ])
        ;

        Notification::assertNothingSent();
    }

    /**
     * Asserts an unverified user can request a verification code.
     *
     * @return void
     */
    public function testUnverifiedUserCanRequestVerificationCode(): void
    {
        $user = User::factory()->unverified()->create();

        $oldVerificationCode = $user->email_verification_code;

        $this->actingAs($user)
            ->getJson($this->uri())
            ->assertOk()
            ->assertExactJson([])
        ;

        // Refresh user from database
        $user->refresh();

        // Verify that a new verification code was generated
        $this->assertNotNull($user->email_verification_code);
        $this->assertNotEquals($oldVerificationCode, $user->email_verification_code);
        $this->assertNotNull($user->email_verification_code_expires_at);
        $this->assertTrue($user->email_verification_code_expires_at->isFuture());

        // Verify that an email was sent
        Notification::assertSentTo(User::first(), EmailVerification::class);
    }
}
