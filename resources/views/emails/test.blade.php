@extends('emails.layout')

@section('email.title', 'Kopi Rider SMTP test')

@section('email.heading', 'SMTP is working! &#9989;')
@section('email.content')
  <p style="margin:0 0 12px;">Hello,</p>

  <p style="margin:0 0 12px;">
    This is a test message from the <b>Kopi Rider</b> admin panel
    (&ldquo;Settings &rarr; E-mail (SMTP)&rdquo;).
  </p>

  <p style="margin:0 0 12px;">
    It was delivered to <b>{{ $recipient }}</b> on
    <b>{{ now()->settings(['timezone' => config('app.timezone')])->format('d M Y, H:i') }}</b>
    using your configured SMTP server &mdash; password resets and future
    notifications will use the same channel.
  </p>

  <p style="margin:0;">
    Sent from server <b>{{ gethostname() }}</b> &middot; PHP {{ PHP_VERSION }} &middot;
    Laravel {{ app()->version() }}.
  </p>
@endsection

@section('email.note')
  <p style="margin:0;">
    If you received this e-mail, everything is configured correctly. You can safely
    delete it. Kopi&nbsp;Rider &mdash; crafted coffee, Bali vibes.
  </p>
@endsection
