<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PrivateController;
use App\Http\Resources\Models\UserResource;
use App\Models\User;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PrivateController::class, 'getUser')]
#[CoversMethod(UserResource::class, 'toArray')]
class GetUserTest extends TestCase
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
        $response = $this->getJson($this->uri());
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

        $response = $this->actingAs($user)->getJson($this->uri());
        $response->assertForbidden();
    }

    /**
     * Asserts verified user can get their data.
     *
     * @return void
     */
    public function testVerifiedUserCanGetTheirData(): void
    {
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)->getJson($this->uri());

        $response->assertOk()
            ->assertExactJson([
                'user_id'        => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'language'       => $user->language,
                'email_verified' => $user->email_verified,
            ])
        ;
    }
}
