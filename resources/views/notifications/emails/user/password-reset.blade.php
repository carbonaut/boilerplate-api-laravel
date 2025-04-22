<x-mail::message>
# {{ __('notifications.GLOBAL.INTRO-WITH-NAME', ['name' => $user->name]) }}

{{ __('notifications.USER.PASSWORD-RESET.CONTENT') }}

<x-mail::panel>
**{{ $token }}**
</x-mail::panel>

{{ __('notifications.USER.PASSWORD-RESET.DISCLAIMER') }}

{!! __('notifications.GLOBAL.OUTRO-WITH-NAME', ['name' => config('app.name')]) !!}

</x-mail::message>