<h1>Hello!</h1>

<p>You are receiving this email because we received a password reset request for your account.</p>

<p>Visit this URL for a password reset: <a href="{{ config('app.url') }}/password/reset/{{ $user }}/{{ $token }}">{{ config('app.url') }}/password/reset/{{ $user }}/{{ $token }}</a></p>

<p>If you did not request a password reset, no further action is required.</p>

<p>Regards,<br>{{ config('app.url') }}</p>
