<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UserControllerTest extends TestCase
{
    // Comment to avoid running the migrations for each run on the testing database
    use RefreshDatabase;

    protected $old_password = 'MyOldPassword1!';
    protected $new_password = 'MyNewPassword43!!!';

    /**
     * Test if the user can change the password.
     *
     * @test
     */
    public function userCanChangePasswordWithValidRequest()
    {
        $user = User::factory()->create([
            'password' => Hash::make($this->old_password),
        ]);

        $response = $this->actingAs($user, 'api')->post(
            '/user/password/change',
            [
                'old_password'              => $this->old_password,
                'new_password'              => $this->new_password,
                'new_password_confirmation' => $this->new_password,
            ],
            ['Accept' => 'application/json']
        );

        $user->refresh();

        $response->assertStatus(200);
        $this->assertPassword($this->new_password, $user->password);
    }

    /**
     * Test if user cannot change password with an invalid request.
     *
     * @test
     */
    public function userCannotChangePasswordWithInvalidRequest()
    {
        $user = User::factory()->create([
            'password' => Hash::make($this->old_password),
        ]);

        $this->actingAs($user, 'api');

        // Wrong old password
        $response = $this->post(
            '/user/password/change',
            [
                'old_password'              => 'NotMyPassword123!',
                'new_password'              => $this->new_password,
                'new_password_confirmation' => $this->new_password,
            ],
            ['Accept' => 'application/json']
        );
        $user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $user->password);

        // Missing new password confirmation
        $response = $this->post(
            '/user/password/change',
            [
                'old_password' => $this->old_password,
                'new_password' => $this->new_password,
            ],
            ['Accept' => 'application/json']
        );
        $user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $user->password);

        // Missing old password
        $response = $this->post(
            '/user/password/change',
            [
                'new_password' => $this->new_password,
                'new_password' => $this->new_password,
            ],
            ['Accept' => 'application/json']
        );
        $user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $user->password);

        // Different new passwords
        $response = $this->post(
            '/user/password/change',
            [
                'old_password' => $this->old_password,
                'new_password' => $this->new_password,
                'new_password' => 'MyNewDifferentPassword123!',
            ],
            ['Accept' => 'application/json']
        );
        $user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $user->password);
    }

    private function assertPassword(string $password, string $hash)
    {
        $this->assertTrue(Hash::check($password, $hash));
    }
}
