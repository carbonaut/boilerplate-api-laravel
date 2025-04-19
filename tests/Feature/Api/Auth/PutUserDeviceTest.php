<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\Auth\PrivateController;
use App\Http\Resources\Models\DeviceResource;
use App\Models\Device;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Api\Auth')]
#[CoversMethod(PrivateController::class, 'putUserDevice')]
#[CoversMethod(UserService::class, 'upsertUserDevice')]
#[CoversMethod(DeviceResource::class, 'toArray')]
class PutUserDeviceTest extends TestCase
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
    protected $path = '/auth/user/devices/{uuid}';

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
        $uuid = Str::uuid()->toString();

        $response = $this->putJson($this->uri(['{uuid}' => $uuid]));

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
        $uuid = Str::uuid()->toString();

        $response = $this->actingAs($user)->putJson($this->uri(['{uuid}' => $uuid]));

        $response->assertForbidden();
    }

    /**
     * Asserts verified user can create a new device.
     *
     * @return void
     */
    public function testVerifiedUserCanCreateNewDevice(): void
    {
        $user = User::factory()->verified()->create();
        $uuid = Str::uuid()->toString();

        $deviceData = [
            'platform'         => 'ios',
            'operating_system' => 'ios',
            'os_version'       => '16.0.0',
            'manufacturer'     => 'Apple',
            'model'            => 'iPhone',
            'name'             => 'My iPhone',
            'web_view_version' => null,
            'app_version'      => '1.0.0',
            'is_virtual'       => false,
            'push_token'       => null,
        ];

        $this->assertDatabaseEmpty('devices');

        $this->actingAs($user)
            ->putJson($this->uri(['{uuid}' => $uuid]), $deviceData)
            ->assertCreated()
            ->assertExactJson([
                ...$deviceData,
                'device_id' => Device::first()?->id,
                'uuid'      => $uuid,
                'is_active' => true,
            ])
        ;

        $this->assertDatabaseHas('devices', [
            ...$deviceData,
            'id'        => Device::first()?->id,
            'user_id'   => $user->id,
            'uuid'      => $uuid,
            'is_active' => true,
        ]);
    }

    /**
     * Asserts verified user can update an existing device.
     *
     * @return void
     */
    public function testVerifiedUserCanUpdateExistingDevice(): void
    {
        $user = User::factory()->verified()->create();

        // Create initial device
        $device = Device::factory()->for($user)->create();

        // Update device data
        $updatedData = [
            'platform'         => 'ios',
            'operating_system' => 'ios',
            'os_version'       => '16.0.0',
            'manufacturer'     => 'Apple',
            'model'            => 'iPhone',
            'name'             => 'My New iPhone',
            'web_view_version' => null,
            'app_version'      => '1.0.0',
            'is_virtual'       => false,
            'push_token'       => 'new-token-123',
        ];

        $this->actingAs($user)
            ->putJson($this->uri(['{uuid}' => $device->uuid]), $updatedData)
            ->assertOk()
            ->assertExactJson([
                ...$updatedData,
                'device_id' => $device->id,
                'uuid'      => $device->uuid,
                'is_active' => true,
            ])
        ;

        $this->assertDatabaseHas('devices', [
            ...$updatedData,
            'id'        => $device->id,
            'user_id'   => $user->id,
            'uuid'      => $device->uuid,
            'is_active' => true,
        ]);
    }

    /**
     * Asserts when a device is registered by a new user, it becomes inactive for the previous user.
     *
     * @return void
     */
    public function testDeviceBecomesInactiveForPreviousUserWhenRegisteredByNewUser(): void
    {
        // Create two verified users
        $user1 = User::factory()->verified()->create();
        $user2 = User::factory()->verified()->create();

        // Create a device for the first user
        $device = Device::factory()->for($user1)->create([
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('devices', [
            'id'        => $device->id,
            'user_id'   => $user1->id,
            'uuid'      => $device->uuid,
            'is_active' => true,
        ]);

        // Device data for the second user
        $deviceData = [
            'platform'         => 'android',
            'operating_system' => 'android',
            'os_version'       => '12.0.0',
            'manufacturer'     => 'Samsung',
            'model'            => 'Galaxy S21',
            'name'             => 'My Galaxy',
            'web_view_version' => null,
            'app_version'      => '1.0.0',
            'is_virtual'       => false,
            'push_token'       => 'token-xyz',
        ];

        // Register the same device UUID with the second user
        $this->actingAs($user2)
            ->putJson($this->uri(['{uuid}' => $device->uuid]), $deviceData)
            ->assertCreated()
            ->assertExactJson([
                ...$deviceData,
                'device_id' => Device::where('user_id', $user2->id)->first()?->id,
                'uuid'      => $device->uuid,
                'is_active' => true,
            ])
        ;

        // Assert the device is now inactive for the first user
        $this->assertDatabaseHas('devices', [
            'id'        => $device->id,
            'user_id'   => $user1->id,
            'uuid'      => $device->uuid,
            'is_active' => false,
        ]);

        // Assert the device is active for the second user
        $this->assertDatabaseHas('devices', [
            'id'        => Device::where('user_id', $user2->id)->first()?->id,
            'user_id'   => $user2->id,
            'uuid'      => $device->uuid,
            'is_active' => true,
        ]);
    }
}
