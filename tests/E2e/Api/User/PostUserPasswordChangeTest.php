<?php

namespace Tests\E2e\Api\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PostUserPasswordChangeTest extends TestCase
{
    // Comment to avoid running the migrations for each run on the testing database
    use RefreshDatabase;

    private $method = 'POST';
    private $endpoint = '/user/password/change';
    private $user;

    protected $old_password = 'MyOldPassword1!';
    protected $new_password = 'MyNewPassword43!!!';

    public function setUp(): void
    {
        parent::setUp();

        // $this->endpoint = $this->baseUrlApi . $this->endpoint;
        $this->user = User::factory()->create([
            'password' => Hash::make($this->old_password),
        ]);
    }

    /**
     * Test if the user can change the password.
     *
     * @test
     * @group User
     * @covers \App\Http\Controllers\Api\UserController::postPasswordChange
     */
    public function userCanChangePasswordWithValidRequestTest()
    {
        $response = $this->actingAs($this->user, 'api')->json($this->method, $this->endpoint, [
            'old_password'              => $this->old_password,
            'new_password'              => $this->new_password,
            'new_password_confirmation' => $this->new_password,
        ]);

        $response->assertStatus(200);
        $this->assertPassword($this->new_password, $this->user->password);
    }

    /**
     * Test if user cannot change password with an invalid request.
     *
     * @test
     * @group User
     * @covers \App\Http\Controllers\Api\UserController::postPasswordChange
     */
    public function userCannotChangePasswordWithInvalidRequest()
    {
        // Wrong old password
        $response = $this->actingAs($this->user, 'api')->json($this->method, $this->endpoint, [
            'old_password'              => 'NotMyPassword123!',
            'new_password'              => $this->new_password,
            'new_password_confirmation' => $this->new_password,
        ]);
        $this->user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $this->user->password);

        // Missing new password confirmation
        $response = $this->actingAs($this->user, 'api')->json($this->method, $this->endpoint, [
            'old_password' => $this->old_password,
            'new_password' => $this->new_password,
        ]);
        $this->user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $this->user->password);

        // Missing old password
        $response = $this->actingAs($this->user, 'api')->json($this->method, $this->endpoint, [
            'new_password' => $this->new_password,
            'new_password' => $this->new_password,
        ]);
        $this->user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $this->user->password);

        // Different new passwords
        $response = $this->actingAs($this->user, 'api')->json($this->method, $this->endpoint, [
            'old_password' => $this->old_password,
            'new_password' => $this->new_password,
            'new_password' => 'MyNewDifferentPassword123!',
        ]);
        $this->user->refresh();

        $response->assertStatus(422);
        $this->assertPassword($this->old_password, $this->user->password);
    }

    private function assertPassword(string $password, string $hash)
    {
        $this->assertTrue(Hash::check($password, $hash));
    }
}
