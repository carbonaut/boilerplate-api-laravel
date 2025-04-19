<?php

namespace Tests\Feature\Api\Auth;

use App\Enums\Language;
use App\Http\Controllers\Api\Auth\PublicController;
use App\Mail\User\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PublicController::class, 'postRegister')]
class PostRegisterTest extends TestCase
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
    protected $path = '/auth/register';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * Asserts a user can register with valid data.
     *
     * @return void
     */
    public function testUserCanRegisterWithValidData(): void
    {
        $userData = [
            'name'     => 'Test User',
            'email'    => 'newuser@example.com',
            'language' => Language::English->value,
            'password' => 'Password1!',
        ];

        $this->assertDatabaseEmpty('users');

        $this->postJson($this->uri(), $userData)
            ->assertCreated()
            ->assertExactJson([
                'user_id'        => User::first()?->id,
                'name'           => $userData['name'],
                'email'          => $userData['email'],
                'language'       => $userData['language'],
                'email_verified' => false,
            ])
        ;

        $this->assertDatabaseHas('users', [
            'name'              => $userData['name'],
            'email'             => $userData['email'],
            'email_verified_at' => null,
        ]);

        Mail::assertNothingSent();
        Mail::assertQueued(EmailVerification::class, $userData['email']);
    }

    /**
     * Asserts registration fails with existing email.
     *
     * @return void
     */
    public function testRegistrationFailsWithExistingEmail(): void
    {
        $this->assertDatabaseCount('users', 0);
        User::factory()->create(['email' => 'existing@example.com']);
        $this->assertDatabaseCount('users', 1);

        $userData = [
            'name'     => 'Another User',
            'email'    => 'existing@example.com',
            'language' => Language::English->value,
            'password' => 'Password1!',
        ];

        $this->postJson($this->uri(), $userData)
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The email has already been taken.',
                'message' => 'The email has already been taken.',
            ])
        ;

        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Asserts registration validation errors.
     *
     * @return void
     */
    public function testRegistrationValidationErrors(): void
    {
        $this->postJson($this->uri())
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The name field is required. The email field is required. The password field is required. The language field is required.',
                'message' => 'The name field is required. (and 3 more errors)',
            ])
        ;
    }
}
