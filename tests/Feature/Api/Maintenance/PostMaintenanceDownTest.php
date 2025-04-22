<?php

namespace Tests\Feature\Api\Maintenance;

use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Requests\Api\Maintenance\MaintenanceRequest;
use App\Models\User;
use App\Policies\ApplicationPolicy;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Maintenance')]
#[CoversMethod(MaintenanceController::class, 'postDown')]
#[CoversMethod(MaintenanceRequest::class, 'authorize')]
#[CoversMethod(ApplicationPolicy::class, 'toggleMaintenance')]
class PostMaintenanceDownTest extends TestCase
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
    protected $path = '/maintenance/down';

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
        $response = $this->postJson($this->uri());
        $response->assertUnauthorized();
    }

    /**
     * Asserts unauthorized user access is forbidden.
     *
     * @return void
     */
    public function testUnauthorizedUserAccessForbidden(): void
    {
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)->postJson($this->uri());
        $response->assertForbidden();
    }

    /**
     * Asserts authorized user can toggle maintenance mode down.
     *
     * @return void
     */
    public function testAuthorizedUserCanToggleMaintenanceDown(): void
    {
        // Create user with authorized email
        $user = User::factory()->verified()->create([
            'email' => 'hello@carbonaut.io',
        ]);

        // Make sure the application is up
        Artisan::call('up');
        $this->assertFalse(App::isDownForMaintenance());

        // Toggle maintenance mode on
        $response = $this->actingAs($user)->postJson($this->uri());
        $response->assertOk()->assertExactJson([]);
        $this->assertTrue(App::isDownForMaintenance());

        // Toggle maintenance mode off
        Artisan::call('up');
    }
}
