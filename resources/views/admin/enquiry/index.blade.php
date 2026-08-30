@extends('admin.layouts.main')
@section('title','Enquiries')
@section('content')
@include('admin.include.message')
<div class="container-fluid">
  <div class="page-header">
    <div class="row align-items-end">
      <div class="col-lg-8">
        <div class="page-header-title">
          <i class="ik ik-mail bg-blue"></i>
          <div class="d-inline">
            <h5>Enquiries</h5>
            <span>Customer website enquiries</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-3 mb-2">
          <input type="text" id="enquiry_search" class="form-control" placeholder="Search name, email, phone, product...">
        </div>
        <div class="col-md-2 mb-2">
          <select id="enquiry_status" class="form-control">
            <option value="">All statuses</option>
            @foreach($statuses as $status)
              <option value="{{ $status->id }}">{{ $status->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 mb-2">
          <select id="enquiry_assignee" class="form-control">
            <option value="">All assignees</option>
            @foreach($salespeople as $person)
              <option value="{{ $person->id }}">{{ $person->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 mb-2">
          <input type="date" id="enquiry_from" class="form-control">
        </div>
        <div class="col-md-2 mb-2">
          <input type="date" id="enquiry_to" class="form-control">
        </div>
      </div>
    </div>
    <div class="card-body">
      <table id="enquiry-datatable" class="table">
        <thead>
          <tr>
            <th>Enquiry #</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Items</th>
            <th>Assigned</th>
            <th>Created</th>
            <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
@endsection

@push('script')
<script>
  $(function () {
    var table = $('#enquiry-datatable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('admin.enquiries.list') }}",
        data: function (d) {
          d.keyword = $('#enquiry_search').val();
          d.status_id = $('#enquiry_status').val();
          d.assigned_to = $('#enquiry_assignee').val();
          d.date_from = $('#enquiry_from').val();
          d.date_to = $('#enquiry_to').val();
        }
      },
      columns: [
        { data: 'enquiry_number', name: 'enquiry_number' },
        { data: 'name', name: 'name' },
        { data: 'phone', name: 'phone' },
        { data: 'status_badge', name: 'enquiry_status_id', orderable: false },
        { data: 'items_count', name: 'items_count' },
        { data: 'assignee_name', name: 'assigned_to', orderable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
      ]
    });

    $('#enquiry_search, #enquiry_status, #enquiry_assignee, #enquiry_from, #enquiry_to').on('change keyup', function () {
      table.ajax.reload();
    });
  });
</script>
@endpush
