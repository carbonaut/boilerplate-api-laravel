<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api')]
#[CoversMethod(ApiController::class, 'getApiDocumentation')]
class GetApiDocumentationTest extends TestCase
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
    protected $path = '/api/documentation';

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
     * Test that the API documentation route returns content.
     *
     * @return void
     */
    public function testGetApiDocumentationReturnsContent(): void
    {
        // Make a GET request to the route
        $response = $this
            ->get($this->uri())
            ->assertOk()
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
        ;

        // Assert that the response contains the expected content
        $this->assertEquals(
            $response->getContent(),
            File::get(resource_path('api/documentation.yaml'))
        );
    }

    /**
     * Test that the API documentation route is blocked when the
     * block-in-production middleware is active and the environment is production.
     *
     * @return void
     */
    public function testGetApiDocumentationIsBlockedWhenMiddlewareActive(): void
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
