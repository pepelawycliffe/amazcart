
<form class="" action="{{ route('sms_gateway_credentials_update') }}" method="post">
    @csrf
    <div class="main-title mb-20">
        <h3 class="mb-0">{{__('general_settings.sms_settings')}}</h3>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <label class="primary_input_label" for="">{{ __('general_settings.activate_sms_gateway') }}</label>
            <ul id="" class="permission_list sms_list">
                @foreach ($sms_gateways as $key => $smsGateway)
                <li>
                    <label class="primary_checkbox d-flex mr-12 ">
                        <input name="sms_gateway_id" type="radio" id="sms_gateway_id{{ $key }}" value="{{ $smsGateway->id }}" @if ($smsGateway->status != 0) checked @endif>
                        <span class="checkmark"></span>
                    </label>
                    <p>{{ $smsGateway->type }}</p>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <label class="primary_input_label" for="">{{ __('general_settings.gateway_settings') }}</label>
            <ul id="sms_setting" class="permission_list sms_list mb-50 ">
                <li>
                    <label  data-id="Twilio_Settings" class="primary_checkbox d-flex mr-12 ">
                        <input name="sms1" type="radio" checked="checked">
                        <span class="checkmark"></span>
                    </label>
                    <p>{{__('general_settings.twilio')}}</p>
                </li>
                <li>
                    <label data-id="TexttoLocal_Settings" class="primary_checkbox d-flex mr-12">
                        <input name="sms1" type="radio">
                        <span class="checkmark"></span>
                    </label>
                    <p>{{__('general_settings.textlocal')}}</p>
                </li>
            </ul>
            <div id="Twilio_Settings" class="sms_ption" >
                <!-- content  -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="primary_input mb-25">
                            <input type="hidden" name="types[]" value="TWILIO_SID">
                            <label class="primary_input_label" for="">{{ __('general_settings.twilio_account_sid') }} <span class="text-danger">*</span></label>
                            <input class="primary_input_field" placeholder="-" type="text" name="TWILIO_SID" value="{{ env('TWILIO_SID') }}">
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="primary_input mb-25">
                            <input type="hidden" name="types[]" value="TWILIO_TOKEN">
                            <label class="primary_input_label" for="">{{ __('general_settings.authentication_token') }} <span class="text-danger">*</span></label>
                            <input class="primary_input_field" placeholder="-" type="text" name="TWILIO_TOKEN" value="{{ env('TWILIO_TOKEN') }}">
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="primary_input mb-25">
                            <input type="hidden" name="types[]" value="VALID_TWILLO_NUMBER">
                            <label class="primary_input_label" for="">{{ __('general_settings.registered_phone_number') }} <span class="text-danger">*</span></label>
                            <input class="primary_input_field" placeholder="-" type="text" name="VALID_TWILLO_NUMBER" value="{{ env('VALID_TWILLO_NUMBER') }}">
                        </div>
                    </div>
                </div>
                <div class="submit_btn text-center mb-100 pt_15">
                    <button class="primary_btn_large" type="submit"> <i class="ti-check"></i> {{ __('common.save') }}</button>
                </div>
                <!-- content  -->
            </div>
            <div id="TexttoLocal_Settings" class="sms_ption" style="display: none;">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="primary_input mb-25">
                            <input type="hidden" name="types[]" value="TEXT_TO_LOCAL_API_KEY">
                            <label class="primary_input_label" for="">{{ __('general_settings.api_key') }} <span class="text-danger">*</span></label>
                            <input class="primary_input_field" placeholder="-" type="text" name="TEXT_TO_LOCAL_API_KEY" value="{{ env('TEXT_TO_LOCAL_API_KEY') }}">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="primary_input mb-25">
                            <input type="hidden" name="types[]" value="TEXT_TO_LOCAL_SENDER">
                            <label class="primary_input_label" for="">{{ __('general_settings.sender_name') }} <span class="text-danger">*</span></label>
                            <input class="primary_input_field" placeholder="-" type="text" name="TEXT_TO_LOCAL_SENDER" value="{{ env('TEXT_TO_LOCAL_SENDER') }}">
                        </div>
                    </div>
                </div>
                @if (permissionCheck('sms_gateway_credentials_update'))
                    <div class="submit_btn text-center pt_15">
                        <button class="primary_btn_large" type="submit"> <i class="ti-check"></i> {{ __('common.save') }}</button>
                    </div>
                @else
                    <div class="col-lg-12 text-center mt-2">
                        <span class="alert alert-warning" role="alert">
                            <strong>You don't have this permission</strong>
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>
<hr>
<form class="" action="{{ route('sms_send_demo') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-xl-12">
            <div class="primary_input mb-25">
                <label class="primary_input_label" for="">{{ __('common.phone_number') }}</label>
                <input class="primary_input_field" placeholder="-" value="{{old('number')}}" type="text" name="number">
                <span class="text-danger">{{$errors->first('number')}}</span>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="primary_input mb-25">
                <label class="primary_input_label" for="">{{ __('general_settings.send_a_test_sms') }}</label>
                <input class="primary_input_field" placeholder="-" value="{{old('message')}}" type="text" name="message">
                <span class="text-danger">{{$errors->first('message')}}</span>
            </div>
        </div>
    </div>
    <div class="submit_btn text-center mb-100 pt_15">
        <button class="primary_btn_2" type="submit">{{ __('general_settings.send_test_sms') }}</button>
    </div>
</form>
