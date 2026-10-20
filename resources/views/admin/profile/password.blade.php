@extends('admin.layouts.main')
@section('title','Change Password')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header"><h3>Change Password</h3></div>
        <div class="card-body">
          @if (session('status') === 'password-updated')
            <div class="alert alert-success">Password updated. A confirmation email has been sent to {{ auth()->user()->email }}.</div>
          @endif
          <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')
            <div class="form-group">
              <label>Current password</label>
              <input type="password" name="current_password" required autocomplete="current-password"
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
              @error('current_password', 'updatePassword')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>New password</label>
              <input type="password" name="password" required autocomplete="new-password"
                class="form-control @error('password', 'updatePassword') is-invalid @enderror">
              @error('password', 'updatePassword')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>Confirm new password</label>
              <input type="password" name="password_confirmation" required autocomplete="new-password" class="form-control">
            </div>
            <button class="btn btn-primary" type="submit">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
