@extends('frontend.default.auth.layouts.app')

@section('content')
<section class="register_part">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6">
                <div class="register_form_iner">
                    <h2>{{ __('common.welcome') }}! {{ __('common.please') }} <br>{{ __('defaultTheme.verify_your_email') }}.</h2>
                    <form id="registerForm" action="{{route('frontend.resend-link',$user->id)}}}" method="POST" class="register_form">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 text-center">
                                <p>{{ __('defaultTheme.before_proceeding_please_check_your_email_for_a_varification_link_if_you_din_not_get_the_email') }}.</p>
                            </div>
                            <input type="hidden" name="verify_code" value="{{$user->verify_code}}">
                            <div class="col-md-12 text-center">
                                <div class="register_area">
                                    <button type="submit" id="submitBtn" class="btn_1">{{ __('defaultTheme.click_here_to_request_another') }}</button>

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


