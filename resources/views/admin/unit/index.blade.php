@extends('admin.layouts.main')
@section('title', 'Unit')
@section('content')

@include('admin.include.message')

<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="ik ik-package bg-blue"></i>
                    <div class="d-inline">
                        <h5>Unit</h5>
                        <span>View, delete and update Units</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/dashboard"><i class="ik ik-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#">Unit</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- CREATE UNIT MODAL --}}
        @include('admin.unit.create')

        <div class="col-md-12">
            <div class="card">
                <div class="card-header row">
                    <div class="col col-sm-1">
                        <div class="card-options d-inline-block">
                            <div class="dropdown d-inline-block">
                                <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button"
                                   data-toggle="dropdown">
                                    <i class="ik ik-more-horizontal"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-left">
                                    <a class="dropdown-item" href="#">Delete</a>
                                    <a class="dropdown-item" href="#">More Action</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col col-sm-6">
                        {{-- SEARCH --}}
                        @include('admin.unit.search')

                        {{-- EDIT MODAL --}}
                        @include('admin.unit.edit',[
                            'modal_header' => 'Edit Unit',
                            'heder_font'   => 'ik-package'
                        ])
                    </div>

                    <div class="col col-sm-5">
                        <div class="card-options text-right">
                            <a href="javascript:void(0)"
                               class="btn btn-outline-primary btn-semi-rounded"
                               data-toggle="modal"
                               data-target="#addUnitModal">
                                Add Unit
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="my-datatable" class="table">
                        <thead>
                        <tr>
                            <th class="nosort" width="10">
                                <label class="custom-control custom-checkbox m-0">
                                    <input type="checkbox" class="custom-control-input" id="selectall">
                                    <span class="custom-control-label">&nbsp;</span>
                                </label>
                            </th>
                            <th>Name</th>
                            <th>Short Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- VIEW / LIST MODAL --}}
<div class="modal fade edit-layout-modal pr-0"
     id="unitView"
     tabindex="-1"
     role="dialog">
    @include('admin.unit.list')
    @include('admin.unit.delete')
</div>

@endsection
