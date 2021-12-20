@extends('frontend.default.auth.layouts.app')
@section('styles')
    <style>

    </style>
@endsection
@section('content')
<section class="register_part">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6">
                <div class="register_form_iner">
                    <h2>{{ __('common.welcome') }}! {{ __('common.please') }} <br>{{ __('defaultTheme.create_your_merchant_account') }}</h2>
                    <form id="registerForm" action="{{route('frontend.merchant.store')}}" method="POST" class="register_form">
                        @csrf
                        <div class="form-row">

                            @if (session()->has('pricing_id'))
                                <div class="col-md-6">
                                    <label for="Shop">{{ __('defaultTheme.subscription_type') }} <span class="text-danger">*</span></label>
                                    <select name="subscription_type" class="nc_select" disabled>
                                        @foreach ($pricing_plans as $pricing_plan)
                                            <option value="{{ $pricing_plan->id }}" @if (session()->get('pricing_id') == $pricing_plan->id) selected @endif>{{ $pricing_plan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <label for="Shop">{{ __('common.shop_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="Shop" value="{{old('name')}}" placeholder="{{ __('common.shop_name') }}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">
                                @error('name')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email">{{ __('common.email_address') }} <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" value="{{old('email')}}" placeholder="{{ __('common.email_address') }} " onfocus="this.placeholder = ''"
                                onblur="this.placeholder = 'Enter email address'">
                                @error('email')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone">{{ __('common.phone_number') }} <span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="phone" value="{{old('phone')}}" placeholder="{{ __('common.phone_number') }} " onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">
                                @error('phone')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password">{{ __('common.password') }} <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" value="{{old('password')}}" placeholder="{{ __('common.password') }} " onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">
                                @error('password')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="re_password">{{ __('common.confirm_password') }}<span class="text-danger">*</span></label>
                                <input type="password" id="re_password" name="password_confirmation" placeholder="{{ __('common.confirm_password') }}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">

                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label class="cs_checkbox">
                                        <input type="checkbox" id="termCheck" checked value="1">
                                        <span class="checkmark"></span>
                                    </label>
                                    <p>{{ __('defaultTheme.by_signing_up_you_agree_to_terms_of_service_and_privacy_policy') }}</p>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="register_area">
                                    <button type="submit" id="submitBtn" class="btn_1">{{ __('defaultTheme.register') }}</button>
                                    <p>{{ __('defaultTheme.already_a_merchant') }}<a href="{{route('login')}}">{{ __('defaultTheme.login_account') }}</a> {{ __('common.here') }}.</p>
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

@push('scripts')
<script>
    (function($){
        "use strict";
        $(document).ready(function(){
            $(document).on('click','#termCheck',function(event){

                if($("#termCheck").prop('checked') == true){
                    //do something
                    $('#submitBtn').prop('disabled', false);
                }else{
                    $('#submitBtn').prop('disabled', true);
                }

            });
        });
    })(jQuery);
</script>
@endpush
