<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\StatusController;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api')]
#[CoversMethod(StatusController::class, 'getStatus')]
class GetStatusTest extends TestCase
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
    protected $path = '/status';

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
     * Test that the status route returns a successful empty array.
     *
     * @return void
     */
    public function testGetStatusReturnsEmptyArray(): void
    {
        // Assert that the response is successful and returns an empty array
        $this
            ->get($this->uri())
            ->assertOk()
            ->assertJson([])
        ;
    }

    /**
     * Test that the status route returns 503 when application is in maintenance mode.
     *
     * @return void
     */
    public function testGetStatusReturnsServiceUnavailableInMaintenanceMode(): void
    {
        try {
            // Put application in maintenance mode
            Artisan::call('down');

            // Assert that the response is 503 Service Unavailable
            $this
                ->get($this->uri())
                ->assertStatus(503)
            ;
        } finally {
            // Ensure application is brought back up even if test fails
            Artisan::call('up');
        }
    }
}
