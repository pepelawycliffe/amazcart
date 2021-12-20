@extends('frontend.default.auth.layouts.app')

@section('content')
<section class="login_area register_part">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 col-xl-4">
                <div class="register_form_iner">
                    <h2>{{ __('defaultTheme.welcome_back') }}, <br>{{ __('defaultTheme.please_confirm_with_new_password') }}</h2>
                    <form method="POST" class="register_form" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="email">{{ __('common.email_address') }}</label>
                                <input type="email" id="email" name="email" placeholder="{{ __('common.email_address') }}" value="{{ $email ?? old('email') }}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''" required autofocus autocomplete="email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password">{{ __('common.password') }}</label>
                                <input type="password" id="password" class="@error('password') is-invalid @enderror" name="password" required placeholder="{{ __('common.password') }}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''" autocomplete="new-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password-confirm">{{ __('common.confirm_password') }}</label>
                                <input type="password" id="password-confirm" name="password_confirmation" required placeholder="{{ __('common.confirm_password') }}" onfocus="this.placeholder = ''"
                                onblur="this.placeholder = ''" autocomplete="new-password">

                            </div>

                            <div class="col-md-12 text-center">
                                <div class="register_area">
                                    <button type="submit" class="btn_1">{{ __('common.reset_password') }}</button>
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
