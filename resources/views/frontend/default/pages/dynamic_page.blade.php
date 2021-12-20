@extends('frontend.default.layouts.app')

@section('breadcrumb')
    @php
        
    $arr = explode(' ',trim($pageData->title));
    @endphp
    {{$pageData->title}}
@endsection

@section('content')

@include('frontend.default.partials._breadcrumb')

<!-- policy part here -->
<section class="policy_part return_part padding_top bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="policy_part_iner">
                    @php echo $pageData->description; @endphp
                </div>
            </div>
        </div>
    </div>
</section>
<!-- policy part end -->

@endsection
