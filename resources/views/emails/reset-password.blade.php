@extends('emails.layout')

@section('email.title', 'Reset your Kopi Rider password')

@section('email.heading', 'Forgot your password?')
@section('email.content')
  <p style="margin:0 0 12px;">Hi <b>{{ $user->name }}</b>,</p>

  <p style="margin:0 0 12px;">
    We received a request to reset the password for the staff account
    <b>{{ $user->email }}</b>. No worries &mdash; it happens to the best of us,
    especially before the first coffee of the day&nbsp;&#9749;.
  </p>

  <p style="margin:0 0 12px;">
    Click the button below to choose a new password. For your security,
    this link can only be used once and expires in
    <b>{{ $expiresMinutes }} minutes</b>.
  </p>
@endsection

@section('email.note')
  <p style="margin:0;">
    If you didn't request a password reset, you can safely ignore this e-mail &mdash;
    your current password will keep working and nothing will change.
    Never share this link with anyone; the Kopi Rider team will never ask for it.
  </p>
@endsection
