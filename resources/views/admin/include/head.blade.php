<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="icon" href="{{ asset('backend_assets/favicon.png')}}" />

<!-- font awesome library -->
<link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">

<script src="{{ asset('backend_assets/js/app.js') }}"></script>

<!-- themekit admin template asstes -->
<link rel="stylesheet" href="{{ asset('backend_assets/all.css') }}">
<link rel="stylesheet" href="{{ asset('backend_assets/dist/css/theme.css') }}">
<link rel="stylesheet" href="{{ asset('backend_assets/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend_assets/plugins/icon-kit/dist/css/iconkit.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend_assets/plugins/ionicons/dist/css/ionicons.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">

<!--Media manager css file-->
<link rel="stylesheet" href="{{ asset('backend_assets/vendor/file-manager/css/file-manager.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- Stack array for including inline css or head elements -->
@stack('head')

<link rel="stylesheet" href="{{ asset('backend_assets/css/style.css') }}">

<style>
    .table tbody td .table-actions{
        text-align:left;
    }
</style>

