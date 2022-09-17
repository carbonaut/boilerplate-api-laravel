<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\PatchUser;
use App\Http\Requests\Api\User\PostPasswordChange;
use App\Http\Requests\Api\User\PostUserDevices;
use App\Http\Requests\Api\User\PostUserPush;
use App\Models\Device;
use App\Models\Phrase;
use App\Models\PushNotification;
use App\Models\User;
use App\Support\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    //======================================================================
    // CONSTRUCTOR
    //
    // Current authenticated user is loaded into $this->user by the parent
    // controller class
    //
    //======================================================================
    public function __construct(Request $request)
    {
        parent::__construct();
    }

    //======================================================================
    // ROUTER METHODS
    //======================================================================


    /**
     * @param request $request
     *
     * Update the user data
     *
     * @return array
     */
    public function patchUser(PatchUser $request)
    {
        $this->user->language_id = $request->language_id ?: $this->user->language_id;
        $this->user->save();

        return [];
    }

    /**
     * Revoke current user authorization token.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postUserLogout(Request $request)
    {
        User::revokeToken($this->user->token());

        return [];
    }

    /**
     * Revoke all user authorization tokens.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postUserLogoutAll(Request $request)
    {
        $tokens = $this->user->tokens;

        foreach ($tokens as $token) {
            User::revokeToken($token);
        }

        return [];
    }

    /**
     * Post a device for the user.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postUserDevices(PostUserDevices $request)
    {
        // Set the devices with this UUID to not active
        // This prevents a device from being active for two different users
        Device::where('uuid', $request->uuid)->update([
            'is_active' => false,
        ]);

        $device = Device::firstOrNew([
            'uuid'     => strip_tags($request->uuid),
            'platform' => strip_tags($request->platform),
            'user_id'  => $this->user->id,
        ]);

        // Update the device information
        $device->manufacturer = strip_tags($request->manufacturer);
        $device->model = strip_tags($request->model);
        $device->version = strip_tags($request->version);
        $device->is_virtual = boolval($request->is_virtual);

        $device->app_version = strip_tags($request->app_version);
        $device->serial = strip_tags($request->serial);
        $device->push_token = strip_tags($request->push_token);
        $device->is_active = true;

        $this->user->devices()->save($device);

        return Helpers::recursive_array_only($device->toArray(), [
            'device_id',
            'manufacturer',
            'model',
            'version',
            'platform',
            'uuid',
            'is_virtual',
            'app_version',
            'push_token',
            'serial',
        ]);
    }

    /**
     * Mark an user push notification as opened.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postUserPush(PostUserPush $request, PushNotification $push)
    {
        if ($push->device->user->id !== $this->user->id) {
            return response()->json([
                'error'   => 'This push notification is from another user',
                'message' => Phrase::getPhrase('ERROR_PUSH_FROM_ANOTHER_USER', 'api'),
            ], 403);
        }

        if ($push->opened_at === null) {
            $push->coldstart = boolval($request->coldstart);
            $push->foreground = boolval($request->foreground);
            $push->opened_at = Carbon::now();
            $push->save();
        }

        return Helpers::recursive_array_only($push->toArray(), [
            'opened_at',
            'coldstart',
            'foreground',
        ]);
    }

    /**
     * Changes the user password if given the old password.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postPasswordChange(PostPasswordChange $request)
    {
        $this->user->password = Hash::make($request->new_password);
        $this->user->save();

        $tokens = $this->user->tokens()->where('revoked', false)->get();

        foreach ($tokens as $token) {
            // Don't revoke the token currently being used by the user
            if ($token->id !== $this->user->token()->id) {
                User::revokeToken($token);
            }
        }

        return [];
    }
}
