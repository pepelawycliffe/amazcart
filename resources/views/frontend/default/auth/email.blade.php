@extends('frontend.default.auth.layouts.app')

@section('content')
<section class="login_area register_part">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-6 col-xl-4">
                <div class="register_form_iner">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <h2>{{ __('defaultTheme.welcome_back') }}, <br>{{ __('defaultTheme.please_send_password_link') }} </h2>
                    <form method="POST" class="register_form" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="email">{{ __('common.email') }}</label>
                                <input type="email" id="email" name="email" placeholder="{{ __('common.email_address') }}" required value="{{ old('email') }}" class="@error('email') is-invalid @enderror"
                                    onfocus="this.placeholder = ''"
                                    onblur="this.placeholder = ''">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>



                            <div class="col-md-12 text-center">
                                <div class="register_area">
                                    <button type="submit" class="btn_1">{{ __('defaultTheme.send_link') }}</button>
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
