<x-mail::message>
# {{ __('email.GLOBAL.INTRO-WITH-NAME', ['name' => $user->name]) }}

{{ __('email.USER.PASSWORD-RESET.CONTENT') }}

<x-mail::panel>
**{{ $token }}**
</x-mail::panel>

{{ __('email.USER.PASSWORD-RESET.DISCLAIMER') }}

{!! __('email.GLOBAL.OUTRO-WITH-NAME', ['name' => config('app.name')]) !!}

</x-mail::message>