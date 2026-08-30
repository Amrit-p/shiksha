@extends('layouts.main')
@section('title', 'Edit Brand')
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
               <h5>{{ __('Edit Brand')}}</h5>
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
                  <a href="#">{{ __('Edit Brand')}}</a>
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
         <h3>{{ __('Edit brand')}}</h3>
      </div>
      <div class="card-body">
         <form class="forms-sample" method="POST" action="{{route('brand.store')}}" enctype="multipart/form-data" >
            @csrf
            <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                     <label for="name">{{ __('Brand')}}<span class="text-red">*</span></label>
                     <input id="name" type="text" value="<?=$data->name?$data->name:''?>" class="form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" placeholder="Enter Brand Name" required>
                     <div class="help-block with-errors"></div>
                     @error('name')
                     <span class="invalid-feedback" role="alert">
                     <strong>{{ $message }}</strong>
                     </span>
                     @enderror
                  </div>
                  <div class="form-group">
                     <label class="col-sm-3 col-form-label">Select File:</label>
                     <img id="blah" class="mb-1"  style="opacity: 4;width: 120px; height:120px; border:1px solid black;margin-left:8px;" src="{{asset('img/brand//'.$data->images)}}" alt="your image" />
                     <input accept="image/png, image/gif, image/jpeg" value="{{old('images')}}" type="file" class="form-control {{$errors->has('images')?'is-invalid':''}}" style="background-color:white" name="images" id="" onchange="readURL(this)";/>
                     <input type="hidden" name="old_pic" value="{{$data->images}}">
                     @if ($errors->has('images'))
                     <span class="fields_invalid">
                     <strong>{{ $errors->first('images') }}.</strong>
                     </span>
                     @endif
                     <script>
                        function readURL(input) {
                          if (input.files && input.files[0]) {
                              var reader = new FileReader();
                              reader.onload = function (e) {
                                  $('#blah')
                                      .attr('src', e.target.result);
                              };
                              reader.readAsDataURL(input.files[0]);
                          }
                           }
                     </script>
                     <div class="form-group">
                        <label for="phone_number">{{ __('status')}}<span class="text-red">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required autofocus>
                           <option value="1" <?=$data->status == "1" ? 'selected="selected"' : ''; ?>>Active</option>
                           <option value="0" <?= $data->status == "0" ? 'selected="selected"' : ''; ?>>Inactive</option>
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
                           <button type="submit" class="btn btn-primary">{{ __('Submit')}}</button>
                        </div>
                     </div>
                  </div>
                  <input type="hidden" name="brand_id" value="{{$data->id}}">
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
