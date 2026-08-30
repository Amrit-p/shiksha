@extends('admin.layouts.main')
@section('title', 'Variant Name')

@section('content')
    @include('admin.include.message')

    <div class="container-fluid">

        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-sliders bg-blue"></i>
                        <div class="d-inline">
                            <h5>Variant Name</h5>
                            <span>View, delete and update Variant Names</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/dashboard"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">Variant Name</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="row">

            {{-- CREATE MODAL --}}
            @include('admin.variant_name.create')

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header row">

                        <div class="col col-sm-6">
                            @include('admin.variant_name.search')
                            @include('admin.variant_name.edit', [
                                'modal_header' => 'Edit Variant Name',
                                'heder_font' => 'ik-sliders',
                            ])
                        </div>

                        <div class="col col-sm-6 text-right">
                            <a href="javascript:void(0)" class="btn btn-outline-primary btn-semi-rounded"
                                data-toggle="modal" data-target="#addVariantNameModal">
                                Add Variant Name
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="my-datatable" class="table">
                            <thead>
                                <tr>
                                    <th width="10">Sr.No</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('admin.variant_name.list')
    @include('admin.variant_name.delete')

@endsection
