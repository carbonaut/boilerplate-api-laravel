<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PublicController;
use App\Http\Resources\Models\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PublicController::class, 'postLogin')]
#[CoversMethod(UserResource::class, 'toArray')]
class PostLoginTest extends TestCase
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
    protected $path = '/auth/login';

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
     * Asserts login with valid credentials returns user data and token.
     *
     * @return void
     */
    public function testLoginWithValidCredentialsReturnsToken(): void
    {
        $user = User::factory()->verified()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this
            ->postJson($this->uri(), [
                'email'    => $user->email,
                'password' => 'password123',
            ])
            ->assertOk()
            ->assertExactJsonStructure([
                'token',
                'expires_at',
            ])
        ;

        // Sanity test to assert the token is accepted by API
        Auth::forgetUser();
        $this->assertIsString($response['token']);
        $this->withToken($response['token'])
            ->getJson($this->uri(path: '/auth/user'))
            ->assertOk()
        ;
    }

    /**
     * Asserts login with invalid credentials returns error.
     *
     * @return void
     */
    public function testLoginWithInvalidCredentialsReturnsError(): void
    {
        User::factory()->verified()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this
            ->postJson($this->uri(), [
                'email'    => 'test@example.com',
                'password' => 'wrong_password',
            ])
            ->assertUnauthorized()
            ->assertExactJson([
                'error'   => 'Invalid credentials.',
                'message' => 'api.ERROR.AUTH.INVALID_CREDENTIALS',
            ])
        ;
    }

    /**
     * Asserts login with unverified email returns no error.
     *
     * @return void
     */
    public function testLoginWithUnverifiedEmailReturnsNoError(): void
    {
        $user = User::factory()->unverified()->create([
            'email'    => 'unverified@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this
            ->postJson($this->uri(), [
                'email'    => $user->email,
                'password' => 'password123',
            ])
            ->assertOk()
            ->assertExactJsonStructure([
                'token',
                'expires_at',
            ])
        ;
    }

    /**
     * Asserts login validation errors.
     *
     * @return void
     */
    public function testLoginValidationErrors(): void
    {
        $this
            ->postJson($this->uri(), [])
            ->assertUnprocessable()
            ->assertExactJson([
                'error'   => 'The email field is required. The password field is required.',
                'message' => 'The email field is required. (and 1 more error)',
            ])
        ;
    }
}
