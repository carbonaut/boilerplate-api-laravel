@component('mail::message')

# {{ __('email.GLOBAL.INTRO-WITH-NAME', ['name' => $user->name]) }}

{{ __('email.USER.EMAIL-VERIFICATION.CONTENT') }}

@component('mail::panel')
**{{ $user->email_verification_code }}**
@endcomponent

{!! __('email.GLOBAL.OUTRO-WITH-NAME', ['name' => config('app.name')]) !!}

@endcomponent