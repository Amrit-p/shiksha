@extends('admin.layouts.main')
@section('title','Enquiry Details')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="page-header">
    <div class="row align-items-end">
      <div class="col-lg-8">
        <div class="page-header-title">
          <i class="ik ik-mail bg-blue"></i>
          <div class="d-inline">
            <h5>{{ $enquiry->enquiry_number }}</h5>
            <span>Enquiry details</span>
          </div>
        </div>
      </div>
      <div class="col-lg-4 text-right">
        <a href="{{ route('admin.enquiries.index') }}" class="btn btn-outline-secondary">Back</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header"><h3>Customer</h3></div>
        <div class="card-body">
          <p><strong>Name:</strong> {{ $enquiry->name }}</p>
          <p><strong>Email:</strong> {{ $enquiry->email }}</p>
          <p><strong>Phone:</strong> {{ $enquiry->phone }}</p>
          <p><strong>Company:</strong> {{ $enquiry->company ?: '-' }}</p>
          <p><strong>Address:</strong> {{ $enquiry->address ?: '-' }}</p>
          <p><strong>City/State/PIN:</strong> {{ trim(($enquiry->city.' '.$enquiry->state.' '.$enquiry->pin_code)) ?: '-' }}</p>
          <p><strong>Message:</strong><br>{{ $enquiry->message ?: '-' }}</p>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h3>Manage</h3></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.enquiries.status', $enquiry->id) }}" class="mb-3">
            @csrf
            <label>Status</label>
            <select name="enquiry_status_id" class="form-control mb-2">
              @foreach($statuses as $status)
                <option value="{{ $status->id }}" @selected($enquiry->enquiry_status_id == $status->id)>{{ $status->name }}</option>
              @endforeach
            </select>
            <input type="text" name="note" class="form-control mb-2" placeholder="Optional status note">
            <button class="btn btn-primary btn-block" type="submit">Update Status</button>
          </form>

          <form method="POST" action="{{ route('admin.enquiries.assign', $enquiry->id) }}" class="mb-3">
            @csrf
            <label>Assign Salesperson</label>
            <select name="assigned_to" class="form-control mb-2">
              <option value="">Unassigned</option>
              @foreach($salespeople as $person)
                <option value="{{ $person->id }}" @selected($enquiry->assigned_to == $person->id)>{{ $person->name }}</option>
              @endforeach
            </select>
            <button class="btn btn-info btn-block" type="submit">Assign</button>
          </form>

          <form method="POST" action="{{ route('admin.enquiries.note', $enquiry->id) }}">
            @csrf
            <label>Internal Note</label>
            <textarea name="note" class="form-control mb-2" rows="3" required></textarea>
            <button class="btn btn-secondary btn-block" type="submit">Add Note</button>
          </form>

          @if($enquiry->internal_notes)
            <hr>
            <h6>Notes</h6>
            <pre style="white-space:pre-wrap;">{{ $enquiry->internal_notes }}</pre>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card">
        <div class="card-header"><h3>Products</h3></div>
        <div class="card-body table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Image</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Variation</th>
                <th>Qty</th>
                <th>Price</th>
              </tr>
            </thead>
            <tbody>
              @foreach($enquiry->items as $item)
                <tr>
                  <td>
                    <img src="{{ $item->product_image ? asset('storage/'.$item->product_image) : asset('front_assets/img/logo.png') }}" style="width:48px;height:48px;object-fit:cover;">
                  </td>
                  <td>{{ $item->product_title }}</td>
                  <td>{{ $item->product_sku ?: '-' }}</td>
                  <td>{{ $item->variation_label ?: '-' }}</td>
                  <td>{{ $item->qty }}</td>
                  <td>{{ $item->unit_price !== null ? '₹'.number_format($item->unit_price, 2) : '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h3>Activity</h3></div>
        <div class="card-body">
          <ul class="list-group">
            @forelse($enquiry->activities as $activity)
              <li class="list-group-item">
                <strong>{{ ucfirst(str_replace('_',' ', $activity->action)) }}</strong>
                <div>{{ $activity->description }}</div>
                <small class="text-muted">
                  {{ $activity->created_at?->format('d M Y H:i') }}
                  @if($activity->user) · by {{ $activity->user->name }} @endif
                </small>
              </li>
            @empty
              <li class="list-group-item">No activity yet.</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
