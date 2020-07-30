@component('mail::message', [
    'email_id'              => $email_id,
    'header_title'          => \App\Models\Phrase::getPhrase('EMAIL_EMAIL_VERIFICATION_TITLE', 'email'),
    'display_app_cta'       => true,
    'display_app_cta_class' => 'dark',
])

{{-- Intro --}}
{!! \App\Models\Phrase::getPhrase('EMAIL_INTRO_NAME_WELCOME', 'email', [
    '{full_name}' => htmlspecialchars($user->full_name),
]) !!}

{{-- Content #1 --}}
{!! \App\Models\Phrase::getPhrase('EMAIL_EMAIL_VERIFICATION_CONTENT_1', 'email') !!}

<h2>
@foreach(str_split($user->email_verification_code) as $char)<span>{{ $char }}</span>@endforeach
</h2>

{{-- Content #2 --}}
{!! \App\Models\Phrase::getPhrase('EMAIL_EMAIL_VERIFICATION_CONTENT_2', 'email', [
    '{email}'               => $user->email,
]) !!}

{{-- Outro --}}
<span class="outro">
{!! \App\Models\Phrase::getPhrase('EMAIL_OUTRO_LOOKING_FORWARD', 'email') !!}
{!! \App\Models\Phrase::getPhrase('EMAIL_OUTRO_TEAM', 'email', [
    '{team}' => config('app.team'),
]) !!}
</span>
@endcomponent
