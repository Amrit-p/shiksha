@extends('layouts.main') 
@section('title', 'Dashboard')
@section('content')
    <!-- push external head elements to head -->
    @push('head')

        <link rel="stylesheet" href="{{ asset('plugins/weather-icons/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/chartist/dist/chartist.min.css') }}">
    @endpush

    <div class="container-fluid">
    	<div class="row">
            <div class="form-group col-md-6">
                <label for="image_label">Image</label>
                <div class="input-group">
                    <input type="text" id="image1" class="form-control" name="image"
                        aria-label="Image" aria-describedby="button-image">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="button-image">Select</button>
                    </div>
                </div>
                <img src="" id="imagePreview" style="width:100px;">
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-block">
                        <div id="fm-main-block">
                            <div id="fm" class="fm" style="min-height: 700px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            
    	</div>
    </div>
	<!-- push external js -->
    @push('script')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded',function(){
            document.getElementById('fm-main-block').setAttribute('style', 'height:' +window.innerHeight+'px');
            document.getElementById('fm').setAttribute('style', 'min-height:600px');

            fm.$store.commit('fm/setFileCallBack',function(fileUrl){
                window.opener.fmSetLink(fileUrl);
                window.close();
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

        document.getElementById('button-image').addEventListener('click', (event) => {
        event.preventDefault();

        inputId = 'image1';

        window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
        });

    });

        // input
        let inputId = '';

        // set file link
        function fmSetLink($url) {
            document.getElementById(inputId).value = $url;
            $("#imagePreview").attr('src',$url);
        }
    </script>
        
    @endpush
@endsection