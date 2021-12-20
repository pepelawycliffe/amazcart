@extends('frontend.default.auth.layouts.app')
@section('styles')
    <style>
        .login_logo img {
            max-width: 140px;
            margin: 0 auto;
        }
        .register_part {
            background: var(--background_color) !important;
            min-height: 100vh !important;
            height: auto !important;
        }
    </style>
@endsection
@section('content')
<section class="register_part">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6">
                <div class="register_form_iner">
                    <div class="login_logo text-center mb-3">
                        <img src="{{asset(asset_path(app('general_setting')->logo))}}" alt="">
                    </div>
                    <h2>{{ __('defaultTheme.welcome') }}! <br>{{ __('defaultTheme.please_create_your_account') }}</h2>
                    <form method="POST" action="{{ route('register') }}" class="register_form" name="register">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6">
                                <label for="name">{{__('common.first_name')}} <span class="text-danger">*</span></label>
                                <input type="text" id="first_name" class="@error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" placeholder="{{__('common.first_name')}}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">
                                @error('first_name')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="name">{{__('common.last_name')}}</label>
                                <input type="text" id="last_name" class="@error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="{{__('common.last_name')}}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">
                                @error('last_name')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email">{{__('common.email_or_phone')}} <span class="text-danger">*</span></label>
                                <input type="text" id="email" name="email" value="{{old('email')}}" placeholder="{{__('common.email_or_phone')}}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">
                                @error('email')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="referral_code">{{__('common.referral_code_(optional)')}}</label>
                                <input type="text" id="referral_code" name="referral_code" value="{{old('referral_code')}}" placeholder="{{__('common.referral_code')}}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''">
                                @error('referral_code')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password">{{__('common.password')}}({{ __('defaultTheme.minimum_8') }})<span class="text-danger">*</span></label>
                                <input type="password" id="password" class="@error('password') is-invalid @enderror" name="password" placeholder="{{__('common.password')}}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''" autocomplete="new-password">
                                @error('password')
                                <span class="text-danger" >{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password-confirm">{{__('common.confirm_password')}} <span class="text-danger">*</span></label>
                                <input type="password" id="password-confirm" name="password_confirmation" placeholder="{{__('common.confirm_password')}}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''" autocomplete="new-password">

                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label class="cs_checkbox">
                                        <input id="policyCheck" type="checkbox" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <p>{{ __('defaultTheme.by_signing_up_you_agree_to_terms_of_service_and_privacy_policy') }}</p>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="register_area">
                                    <button type="submit" id="submitBtn" class="btn_1 cs-pointer">{{ __('defaultTheme.register') }}</button>
                                    <p>
                                        {{ __('defaultTheme.already_a_member_yet') }}
                                        <a href="{{url('/login')}}">{{ __('defaultTheme.login_account') }}</a> {{ __('common.here') }}.</p>
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

            $(document).on('submit', '.register_form', function(event){

                if($("#policyCheck").prop('checked')!=true){
                    event.preventDefault();
                    toastr.error("{{__('common.please_agree_with_our_policy_privacy')}}","{{__('common.error')}}");
                    return false;
                }

            });

        });
    })(jQuery);
</script>
@endpush
