@extends('front/layout')
@section('page_title','Change Password')
@section('container')
<section id="cart-view">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <h3 style="margin:30px 0 20px;">Change Password</h3>
        @if (session('status') === 'password-updated')
          <div class="alert alert-success">Password updated. A confirmation email has been sent to {{ auth()->user()->email }}.</div>
        @endif
        <form method="POST" action="{{ route('password.update') }}">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label>Current password</label>
            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
            @error('current_password', 'updatePassword')<span class="text-danger">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>New password</label>
            <input type="password" name="password" class="form-control" required autocomplete="new-password">
            @error('password', 'updatePassword')<span class="text-danger">{{ $message }}</span>@enderror
          </div>
          <div class="form-group">
            <label>Confirm new password</label>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
          </div>
          <button type="submit" class="aa-primary-btn" style="margin-bottom:40px;">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
