@extends('admin.layouts.main')
@section('title','Website Settings')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="card">
    <div class="card-header"><h3>Website Settings</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.website-settings.update') }}" enctype="multipart/form-data">
        @csrf
        <h5>General</h5>
        <div class="form-group"><label>Website Name</label><input class="form-control" name="website_name" value="{{ old('website_name', optional($settings->get('website_name'))->value) }}"></div>
        <div class="form-group"><label>Website Title</label><input class="form-control" name="website_title" value="{{ old('website_title', optional($settings->get('website_title'))->value) }}"></div>
        <div class="form-group"><label>Description</label><textarea class="form-control" name="website_description" rows="3">{{ old('website_description', optional($settings->get('website_description'))->value) }}</textarea></div>
        <div class="form-group"><label>Contact Email</label><input class="form-control" name="contact_email" value="{{ old('contact_email', optional($settings->get('contact_email'))->value) }}"></div>
        <div class="form-group"><label>Contact Phone</label><input class="form-control" name="contact_phone" value="{{ old('contact_phone', optional($settings->get('contact_phone'))->value) }}"></div>
        <div class="form-group"><label>Address</label><textarea class="form-control" name="address" rows="2">{{ old('address', optional($settings->get('address'))->value) }}</textarea></div>
        <div class="form-group"><label>Admin Notification Email</label><input class="form-control" name="admin_notification_email" value="{{ old('admin_notification_email', optional($settings->get('admin_notification_email'))->value) }}"></div>

        <h5 class="mt-4">Branding</h5>
        <div class="form-group"><label>Primary Color</label><input type="color" class="form-control" name="primary_color" value="{{ old('primary_color', optional($settings->get('primary_color'))->value ?: '#E6007E') }}"></div>
        <div class="form-group"><label>Secondary Color</label><input type="color" class="form-control" name="secondary_color" value="{{ old('secondary_color', optional($settings->get('secondary_color'))->value ?: '#00ADEF') }}"></div>
        <div class="form-group"><label>Button Color</label><input type="color" class="form-control" name="button_color" value="{{ old('button_color', optional($settings->get('button_color'))->value ?: '#E6007E') }}"></div>
        <div class="form-group"><label>Logo</label><input type="file" class="form-control" name="logo" accept="image/*"></div>
        <div class="form-group"><label>Favicon</label><input type="file" class="form-control" name="favicon" accept="image/*"></div>

        <h5 class="mt-4">Social</h5>
        <div class="form-group"><label>Facebook</label><input class="form-control" name="facebook" value="{{ old('facebook', optional($settings->get('facebook'))->value) }}"></div>
        <div class="form-group"><label>Instagram</label><input class="form-control" name="instagram" value="{{ old('instagram', optional($settings->get('instagram'))->value) }}"></div>
        <div class="form-group"><label>YouTube</label><input class="form-control" name="youtube" value="{{ old('youtube', optional($settings->get('youtube'))->value) }}"></div>
        <div class="form-group"><label>LinkedIn</label><input class="form-control" name="linkedin" value="{{ old('linkedin', optional($settings->get('linkedin'))->value) }}"></div>

        <h5 class="mt-4">SEO</h5>
        <div class="form-group"><label>Meta Keywords</label><input class="form-control" name="meta_keywords" value="{{ old('meta_keywords', optional($settings->get('meta_keywords'))->value) }}"></div>

        <button class="btn btn-primary" type="submit">Save Settings</button>
      </form>
    </div>
  </div>
</div>
@endsection
