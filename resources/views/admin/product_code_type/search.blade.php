@push('head')
<style>
    /* Hide default DataTables search box */
    #my-datatable_filter {
        display: none !important;
    }
</style>
@endpush

<div class="card-search with-adv-search dropdown">
    <form action="javascript:void(0);">

        <input type="text"
               class="form-control global_filter"
               id="global_filter"
               placeholder="Search Product Code Type..."
               required>

        <button type="submit" class="btn btn-icon">
            <i class="ik ik-search"></i>
        </button>

        {{-- <button type="button"
                id="adv_wrap_toggler_1"
                class="adv-btn ik ik-chevron-down dropdown-toggle"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
        </button> --}}

        <div class="adv-search-wrap dropdown-menu dropdown-menu-right"
             aria-labelledby="adv_wrap_toggler_1">

            <div class="row">

                <div class="col-md-12">
                    <div class="form-group">
                        <input type="text"
                               class="form-control column_filter"
                               placeholder="Product Code Type"
                               data-column="1">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <select class="form-control column_filter"
                                data-column="2">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

            </div>

            <button type="button" class="btn btn-theme apply-filter">
                Search
            </button>
        </div>
    </form>
</div>
