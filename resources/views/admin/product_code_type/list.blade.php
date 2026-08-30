@push('script')
<script>
    $(document).ready(function() {
        load_product_code_type_datatable();
    });

    function load_product_code_type_datatable(
        status = "",
        start_date = "",
        end_date = ""
    ) {
        $("#my-datatable").DataTable({
            processing: true,
            serverSide: true,
            serverMethod: 'GET',

            ajax: {
                url: "{{ route('admin.product-code-type.list') }}",
                data: {
                    status: status,
                    start_date: start_date,
                    end_date: end_date
                }
            },

            columns: [

                {
                    data: null,
                    name: 'sr_no',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'usage',
                    name: 'usage',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],

            dom: "<'row'<'col-sm-6'><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'li><'col-sm-7'p>>",

            language: {
                processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>',
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            },

            order: [
                [1, "asc"]
            ],
            destroy: true
        });

        // Global search (custom input)
        $(document).on('keyup', '#global_filter', function() {
            var keyValue = $(this).val();
            var searchInput = $('input[aria-controls="my-datatable"]');
            searchInput.val(keyValue);
            $('#my-datatable').DataTable().search(keyValue).draw();
        });
    }
</script>
@endpush
