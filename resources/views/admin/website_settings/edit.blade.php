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

        <h5 class="mt-4">Orders</h5>
        <div class="form-group">
          <input type="hidden" name="allow_order_beyond_stock" value="0">
          <label class="mb-0">
            <input type="checkbox" name="allow_order_beyond_stock" value="1" @checked(old('allow_order_beyond_stock', optional($settings->get('allow_order_beyond_stock'))->value))>
            Allow ordering more than available stock
          </label>
          <small class="form-text text-muted">When on, customers and salesmen can add any quantity to the cart and place the order even if stock is short. Stock is still deducted and can go negative.</small>
        </div>
        <div class="form-group">
          <input type="hidden" name="show_stock_quantity" value="0">
          <label class="mb-0">
            <input type="checkbox" name="show_stock_quantity" value="1" @checked(old('show_stock_quantity', optional($settings->get('show_stock_quantity'))->value))>
            Show stock quantity on product pages
          </label>
          <small class="form-text text-muted">When off, salesmen and website visitors only see "In stock" / "Out of stock", never the number of units available.</small>
        </div>

        <h5 class="mt-4">WhatsApp Notifications</h5>
        <p class="text-muted small mb-2">New contact form messages are sent to this WhatsApp number through the Meta WhatsApp Cloud API, alongside the admin email. Leave the number empty to turn WhatsApp alerts off.</p>
        <div class="form-group">
          <label>Admin WhatsApp Number</label>
          <input class="form-control" name="whatsapp_notification_number" placeholder="919876543210" value="{{ old('whatsapp_notification_number', optional($settings->get('whatsapp_notification_number'))->value) }}">
          <small class="form-text text-muted">The number that receives alerts. Include the country code, e.g. 91 for India.</small>
        </div>
        <div class="form-group">
          <label>Phone Number ID</label>
          <input class="form-control" name="whatsapp_phone_number_id" value="{{ old('whatsapp_phone_number_id', optional($settings->get('whatsapp_phone_number_id'))->value) }}">
          <small class="form-text text-muted">From Meta Developers &rarr; your app &rarr; WhatsApp &rarr; API Setup (the business number that sends the alerts).</small>
        </div>
        <div class="form-group">
          <label>Access Token</label>
          <input type="password" class="form-control" name="whatsapp_access_token" autocomplete="new-password" placeholder="{{ optional($settings->get('whatsapp_access_token'))->value ? 'Saved. Leave blank to keep it' : 'Permanent system user access token' }}">
        </div>
        <div class="form-group">
          <label>Message Template Name</label>
          <input class="form-control" name="whatsapp_template_name" placeholder="contact_message_alert" value="{{ old('whatsapp_template_name', optional($settings->get('whatsapp_template_name'))->value) }}">
          <small class="form-text text-muted">An approved template whose body has 4 variables: @{{1}} name, @{{2}} phone, @{{3}} email, @{{4}} message. Without a template, WhatsApp only delivers if the admin number has messaged the business number in the last 24 hours.</small>
        </div>
        <div class="form-group">
          <label>Template Language Code</label>
          <input class="form-control" name="whatsapp_template_language" placeholder="en" value="{{ old('whatsapp_template_language', optional($settings->get('whatsapp_template_language'))->value) }}">
        </div>

        <button class="btn btn-primary" type="submit">Save Settings</button>
      </form>
    </div>
  </div>
</div>
@endsection
