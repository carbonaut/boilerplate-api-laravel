<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\ApiController;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api')]
#[CoversMethod(ApiController::class, 'getApi')]
class GetApiTest extends TestCase
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
    protected $path = '/';

    /**
     * Test that the API UI route returns a view.
     *
     * @return void
     */
    public function testGetApiReturnsView(): void
    {
        // Assert that the response is successful and the correct view is returned
        $this
            ->get($this->uri())
            ->assertOk()
            ->assertViewIs('api.swagger.ui')
        ;
    }

    /**
     * Test that the API UI route is blocked when the block-in-production
     * middleware is active.
     *
     * @return void
     */
    public function testGetApiIsBlockedWhenMiddlewareActive(): void
    {
        // Mock the environment to be production
        $this->app->detectEnvironment(function () {
            return 'production';
        });

        // Assert that the response is forbidden
        $this
            ->get($this->uri())
            ->assertServerError()
        ;

        // Return environment to testing
        $this->app->detectEnvironment(function () {
            return 'testing';
        });
    }
}
