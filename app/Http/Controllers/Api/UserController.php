<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Phrase;
use App\Models\PushNotification;
use App\Models\User;
use App\Rules\PasswordStrength;
use App\Support\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller {
    //======================================================================
    // CONSTRUCTOR
    //
    // Current authenticated user is loaded into $this->user by the parent
    // controller class
    //
    //======================================================================
    public function __construct(Request $request) {
        parent::__construct();
    }

    //======================================================================
    // ROUTER METHODS
    //======================================================================

    /**
     * Returns user data.
     *
     * @param Request $request
     *
     * @return array
     */
    public function getUser(Request $request) {
        $this->user->load('language');

        return Helpers::recursive_array_only($this->user->toArray(), [
            'user_id',
            'title',
            'first_name',
            'last_name',
            'email',
            'gender',
            'language',
            'language.language_id',
            'language.name',
            'language.locale',
        ]);
    }

    /**
     * @param request $request
     *
     * Update the user data
     *
     * @return array
     */
    public function patchUser(Request $request) {
        $request->validate([
            'language_id' => 'nullable|uuid|exists:App\Models\Language,id',
        ]);

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
    public function postUserLogout(Request $request) {
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
    public function postUserLogoutAll(Request $request) {
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
    public function postUserDevices(Request $request) {
        $request->validate([
            'manufacturer' => 'required|string|max:255',
            'model'        => 'required|string|max:255',
            'version'      => 'required|string|max:255',
            'platform'     => 'required|string|max:255',
            'uuid'         => 'required|string|max:255',
            'is_virtual'   => 'required|boolean',
            'app_version'  => 'string|max:255',
            'push_token'   => 'string|min:64|max:255',
            'serial'       => 'string|max:255',
        ]);

        // Set the devices with this UUID to not active
        // This prevents a device from being active for two different users
        Device::where('uuid', $request->uuid)->update([
            'is_active'=> false,
        ]);

        $device = Device::firstOrNew([
            'uuid'    => strip_tags($request->uuid),
            'platform'=> strip_tags($request->platform),
            'user_id' => $this->user->id,
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
    public function postUserPush(Request $request, PushNotification $push) {
        $request->validate([
            'coldstart' => 'boolean',
            'foreground'=> 'boolean',
        ]);

        if ($push->device->user->id !== $this->user->id) {
            return response()->json([
                'error'    => 'This push notification is from another user',
                'message'  => Phrase::getPhrase('ERROR_PUSH_FROM_ANOTHER_USER', 'api'),
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
    public function postPasswordChange(Request $request) {
        $request->validate([
            'old_password'              => ['required', 'password'],
            'new_password'              => ['required', 'confirmed', new PasswordStrength()],
        ]);

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
