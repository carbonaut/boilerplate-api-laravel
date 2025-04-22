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
#[CoversMethod(MaintenanceController::class, 'postUp')]
#[CoversMethod(MaintenanceRequest::class, 'authorize')]
#[CoversMethod(ApplicationPolicy::class, 'toggleMaintenance')]
class PostMaintenanceUpTest extends TestCase
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
    protected $path = '/maintenance/up';

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
     * Asserts authorized user can toggle maintenance mode up.
     *
     * @return void
     */
    public function testAuthorizedUserCanToggleMaintenanceUp(): void
    {
        // Create user with authorized email
        $user = User::factory()->verified()->create([
            'email' => 'hello@carbonaut.io',
        ]);

        // Make sure the application is down
        Artisan::call('down');
        $this->assertTrue(App::isDownForMaintenance());

        // Toggle maintenance mode off
        $response = $this->actingAs($user)->postJson($this->uri());
        $response->assertOk()->assertExactJson([]);
        $this->assertFalse(App::isDownForMaintenance());
    }
}
