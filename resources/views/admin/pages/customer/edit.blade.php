@extends('layouts.main') 
@section('title', 'Edit Customer')
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <!-- <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}"> -->
    @endpush

    
    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Edit Customer')}}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('dashboard')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('Edit Customer')}}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('Edit customer')}}</h3>
                    </div>
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{route('admin.customer.update', !empty($customer) && isset($customer->id) ? $customer->id : '')}}" >
                        @csrf
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="form-group">
                                        <label for="name">{{ __('Name')}}<span class="text-red">*</span></label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{!empty($customer) && isset($customer->name) ? $customer->name : ''}}" placeholder="Enter Customer Name" required>
                                        <div class="help-block with-errors"></div>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">{{ __('Email')}}<span class="text-red">*</span></label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{!empty($customer) && isset($customer->email) ? $customer->email : ''}}" placeholder="Enter email address" required>
                                        <div class="help-block with-errors" ></div>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                   
                                    <div class="form-group">
                                        <label for="phone_number">{{ __('Phone Number')}}<span class="text-red">*</span></label>
                                        <input id="phone_number" type="tel" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" placeholder="Enter Phone Number" value="{{!empty($customer) && isset($customer->phone_number) ? $customer->phone_number : ''}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('phone_number')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">{{ __('Notes')}}</label>
                                        <textarea id="notes" class="form-control" name="notes" placeholder="Enter Note">{{!empty($customer) && isset($customer->notes) ? $customer->notes : ''}}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone_number">{{ __('status')}}<span class="text-red">*</span></label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror" required autofocus>
                                            <option value="1" <?php echo (!empty($customer) && isset($customer['status']) && ($customer['status'] == "1") ? 'selected="selected"' : ''); ?>>Active</option>
                                            <option value="0" <?php echo (!empty($customer) && isset($customer['status']) && ($customer['status'] == "0") ? 'selected="selected"' : ''); ?>>Inactive</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                            @error('status')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                    </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <a href="{{route('admin.customers.list')}}" class="btn btn-info">Back</a>
                                        <button type="submit" class="btn btn-primary">{{ __('Submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script') 
        <!-- <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script> -->
         <!--get role wise permissiom ajax script-->
        <!-- <script src="{{ asset('js/get-role.js') }}"></script> -->
    @endpush
@endsection
