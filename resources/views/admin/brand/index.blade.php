@extends('admin.layouts.main')
@section('title', 'Products')
@section('content')
@include('admin.include.message')

    <div class="container-fluid">
       <div class="page-header">
        <div class="row align-items-end">
          <div class="col-lg-8">
            <div class="page-header-title">
              <i class="ik ik-image bg-blue"></i>
              <div class="d-inline">
                <h5>Brand</h5>
                <span>View, delete and update Brand</span>
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
                  <a href="#">Brand</a>
                </li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
      <div class="row">
      @include('admin.brand.create')
        <!-- list layout 1 start -->
        <div class="col-md-12">
          <div class="card">
            <div class="card-header row">
              <div class="col col-sm-1">
                <div class="card-options d-inline-block">
                  <div class="dropdown d-inline-block">
                    <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button"
                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                        class="ik ik-more-horizontal"></i></a>
                    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="moreDropdown">
                      <a class="dropdown-item" href="#">Delete</a>
                      <a class="dropdown-item" href="#">More Action</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col col-sm-6">
                @include('admin.brand.search')
              </div>
              <div class="col col-sm-5">
                <div class="card-options text-right">
                  <a href="javascript:void(0)" class="btn btn-outline-primary btn-semi-rounded" data-toggle="modal"
                    data-target="#addModal">
                    Add Brand
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
                        <input type="checkbox" class="custom-control-input" id="selectall" name=""
                          value="option2">
                        <span class="custom-control-label">&nbsp;</span>
                      </label>
                    </th>
                    <th class="nosort">Image</th>
                    <th>Name</th>
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
    <div class="modal fade edit-layout-modal pr-0" id="productView" tabindex="-1" role="dialog"
      aria-labelledby="productViewLabel" aria-hidden="true">
    @include('admin.brand.list') 
    @include('admin.brand.delete')
@endsection
