<x-mail::message>

# {{ __('email.GLOBAL.INTRO-WITH-NAME', ['name' => $user->name]) }}

{{ __('email.USER.EMAIL-VERIFICATION.CONTENT') }}

<x-mail::panel>
**{{ $user->email_verification_code }}**
</x-mail::panel>

{!! __('email.GLOBAL.OUTRO-WITH-NAME', ['name' => config('app.name')]) !!}

</x-mail::message>