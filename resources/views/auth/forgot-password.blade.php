@extends('admin.layouts.auth')
@section('title', 'Forgot Password')
@section('form')
    <h3>{{ __('Forgot your password?') }}</h3>
    <p>{{ __('Enter your account email and we will send you a link to choose a new password.') }}</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus
                class="form-control @error('email') is-invalid @enderror">
            <i class="ik ik-mail"></i>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>
        <div class="sign-btn text-center">
            <button class="btn btn-theme">{{ __('Email reset link') }}</button>
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('login') }}">{{ __('Admin sign in') }}</a>
            &middot;
            <a href="{{ route('salesman.login') }}">{{ __('Salesman sign in') }}</a>
        </div>
    </form>
@endsection
