<x-mail::message>

# {{ __('notifications.GLOBAL.INTRO-WITH-NAME', ['name' => $user->name]) }}

{{ __('notifications.USER.EMAIL-VERIFICATION.CONTENT') }}

<x-mail::panel>
**{{ $user->email_verification_code }}**
</x-mail::panel>

{!! __('notifications.GLOBAL.OUTRO-WITH-NAME', ['name' => config('app.name')]) !!}

</x-mail::message>