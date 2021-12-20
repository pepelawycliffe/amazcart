<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Amazcart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{asset(asset_path(app('general_setting')->favicon))}}" type="image/png">
    <!-- Bootstrap CSS -->

    @if(isRtl())
        <link rel="stylesheet" href="{{ asset(asset_path('backend/css/rtl/bootstrap.rtl.min.css')) }}" />
    @else
        <link rel="stylesheet" href="{{ asset(asset_path('backend/vendors/css/bootstrap.min.css')) }}" />
    @endif

    <!-- themefy CSS -->
    <link rel="stylesheet" href="{{asset(asset_path('backend/vendors/themefy_icon/themify-icons.css'))}}" />
    <!-- swiper slider CSS -->
    <link rel="stylesheet" href="{{ asset(asset_path('frontend/default/vendors/swiper_slider/css/swiper.min.css')) }}" />
    <!-- select2 CSS -->
    <link rel="stylesheet" href="{{asset(asset_path('backend/vendors/select2/css/select2.min.css'))}}" />
    <!-- select2 CSS -->
    <link rel="stylesheet" href="{{ asset(asset_path('backend/vendors/css/nice-select.css')) }}" />
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="{{ asset(asset_path('frontend/default/vendors/owl_carousel/css/owl.carousel.css')) }}" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="{{ asset(asset_path('backend/vendors/font_awesome/css/all.min.css')) }}" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="{{ asset(asset_path('frontend/default/css/jquery.scrollbar.css')) }}" />
    <link rel="stylesheet" href="{{asset(asset_path('backend/vendors/css/toastr.min.css'))}}" />
    <!-- style CSS -->
    
    <link rel="stylesheet" href="{{ asset(asset_path('frontend/default/css/frontend_style.css')) }}" />

    @section('styles')
     @show
    <!-- jquery -->
    <script src="{{ asset(asset_path('backend/vendors/js/jquery-3.6.0.min.js')) }}"></script>



    <style>
        .text-danger {
            color: #dc3545 !important;
        }
        .toast-success {
            background-color: {{ $themeColor->success_color }}!important;
        }

        .toast-error{
            background-color: {{ $themeColor->danger_color }}!important;
        }
        .toast-warning{
            background-color: {{ $themeColor->warning_color }}!important;
        }
    </style>

</head>
