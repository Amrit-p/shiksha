@push('script')  
  <script>
    $(document).ready(function () {
      load_datatable();
    });
      function load_datatable(tellecaller = "", status = "", counselor = "", start_date = "", end_date = "", source = "",
        not_contactable = "") {
        $("#my-datatable").DataTable({
          order: [],
          "processing": true,
          "serverSide": true,
          'serverMethod': 'GET',
          "ajax": {
            "url": "{{ route('admin.color.list') }}",
            "data": {
              "tellecaller": tellecaller,
              "status": status,
              "start_date": start_date,
              "end_date": end_date,
              "source": source,
              "not_contactable": not_contactable
            }
          },
          "columns": [
            {
              data: 'checkbox',
              name: 'checkbox'
            },
            
            {
              data: 'name',
              name: 'name'
            },
             
            {
              data: 'status',
              name: 'status'
            },
            
            {
              data: 'action',
              name: 'action',
              orderable: false,
              searchable: true
            }

          ],
          "dom": "<'row'<'col-sm-6'><'col-sm-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'li><'col-sm-7'p>>",

          "language": {
            processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>',
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
          },
          order: [
            [0, "asc"]
          ],
        });
     $(document).on('keyup', '#global_filter', function() {
    var keyValue = $(this).val();
    // Mirror value to default search input
    var searchInput = $('input[aria-controls="my-datatable"]');
    searchInput.val(keyValue);
    var table = $('#my-datatable').DataTable().search(keyValue).draw();;
});
      }
  </script>
@endpush