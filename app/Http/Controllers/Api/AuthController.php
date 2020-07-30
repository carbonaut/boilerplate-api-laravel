<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Models\Email;
use App\Models\Phrase;
use App\Models\User;
use App\Rules\PasswordStrength;
use App\Support\Helpers;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller {
    use ResetsPasswords;

    //======================================================================
    // CONSTRUCTOR
    //
    // Avoid calling parent controller as auth routes are not auth'ed
    //======================================================================
    public function __construct(Request $request) {
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
    public function postRegister(Request $request) {
        $request->validate([
            'first_name'               => 'required|string',
            'last_name'                => 'required|string|different:first_name',
            'email'                    => 'required|email:filter|unique:App\Models\User,email',
            'date_of_birth'            => 'required|date_format:Y-m-d', 'after_or_equal:' . Carbon::now()->subYears(110)->toDateString(),
            'gender'                   => 'required|integer|in:1,2,9',
            'password'                 => ['required', 'same:password_confirmation', new PasswordStrength()],
            'password_confirmation'    => 'required',
            'language_id'              => 'nullable|uuid|exists:App\Models\Language,id',
        ]);

        $user = new User();
        $user->first_name = strip_tags($request->first_name);
        $user->last_name = strip_tags($request->last_name);
        $user->email = strip_tags($request->email);
        $user->gender = intval($request->gender);
        $user->password = Hash::make($request->password);
        $user->email_verification_code = User::generateVerificationCode();
        $user->email_verification_code_expires_at = Carbon::now()->addMinutes(15);
        $user->language_id = $request->language_id;
        $user->date_of_birth = $request->date_of_birth;
        $user->save();

        // Send the email verification email
        $email = new Email();
        $email->user_id = $user->user_id;
        $email->mailable = new EmailVerification($user);
        $email->type = 'email-verification';
        $email->save();

        return Helpers::recursive_array_only($user->toArray(), [
            'email_verified',
        ]);
    }

    /**
     * Requests an email verification code to be sent to the requested email.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postEmailVerificationRequest(Request $request) {
        $request->validate([
            'email' => 'required|email:filter',
        ]);

        $user = User::where('email', $request->email)->where('email_verified_at', null)->first();

        if ($user === null) {
            return response()->json([
                'error'   => 'Email was not found or the user already had the email verified.',
                'message' => Phrase::getPhrase('ERROR_EMAIL_VERIFIED_OR_NOT_FOUND', 'api'),
            ], 400);
        }

        $user->email_verification_code = User::generateVerificationCode();
        $user->email_verification_code_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // Send the email verification email
        $email = new Email();
        $email->user_id = $user->user_id;
        $email->mailable = new EmailVerification($user);
        $email->type = 'email-verification';
        $email->save();

        return Helpers::recursive_array_only($user->toArray(), [
            'email_verified',
        ]);
    }

    /**
     * Verifies an email by the verification code.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postEmailVerificationConfirm(Request $request) {
        $request->validate([
            'email'                   => 'required|email:filter',
            'email_verification_code' => 'required|numeric|integer',
        ]);

        $user = User::where('email', $request->email)->where('email_verification_code', intval($request->email_verification_code))->first();

        if ($user === null) {
            return response()->json([
                'error'   => 'No user with the requested email and verification code.',
                'message' => Phrase::getPhrase('ERROR_EMAIL_OR_VERIFICATION_CODE_NOT_FOUND', 'api'),
            ], 400);
        }

        if (Carbon::now() > $user->email_verification_code_expires_at) {
            return response()->json([
                'error'   => 'The verification code has expired.',
                'message' => Phrase::getPhrase('ERROR_EMAIL_VERIFICATION_CODE_EXPIRED', 'api'),
            ], 400);
        }

        $user->email_verified_at = Carbon::now();
        $user->email_verification_code = null;
        $user->email_verification_code_expires_at = null;
        $user->save();

        return [];
    }

    /**
     * Sends a password reset token by email to the user.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postPasswordResetRequest(Request $request) {
        $request->validate([
            'email' => 'required|email:filter|exists:App\Models\User,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Password::broker()->createToken($user);

        // Send the password reset email
        $email = new Email();
        $email->user_id = $user->user_id;
        $email->mailable = new PasswordReset($user, $token);
        $email->type = 'password-reset';
        $email->save();

        return [];
    }

    /**
     * Resets the user password if token is valid.
     *
     * @param Request $request
     *
     * @return array
     */
    public function postPasswordResetSubmit(Request $request) {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email:filter|exists:App\Models\User,email',
            'password'              => ['required', 'same:password_confirmation', new PasswordStrength()],
            'password_confirmation' => 'required',
        ]);

        // Response is only sent on success, otherwise exception is thrown
        $response = $this->reset($request);

        // Since reset was successful, invalidate all previous tokens
        $tokens = User::where('email', $request->email)->first()->tokens;

        foreach ($tokens as $token) {
            User::revokeToken($token);
        }

        return $response;
    }
}
