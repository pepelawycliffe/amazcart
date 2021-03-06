@extends('backEnd.master')
@section('styles')
<link rel="stylesheet" href="{{ asset(asset_path('backend/css/backend_style.css')) }}" />
<link rel="stylesheet" href="{{ asset(asset_path('backend/vendors/vendors_style.css')) }}" />
@endsection
@section('mainContent')
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="box_header common_table_header">
                    <div class="main-title d-md-flex">
                        <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('appearance.add_new_color') }}</h3>
                        <ul class="d-flex">
                            <li><a class="primary-btn radius_30px mr-10 fix-gr-bg text-white"
                                    href="{{route('appearance.color.index')}}" dusk="Add New"><i
                                        class="ti-list"></i>{{__('common.dashboard')}}  {{ __('appearance.color') }} {{__('common.list')}}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="white_box_50px box_shadow_white pb-3">
                    <form class="" action="{{ route('appearance.color.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="title">{{__('common.title')}} <span
                                            class="text-danger">*</span></label>
                                    <input required type="text" id="title" class="primary_input_field" name="title"
                                        autocomplete="off" value="" placeholder="{{__('common.title')}} ">
                                    @error('title')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="title">{{__('appearance.color_mode')}} <span
                                            class="text-danger">*</span></label>
                                    <select required class="primary_select mb-15" name="color_mode" id="color_mode">
                                        <option value="gradient">{{ __('appearance.gradient') }}</option>
                                        <option value="solid">{{ __('appearance.solid') }}</option>
                                    </select>
                                    @error('color_mode')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="title">{{__('appearance.background')}}
                                        {{__('common.type')}} <span class="text-danger">*</span></label>
                                    <select id="background-type" required class="primary_select mb-15"
                                        name="background_type" id="">
                                        <option value="color">{{ __('appearance.color') }}</option>
                                        <option value="image">{{ __('common.image') }} (1920X1400)</option>
                                    </select>
                                    @error('background_type')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div id="div-color" class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="background_color">{{__('marketing.background_color')}} <span
                                            class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="background_color"
                                        class="form-control" name="background_color" autocomplete="off" value="#E2DADA">
                                    @error('background_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div style="display: none" id="div-image" class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="">{{__('appearance.background')}}
                                        {{__('common.image')}}</label>
                                    <div class="primary_file_uploader">
                                        <input class="primary-input" type="text" id="backGroundImagePlaceholder"
                                            placeholder="{{__('common.browse_image_file')}}" readonly="">
                                        <button class="" type="button">
                                            <label class="primary-btn small fix-gr-bg"
                                                for="meta_image">{{ __('common.image') }} </label>
                                            <input type="file" class="d-none" name="background_image" id="meta_image">
                                        </button>
                                    </div>
                                    @error('background_image')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="base_color">{{__('appearance.base_color')}}
                                        <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="base_color"
                                        class="form-control" name="base_color" autocomplete="off" value="#415094">
                                    @error('base_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div style="display: none" id="div-solid" class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="solid_color">{{__('appearance.solid')}}
                                        {{__('appearance.color')}}
                                        <span class="text-danger">*</span></label>
                                    <input class="primary_input_field" type="color" id="solid_color"
                                        class="form-control" name="solid_color" autocomplete="off" value="#415094">
                                    @error('solid_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3" id="div-gradient-1">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="gradient_color_one">{{__('appearance.gradient')}}
                                        {{ __('appearance.color') }} 1 <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="gradient_1"
                                        class="form-control" name="gradient_color_one" autocomplete="off"
                                        value="#7C32FF">
                                    @error('gradient_color_one')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3" id="div-gradient-2">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="gradient_color_two">{{__('appearance.gradient')}}
                                        {{ __('appearance.color') }} 2 <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="gradient_2"
                                        class="form-control" name="gradient_color_two" autocomplete="off"
                                        value="#A235EC">
                                    @error('gradient_color_two')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3" id="div-gradient-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="gradient_color_three">{{__('appearance.gradient')}}
                                        {{ __('appearance.color') }} 3 <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="gradient_3"
                                        class="form-control" name="gradient_color_three" autocomplete="off"
                                        value="#C738D8">
                                    @error('gradient_color_three')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="scroll_color">{{__('appearance.scroll')}}
                                        {{__('appearance.color')}} <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="scroll_color"
                                        class="form-control" name="scroll_color" autocomplete="off" value="#7E7172">
                                    @error('scroll_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="text_color">{{__('appearance.text')}}
                                        {{__('appearance.color')}}
                                        <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="text_color"
                                        class="form-control" name="text_color" autocomplete="off" value="#828BB2">
                                    @error('text_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="text_white">{{__('appearance.text')}}
                                        {{__('appearance.white')}}
                                        <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="text_white"
                                        class="form-control" name="text_white" autocomplete="off" value="#FFFFFF">
                                    @error('text_white')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="background_white">{{__('appearance.background')}}
                                        {{__('appearance.white')}} <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="background_white"
                                        class="form-control" name="background_white" autocomplete="off" value="#FFFFFF">
                                    @error('background_white')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="text_black">{{__('appearance.text')}}
                                        {{__('appearance.black')}}
                                        <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="text_black"
                                        class="form-control" name="text_black" autocomplete="off" value="#000000">
                                    @error('text_black')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="background_black">{{__('appearance.background')}}
                                        {{__('appearance.black')}} <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="background_black"
                                        class="form-control" name="background_black" autocomplete="off" value="#000000">
                                    @error('background_black')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="border_color">{{__('appearance.border')}}
                                        {{__('appearance.color')}} <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="border_color"
                                        class="form-control" name="border_color" autocomplete="off" value="#FFFFFF">
                                    @error('border_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="input_background">{{__('appearance.input_background')}} <span
                                            class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="input_background"
                                        class="form-control" name="input_background" autocomplete="off" value="#FFFFFF">
                                    @error('input_background')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="success_color">{{__('common.success')}}
                                        {{__('appearance.color')}} <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="success_color"
                                        class="form-control" name="success_color" autocomplete="off" value="#4BCF90">
                                    @error('success_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="warning_color">{{__('common.warning')}}
                                        {{__('appearance.color')}} <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="warning_color"
                                        class="form-control" name="warning_color" autocomplete="off" value="#E09079">
                                    @error('warning_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label" for="danger_color">{{__('common.danger')}}
                                        {{__('appearance.color')}} <span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="color" id="danger_color"
                                        class="form-control" name="danger_color" autocomplete="off" value="#FF6D68">
                                    @error('danger_color')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="title">{{__('appearance.toastr_message_postion')}} <span
                                            class="text-danger">*</span></label>
                                    <select required class="primary_select mb-15" name="toastr_position"
                                        id="toastr_position">
                                        <option value="toast-top-right">{{ __('appearance.top_right') }}</option>
                                        <option value="toast-top-left">{{ __('appearance.top_left') }}</option>
                                        <option value="toast-bottom-right">{{ __('appearance.bottom_right') }}</option>
                                        <option value="toast-bottom-left">{{ __('appearance.bottom_left') }}</option>
                                    </select>
                                    @error('toastr_position')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="primary_input mb-25">
                                    <label class="primary_input_label"
                                        for="toastr_time">{{__('appearance.toastr_message_time')}}
                                        ({{__('common.second')}})<span class="text-danger">*</span></label>
                                    <input required class="primary_input_field" type="number" id="toastr_time"
                                        class="form-control" name="toastr_time" autocomplete="off" value="3" step=".01">
                                    @error('toastr_time')
                                    <span class="text-danger" id="error_background_color">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="primary_input">
                                <button type="submit" class="primary-btn fix-gr-bg" id="save_button_parent"><i
                                        class="ti-plus"></i>{{ __('common.add') }}</button>
                            </div>
                        </div>

                    </form>
                    <input type="hidden" id="old_bg_image" value="{{ asset(asset_path('backend/img/body-bg.jpg')) }}">
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('scripts')
@include('appearance::color.components.script')


@endpush
