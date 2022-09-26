@component('mail::message')

# {{ __('email.GLOBAL.INTRO-WITH-NAME', ['name' => $user->name]) }}

{{ __('email.USER.PASSWORD-RESET.CONTENT') }}

@component('mail::panel')
**{{ $token }}**
@endcomponent

{{ __('email.USER.PASSWORD-RESET.DISCLAIMER') }}

{!! __('email.GLOBAL.OUTRO-WITH-NAME', ['name' => config('app.name')]) !!}

@endcomponent