@extends('admin.layouts.main')
@section('title','SMTP Settings')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header"><h3>SMTP Configuration</h3></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.smtp.update') }}">
            @csrf
            <div class="form-group"><label>Host</label><input class="form-control" name="host" value="{{ old('host', $smtp->host) }}"></div>
            <div class="form-group"><label>Port</label><input class="form-control" name="port" type="number" value="{{ old('port', $smtp->port) }}"></div>
            <div class="form-group"><label>Username</label><input class="form-control" name="username" value="{{ old('username', $smtp->username) }}"></div>
            <div class="form-group"><label>Password</label><input class="form-control" type="password" name="password" placeholder="Leave blank to keep current"></div>
            <div class="form-group">
              <label>Encryption</label>
              <select class="form-control" name="encryption">
                <option value="">None</option>
                <option value="tls" @selected(old('encryption', $smtp->encryption) === 'tls')>TLS</option>
                <option value="ssl" @selected(old('encryption', $smtp->encryption) === 'ssl')>SSL</option>
              </select>
            </div>
            <div class="form-group"><label>From Email</label><input class="form-control" name="from_email" value="{{ old('from_email', $smtp->from_email) }}"></div>
            <div class="form-group"><label>From Name</label><input class="form-control" name="from_name" value="{{ old('from_name', $smtp->from_name) }}"></div>
            <div class="form-group"><label>Admin Email</label><input class="form-control" name="admin_email" value="{{ old('admin_email', $smtp->admin_email) }}"></div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $smtp->is_active))>
              <label class="form-check-label">Active</label>
            </div>
            <button class="btn btn-primary" type="submit">Save SMTP</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card">
        <div class="card-header"><h3>Send Test Email</h3></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.smtp.test') }}">
            @csrf
            <div class="form-group">
              <label>Test Email</label>
              <input class="form-control" type="email" name="test_email" required>
            </div>
            <button class="btn btn-info" type="submit">Send Test</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
