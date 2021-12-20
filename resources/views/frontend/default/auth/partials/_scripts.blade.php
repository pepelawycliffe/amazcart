
<!-- popper js -->
<script src="{{asset(asset_path('backend/vendors/js/popper.js'))}}"></script>
<!-- bootstarp js -->
@if(isRtl())
<script src="{{asset(asset_path('backend/js/bootstrap.rtl.min.js')) }}"></script>
@else
<script src="{{asset(asset_path('backend/js/bootstrap.min.js')) }}"></script>
@endif
<!-- jquery ui js -->
<script src="{{ asset(asset_path('frontend/default/js/jquery.simpleLoadMore.js')) }}"></script>
<!-- swiper slider js -->
<script src="{{ asset(asset_path('frontend/default/vendors/swiper_slider/js/swiper.min.js')) }}"></script>
<!-- select2 js -->
<script src="{{asset(asset_path('backend/vendors/select2/js/select2.min.js'))}}"></script>
<!-- nice select -->
<script src="{{asset(asset_path('backend/vendors/js/nice-select.min.js'))}}"></script>
<!-- owl carousel -->
<script src="{{ asset(asset_path('frontend/default/vendors/owl_carousel/js/owl.carousel.min.js')) }}"></script>
<!-- load more -->
<script src="{{ asset(asset_path('frontend/default/js/jquery.nicescroll.min.js')) }}"></script>
<!-- load more -->
<script src="{{ asset(asset_path('frontend/default/js/jquery.scrollbar.min.js')) }}"></script>

<script type="text/javascript" src="{{asset(asset_path('backend/vendors/js/toastr.min.js'))}}"></script>
@php echo Toastr::message(); @endphp
<script>
    @if(Session::has('messege'))
    let type = "{{Session::get('alert-type','info')}}";
    switch(type){
        case 'info':
            toastr.info("{{ Session::get('messege') }}");
            break;
        case 'success':
            toastr.success("{{ Session::get('messege') }}");
            break;
        case 'warning':
            toastr.warning("{{ Session::get('messege') }}");
            break;
        case 'error':
            toastr.error("{{ Session::get('messege') }}");
            break;
    }
    @endif
</script>
@stack('scripts')
