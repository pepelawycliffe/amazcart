@extends('backEnd.master')

@section('mainContent')

<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-6">
                <div class="white_box_30px">
                    <form action="{{ route('review.review_configuration.update') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="box_header">
                                            <div class="main-title d-flex">
                                                <h3 class="mb-0 mr-30">{{ __('wallet.auto_approve') }} {{ __('review.review') }} {{ __('common.configuration') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">{{ __('wallet.auto_approve') }} {{ __('common.product') }} {{ __('review.review') }}  &nbsp; &nbsp;</label>
                                            <ul id="theme_nav" class="permission_list sms_list ">
                                                <li>
                                                    
                                                    <label data-id="bg_option"
                                                           class="primary_checkbox d-flex mr-12">
                                                        <input name="product_review_status" id="status_active" value="1" {{$reviewConfiguration->auto_approve_product_review == 1?'checked':''}} class="active" type="radio">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <p>{{ __('common.active') }}</p>
                                                </li>
                                                <li>
                
                                                    <label data-id="color_option"
                                                           class="primary_checkbox d-flex mr-12">
                                                        <input name="product_review_status" value="0" id="status_inactive" {{$reviewConfiguration->auto_approve_product_review == 0?'checked':''}}  class="de_active"
                                                               type="radio">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <p>{{ __('common.inactive') }}</p>
                                                </li>
                                            </ul>
                                            <span class="text-danger" id="status_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">{{ __('wallet.auto_approve') }} @if(isModuleActive('MultiVendor')){{ __('common.seller') }}@else {{__('common.company')}} @endif {{ __('review.review') }}  &nbsp; &nbsp;</label>
                                            <ul id="theme_nav" class="permission_list sms_list ">
                                                <li>
                                                    <label data-id="bg_option"
                                                           class="primary_checkbox d-flex mr-12">
                                                        <input name="seller_review_status" id="status_active" value="1" {{$reviewConfiguration->auto_approve_seller_review == 1?'checked':''}} class="active" type="radio">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <p>{{ __('common.active') }}</p>
                                                </li>
                                                <li>
                                                    <label data-id="color_option"
                                                           class="primary_checkbox d-flex mr-12">
                                                        <input name="seller_review_status" value="0" id="status_inactive" {{$reviewConfiguration->auto_approve_seller_review == 0?'checked':''}}  class="de_active"
                                                               type="radio">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <p>{{ __('common.inactive') }}</p>
                                                </li>
                                            </ul>
                                            <span class="text-danger" id="status_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="submit_btn text-center">
                                            <button class="primary_btn_2" type="submit"> <i class="ti-check"
                                                    dusk="save"></i>{{ __('common.update') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection
