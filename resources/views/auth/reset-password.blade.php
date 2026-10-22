@extends('admin.layouts.auth')
@php($isInvite = $request->boolean('invite'))
@section('title', $isInvite ? 'Set Password' : 'Reset Password')
@section('form')
    <h3>{{ $isInvite ? __('Set your password') : __('Choose a new password') }}</h3>
    <p>{{ $isInvite ? __('Welcome! Choose a password to activate your account.') : __('Enter a new password for your account.') }}</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        @if ($isInvite)
            <input type="hidden" name="invite" value="1">
        @endif

        <div class="form-group">
            <input type="email" name="email" placeholder="Email" value="{{ old('email', $request->email) }}" required readonly
                class="form-control @error('email') is-invalid @enderror">
            <i class="ik ik-mail"></i>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="New password" required autofocus autocomplete="new-password"
                class="form-control @error('password') is-invalid @enderror">
            <i class="ik ik-lock"></i>
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="form-group">
            <input type="password" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password"
                class="form-control">
            <i class="ik ik-lock"></i>
        </div>
        <div class="sign-btn text-center">
            <button class="btn btn-theme">{{ $isInvite ? __('Set password') : __('Reset password') }}</button>
        </div>
    </form>
@endsection
