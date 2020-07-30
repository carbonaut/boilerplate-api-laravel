@component('mail::message', [
    'email_id'              => $email_id,
    'header_title'          => \App\Models\Phrase::getPhrase('EMAIL_PASSWORD_RESET_TITLE', 'email'),
    'display_app_cta'       => true,
    'display_app_cta_class' => 'dark',
])

{{-- Intro --}}
{!! \App\Models\Phrase::getPhrase('EMAIL_INTRO_NAME_NO_PROBLEM', 'email', [
    '{full_name}' => htmlspecialchars($user->full_name),
]) !!}

{{-- Content #1 --}}
{!! \App\Models\Phrase::getPhrase('EMAIL_PASSWORD_RESET_CONTENT_1', 'email') !!}

{{-- Button --}}
@component('mail::button', ['url' => $url])
{!! \App\Models\Phrase::getPhrase('EMAIL_PASSWORD_RESET_BUTTON', 'email') !!}
@endcomponent

{{-- Content #2 --}}
{!! \App\Models\Phrase::getPhrase('EMAIL_PASSWORD_RESET_CONTENT_2', 'email', [
    '{url}'   => $url,
    '{reset}' => config('app.app_password_reset_url'),
]) !!}

{{-- Outro --}}
<span class="outro">
{!! \App\Models\Phrase::getPhrase('EMAIL_OUTRO_TAKE_CARE', 'email') !!}
{!! \App\Models\Phrase::getPhrase('EMAIL_OUTRO_TEAM', 'email', [
    '{team}' => config('app.team'),
]) !!}
</span>
@endcomponent
