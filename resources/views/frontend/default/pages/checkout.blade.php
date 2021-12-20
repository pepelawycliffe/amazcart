@extends('frontend.default.layouts.app')

@section('breadcrumb')
{{ __('order.checkout') }}
@endsection

@section('styles')
<link rel="stylesheet" href="{{asset(asset_path('frontend/default/css/page_css/checkout.css'))}}" />

<style>
    .form-control {
        border-color: var(--border_color);
        font-size: 13px;
        font-weight: 300;
        border-radius: 0;
        height: 50px;
        padding: 10px 20px;
    }
</style>

@endsection

@section('content')

@include('frontend.default.partials._breadcrumb')
<!-- checkout form part -->
<section class="checkout_form padding_top">
    <div class="container">
        <div id="mainDiv">
            @include('frontend.default.partials._checkout_details')
        </div>
    </div>
    @include('frontend.default.partials._delete_modal_for_ajax',['item_name' => __('common.checkout_product'),'modal_id' =>
    'deleteProductModal',
    'form_id' => 'product_delete_form','delete_item_id' => 'delete_product_id','dataDeleteBtn' =>'productDeleteBtn'])
</section>
@if (auth()->check())
@include('frontend.default.partials._customer_address_modal')
@endif
@endsection

@push('scripts')
<script>
    (function($){
           "use strict";

           $(document).ready(function(){
                $(".stripe-button-el").remove();
                $(".razorpay-payment-button").hide();
                gateway_btn_hide();
                form_submit();
                $(document).on('click','#modal_add_new',function(event){
                    $('#new_address_form_div').show();
                    $('#list_div').hide();
                    $('.modal-header').empty();
                    $('.modal-header').append(
                        `<h5 class="modal-title" id="exampleModalLabel">Add new Address</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>`
                    );
                });


                $(document).on('change', '#address_country', function(event){
                    let country = $('#address_country').val();
                    $('#pre-loader').show();
                    if(country){
                        let base_url = $('#url').val();
                        let url = base_url + '/seller/profile/get-state?country_id=' +country;

                        $('#address_state').empty();

                        $('#address_state').append(
                            `<option value="">Select from options</option>`
                        );
                        $('#address_state').niceSelect('update');
                        $('#address_city').empty();
                        $('#address_city').append(
                            `<option value="">Select from options</option>`
                        );
                        $('#address_city').niceSelect('update');
                        $.get(url, function(data){

                            $.each(data, function(index, stateObj) {
                                $('#address_state').append('<option value="'+ stateObj.id +'">'+ stateObj.name +'</option>');
                            });

                            $('#address_state').niceSelect('update');
                            $('#pre-loader').hide();
                        });
                    }
                });

                $(document).on('change', '#shipping_country', function(event){
                    let country = $('#shipping_country').val();
                    $('#pre-loader').show();
                    if(country){
                        let base_url = $('#url').val();
                        let url = base_url + '/seller/profile/get-state?country_id=' +country;

                        $('#shipping_state').empty();

                        $('#shipping_state').append(
                            `<option value="">Select from options</option>`
                        );
                        $('#shipping_state').niceSelect('update');
                        $('#shipping_city').empty();
                        $('#shipping_city').append(
                            `<option value="">Select from options</option>`
                        );
                        $('#shipping_city').niceSelect('update');
                        $.get(url, function(data){

                            $.each(data, function(index, stateObj) {
                                $('#shipping_state').append('<option value="'+ stateObj.id +'">'+ stateObj.name +'</option>');
                            });

                            $('#shipping_state').niceSelect('update');
                            $('#pre-loader').hide();
                        });
                    }
                });

                $(document).on('change', '#billing_country', function(event){
                    let country = $('#billing_country').val();
                    $('#pre-loader').show();
                    if(country){
                        let base_url = $('#url').val();
                        let url = base_url + '/seller/profile/get-state?country_id=' +country;

                        $('#billing_state').empty();

                        $('#billing_state').append(
                            `<option value="">Select from options</option>`
                        );
                        $('#billing_state').niceSelect('update');
                        $('#billing_city').empty();
                        $('#billing_city').append(
                            `<option value="">Select from options</option>`
                        );
                        $('#billing_city').niceSelect('update');
                        $.get(url, function(data){

                            $.each(data, function(index, stateObj) {
                                $('#billing_state').append('<option value="'+ stateObj.id +'">'+ stateObj.name +'</option>');
                            });

                            $('#billing_state').niceSelect('update');
                            $('#pre-loader').hide();
                        });
                    }
                });

                $(document).on('change', '#address_state', function(event){
                    let state = $('#address_state').val();
                    $('#pre-loader').show();
                    if(state){
                        let base_url = $('#url').val();
                        let url = base_url + '/seller/profile/get-city?state_id=' +state;


                        $('#address_city').empty();
                        $('#address_city').append(
                            `<option value="">Select from options</option>`
                        );
                        $.get(url, function(data){

                            $.each(data, function(index, cityObj) {
                                $('#address_city').append('<option value="'+ cityObj.id +'">'+ cityObj.name +'</option>');
                            });

                            $('#address_city').niceSelect('update');
                            $('#pre-loader').hide();
                        });
                    }
                });

                $(document).on('change', '#shipping_state', function(event){
                    let state = $('#shipping_state').val();
                    $('#pre-loader').show();
                    if(state){
                        let base_url = $('#url').val();
                        let url = base_url + '/seller/profile/get-city?state_id=' +state;


                        $('#shipping_city').empty();
                        $('#shipping_city').append(
                            `<option value="">Select from options</option>`
                        );
                        $.get(url, function(data){

                            $.each(data, function(index, cityObj) {
                                $('#shipping_city').append('<option value="'+ cityObj.id +'">'+ cityObj.name +'</option>');
                            });

                            $('#shipping_city').niceSelect('update');
                            $('#pre-loader').hide();
                        });
                    }
                });

                $(document).on('change', '#billing_state', function(event){
                    let state = $('#billing_state').val();
                    $('#pre-loader').show();
                    if(state){
                        let base_url = $('#url').val();
                        let url = base_url + '/seller/profile/get-city?state_id=' +state;


                        $('#billing_city').empty();
                        $('#billing_city').append(
                            `<option value="">Select from options</option>`
                        );
                        $.get(url, function(data){

                            $.each(data, function(index, cityObj) {
                                $('#billing_city').append('<option value="'+ cityObj.id +'">'+ cityObj.name +'</option>');
                            });

                            $('#billing_city').niceSelect('update');
                            $('#pre-loader').hide();
                        });
                    }
                });


                $(document).on('click','#modal_add_new_billing',function(event){
                    $('#new_address_form_div_billing').show();
                    $('#list_div_billing').hide();
                    $('.modal_header_billing').empty();
                    $('.modal_header_billing').append(
                        `<h5 class="modal-title" id="exampleModalLabel">Add new Address</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>`
                    );
                });

                $(document).on('click','#add_submit_btn',function(event){
                    $('#pre-loader').show();
                    $('#add_submit_btn').prop("disabled", true);
                    let name = $('#address_name').val();
                    let email = $('#address_email').val();
                    let phone_number = $('#address_phone').val();
                    let address = $('#address_address').val();
                    let city = $('#address_city').val();
                    let state = $('#address_state').val();
                    let country = $('#address_country').val();
                    let postal_code = $('#address_postal_code').val();

                    let formData = new FormData();
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('name', name);
                    formData.append('email', email);
                    formData.append('phone_number', phone_number);
                    formData.append('address', address);
                    formData.append('city', city);
                    formData.append('state', state);
                    formData.append('country', country);
                    formData.append('postal_code', postal_code);
                    formData.append('form', 'normal_form');

                    removeError();

                    $.ajax({
                        url: "{{route('frontend.checkout.address.store')}}",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            $('#mainDiv').empty();
                            $('#mainDiv').html(response.MainCheckout);
                            $('#address_list_div').empty();
                            $('#address_list_div').html(response.AddressesShipping);
                            $('#address_list_div_billing').empty();
                            $('#address_list_div_billing').html(response.AddressesBilling);
                            $('#add_submit_btn').prop("disabled", false);
                            $('#pre-loader').hide();
                            location.reload();
                        },
                        error: function (response) {

                            showValidate(response)
                            $('#add_submit_btn').prop("disabled", false);
                            $('#pre-loader').hide();

                        }
                    });

                });

                $(document).on('submit','#new_address_form',function(event){
                    event.preventDefault();
                    $('#new_add_submit_btn').prop("disabled", true);

                    let formElement = $(this).serializeArray()
                    let formData = new FormData();
                    formElement.forEach(element => {
                        formData.append(element.name, element.value);
                    });
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('form', 'modal_form');


                    $.ajax({
                        url: "{{route('frontend.checkout.address.store')}}",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {

                            $('#mainDiv').empty();
                            $('#mainDiv').html(response.MainCheckout);

                            $('#address_list_div').empty();
                            $('#address_list_div').html(response.AddressesShipping);
                            $('#address_list_div_billing').empty();
                            $('#address_list_div_billing').html(response.AddressesBilling);

                            $('#new_add_submit_btn').prop("disabled", false);
                            $('#new_address_form_div').hide();
                            $('#list_div').show();
                            $('.modal-header').empty();
                            $('.modal-header').append(
                                `<h5 class="modal-title" id="exampleModalLabel">Shipping Address</h5>
                                <button type="button" id="modal_add_new" class="transfarent-btn">Add New Address</button>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>`
                            );
                            removeError();
                        },
                        error: function (response) {

                            showValidate(response)
                            $('#new_add_submit_btn').prop("disabled", false);

                        }
                    });

                });

                $(document).on('submit','#new_address_form_billing',function(event){
                    event.preventDefault();
                    $('#new_add_submit_btn_billing').prop("disabled", true);

                    let formElement = $(this).serializeArray()
                    let formData = new FormData();
                    formElement.forEach(element => {
                        formData.append(element.name, element.value);
                    });
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('form', 'modal_form');


                    $.ajax({
                        url: "{{route('frontend.checkout.address.store')}}",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {

                            $('#mainDiv').empty();
                            $('#mainDiv').html(response.MainCheckout);

                            $('#address_list_div_billing').empty();
                            $('#address_list_div_billing').html(response.AddressesBilling);
                            $('#address_list_div').empty();
                            $('#address_list_div').html(response.AddressesShipping);

                            $('#new_add_submit_btn_billing').prop("disabled", false);
                            $('#new_address_form_div_billing').hide();
                            $('#list_div_billing').show();
                            $('.modal_header_billing').empty();
                            $('.modal_header_billing').append(
                                `<h5 class="modal-title" id="exampleModalLabel">Billing Address</h5>
                                <button type="button" id="modal_add_new_billing" class="transfarent-btn">Add New Address</button>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>`
                            );
                            removeError();
                        },
                        error: function (response) {

                            showValidate(response)
                            $('#new_add_submit_btn_billing').prop("disabled", false);

                        }
                    });

                });


                $(document).on('click','#shipping_address_set_btn',function(event){
                    $('#shipping_address_set_btn').prop("disabled", true);
                    let id = $('#shipping_address_id').val();
                    let formData = new FormData();
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('id', id);
                    $.ajax({
                        url: "{{route('frontend.checkout.address.shipping')}}",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            $('#shipping_address_set_btn').prop("disabled", false);

                            $('#mainDiv').empty();
                            $('#mainDiv').html(response.MainCheckout);

                            $('#address_list_div_billing').empty();
                            $('#address_list_div_billing').html(response.AddressesBilling);
                            $('#address_list_div').empty();
                            $('#address_list_div').html(response.AddressesShipping);
                            $('#shipping_address_modal').modal('hide');

                        },
                        error: function (response) {

                            $('#shipping_address_set_btn').prop("disabled", false);

                        }
                    });

                });
                $(document).on('click','#billing_address_set_btn',function(event){
                    $('#billing_address_set_btn').prop("disabled", true);
                    let id = $('#billing_address_id').val();
                    let formData = new FormData();
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('id', id);
                    $.ajax({
                        url: "{{route('frontend.checkout.address.billing')}}",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {
                            $('#shipping_address_set_btn').prop("disabled", false);

                            $('#mainDiv').empty();
                            $('#mainDiv').html(response.MainCheckout);

                            $('#address_list_div_billing').empty();
                            $('#address_list_div_billing').html(response.AddressesBilling);
                            $('#address_list_div').empty();
                            $('#address_list_div').html(response.AddressesShipping);
                            $('#billing_address_modal').modal('hide');

                        },
                        error: function (response) {

                            $('#shipping_address_set_btn').prop("disabled", false);

                        }
                    });

                });

                $(document).on('click','#orderConfirm',function(event){
                    event.preventDefault();
                    toastr.warning("{{__('defaultTheme.your_cart_is_empty_please_select_item_s_before_checkout_also_add_address_first')}}","{{__('common.warning')}}");
                });

                let id_for_delete = null;
                let product_id_for_delete = null;
                let unique_id_for_delete = null;
                let empty_check_for_delete = null;

                $(document).on('click', '.checkoutProductDelete', function(event){
                    event.preventDefault();

                    $('#deleteProductModal').modal('show');
                    id_for_delete = $(this).data('id');
                    product_id_for_delete = $(this).data('product_id');
                    unique_id_for_delete = $(this).data('unique_id');
                    empty_check_for_delete = $(this).data('empty_check');

                });

                $(document).on('click', '#product_delete_form', function(event){
                    event.preventDefault();

                    checkoutProductDelete(id_for_delete, product_id_for_delete,unique_id_for_delete, empty_check_for_delete);
                });


                function checkoutProductDelete(id,p_id,btn_id,empty_check){
                    $(btn_id).prop("disabled", true);
                    $('#pre-loader').show();
                    $('#deleteProductModal').modal('hide');
                    if(empty_check>1){
                        var formData = new FormData();
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('id', id);
                        formData.append('p_id', p_id);

                        var base_url = $('#url').val();
                        $.ajax({
                            url: base_url + "/checkout/item/delete",
                            type: "POST",
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {
                                toastr.success("{{__('defaultTheme.product_successfully_deleted_from_cart')}}","{{__('common.success')}}");
                                $('#mainDiv').empty();
                                $('#mainDiv').html(response.MainCheckout);
                                $(btn_id).prop("disabled", false);
                                $('#pre-loader').hide();

                            },
                            error: function (response) {

                                $(btn_id).prop("disabled", false);
                                $('#pre-loader').hide();

                            }
                        });
                    }else{
                        toastr.warning("{{__('defaultTheme.your_cart_is_empty_please_select_item_s_before_checkout')}}","{{__('common.warning')}}");
                        $(btn_id).prop("disabled", false);
                        $('#pre-loader').hide();
                    }

                }

                $(document).on('click', '.showShippingModal', function(event){
                    showShippingModal();
                });

                function showShippingModal(){
                    $('#list_div').show();
                    $('#new_address_form_div').hide();
                    $('.modal-header').empty();
                    $('.modal-header').append(
                        `<h5 class="modal-title" id="exampleModalLabel">Shipping Address</h5>
                        <button type="button" id="modal_add_new" class="transfarent-btn">Add New Address</button>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>`
                    );
                }

                $(document).on('click', '.showBillingModal', function(event){
                    showBillingModal();
                });

                function showBillingModal(){
                    $('#list_div_billing').show();
                    $('#new_address_form_div_billing').hide();
                    $('.modal_header_billing').empty();
                    $('.modal_header_billing').append(
                        `<h5 class="modal-title" id="exampleModalLabel">Billing Address</h5>
                        <button type="button" id="modal_add_new_billing" class="transfarent-btn">Add New Address</button>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>`
                    );
                }

                $(document).on('click', '.email_hide_how', function(event){
                    event.preventDefault();
                    $('#old_customer_email_div').addClass('d-none');
                    $('#new_customer_email_div').removeClass('d-none');
                });

                $(document).on('click', '#customer_email_new_btn', function(event){
                    event.preventDefault();
                    let id = $(this).data('id');
                    customerEmailChange(id);
                });

                function customerEmailChange(id){
                    $('#customer_email_new_btn').prop("disabled", true);
                    $('#pre-loader').show();
                    let email = $('#customer_email_new').val();
                    let formData = new FormData();

                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('email', email);
                    formData.append('id', id);


                    $.ajax({
                        url: "{{route('frontend.checkout.email.change')}}",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {

                            $('#mainDiv').empty();
                            $('#mainDiv').html(response.MainCheckout);

                            $('#address_list_div_billing').empty();
                            $('#address_list_div_billing').html(response.AddressesBilling);
                            $('#address_list_div').empty();
                            $('#address_list_div').html(response.AddressesShipping);
                            $('#customer_email_new_btn').prop("disabled", false);
                            $('#pre-loader').hide();

                        },
                        error: function (response) {
                            $('#customer_email_new_btn').prop("disabled", false);
                            toastr.error(response.responseJSON.errors.email);
                            $('#pre-loader').hide();
                        }
                    });
                }

                $(document).on('click', '.phone_hide_show', function(event){
                    event.preventDefault();
                    $('#old_customer_phone_div').addClass('d-none');
                    $('#new_customer_phone_div').removeClass('d-none');
                });

                $(document).on('click', '#customer_phone_new_btn', function(event){
                    event.preventDefault();
                    let id = $(this).data('id');
                    customerPhoneChange(id);
                });

                function customerPhoneChange(id){
                    $('#customer_phone_new_btn').prop("disabled", true);
                    $('#pre-loader').show();
                    let email = $('#customer_phone_new').val();
                    let formData = new FormData();

                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('phone', email);
                    formData.append('id', id);


                    $.ajax({
                        url: "{{route('frontend.checkout.phone.change')}}",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (response) {

                            $('#mainDiv').empty();
                            $('#mainDiv').html(response.MainCheckout);

                            $('#address_list_div_billing').empty();
                            $('#address_list_div_billing').html(response.AddressesBilling);
                            $('#address_list_div').empty();
                            $('#address_list_div').html(response.AddressesShipping);
                            $('#customer_phone_new_btn').prop("disabled", false);
                            $('#pre-loader').hide();

                        },
                        error: function (response) {

                            $('#customer_phone_new_btn').prop("disabled", false);
                            toastr.error(response.responseJSON.errors.phone);
                            $('#pre-loader').hide();

                        }
                    });

                }

                $(document).on('click', '.coupon_apply_btn', function(event){
                    event.preventDefault();
                    let total = $(this).data('total');
                    couponApply(total);
                });

                function couponApply(total){
                    let coupon_code = $('#coupon_code').val();
                    if(coupon_code){
                        $('#pre-loader').show();

                        let formData = new FormData();
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('coupon_code', coupon_code);
                        formData.append('shopping_amount', total);
                        $.ajax({
                            url: '{{route('frontend.checkout.coupon-apply')}}',
                            type: "POST",
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response) {

                                if(response.error){
                                    toastr.error(response.error,'Error');
                                    $('#pre-loader').hide();
                                }else{
                                    $('#mainDiv').empty();
                                    $('#mainDiv').html(response.MainCheckout);
                                    toastr.success("{{__('defaultTheme.coupon_applied_successfully')}}","{{__('common.success')}}");
                                    $('#pre-loader').hide();
                                }


                            },
                            error: function (response) {
                                toastr.error(response.responseJSON.errors.coupon_code)
                                $('#pre-loader').hide();
                            }
                        });
                    }else{
                        toastr.error("{{__('defaultTheme.coupon_field_is_required')}}","{{__('common.error')}}");
                    }
                }

                $(document).on('click', '#coupon_delete', function(event){
                    event.preventDefault();
                    couponDelete();
                });

                function couponDelete(){
                    $('#pre-loader').show();
                    let base_url = $('#url').val();
                    let url = base_url + '/checkout/coupon-delete';
                    $.get(url, function(response) {
                        $('#mainDiv').empty();
                        $('#mainDiv').html(response.MainCheckout);
                        $('#pre-loader').hide();
                        toastr.success("{{__('defaultTheme.coupon_deleted_successfully')}}","{{__('common.success')}}");
                    });
                }

                function showValidate(response){
                    $('.new_address_name').text(response.responseJSON.errors.name)
                    $('.new_address_email').text(response.responseJSON.errors.email)
                    $('.new_address_phone').text(response.responseJSON.errors.phone_number)
                    $('.new_address_address').text(response.responseJSON.errors.address)
                    $('.new_address_city').text(response.responseJSON.errors.city)
                    $('.new_address_state').text(response.responseJSON.errors.state)
                    $('.new_address_country').text(response.responseJSON.errors.country)
                    $('.new_address_postal_code').text(response.responseJSON.errors.postal_code)
                }

                function removeError(){
                    $('.new_address_name').text('')
                    $('.new_address_email').text('')
                    $('.new_address_phone').text('')
                    $('.new_address_address').text('')
                    $('.new_address_city').text('')
                    $('.new_address_state').text('')
                    $('.new_address_country').text('')
                    $('.new_address_postal_code').text('')
                }

                function gateway_btn_hide(){
                    $(".paypal_form_payment_23").addClass('d-none');
                    $("#paystack_form").addClass('d-none');
                    $("#razor_form").addClass('d-none');
                    $("#stripe_form").addClass('d-none');
                    $(".regular_order_btn").removeClass('d-none');
                }

                function form_submit(){
                    var payment_id = $("#payment_id").val();
                    if (payment_id != 0) {
                        $(".regular_order_btn").prop('disabled', true);
                        $("#mainOrderForm").submit();
                    }
                }


                $(document).on('change', 'input[type=radio][name=payment_method]', function(event){
                    if (this.value == 2) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        if (parseFloat($("#grand_total").val()) > parseFloat($("#wallet_amount").val())) {
                            toastr.warning("{{__('defaultTheme.wallet_balance_is_not_enough_to_pay_for_this_order')}}","{{__('common.warning')}}");
                            $(".regular_order_btn").prop('disabled', true);

                        }else {
                            $(".regular_order_btn").prop('disabled', false);
                        }
                    }
                    else if (this.value == 3) {
                        $(".paypal_form_payment_23").removeClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").addClass('d-none');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 4) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#stripe_form").removeClass('d-none');
                        $(".regular_order_btn").addClass('d-none');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 5) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").removeClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").addClass('d-none');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 6) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#razor_form").removeClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").addClass('d-none');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 7) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $("#bankModal").modal('show');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 8) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $(".instamojoModal").removeClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $("#instamojoModal").modal('show');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 9) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $("#paytmModal").modal('show');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 10) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $(".midtrans_payment_form").removeClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").addClass('d-none');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 11) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $("#PayUMoneyModal").modal('show');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 12) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $("#JazzCashModal").modal('show');
                        $(".regular_order_btn").prop('disabled', false);
                    }
                    else if (this.value == 13) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $("#gPayModal").modal('show');
                        $(".regular_order_btn").prop('disabled', true);
                    }
                    else if (this.value == 14) {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $("#flutterModal").modal('show');
                        $(".regular_order_btn").prop('disabled', true);
                    }
                    else {
                        $(".paypal_form_payment_23").addClass('d-none');
                        $("#paystack_form").addClass('d-none');
                        $("#razor_form").addClass('d-none');
                        $("#stripe_form").addClass('d-none');
                        $(".midtrans_payment_form").addClass('d-none');
                        $(".regular_order_btn").removeClass('d-none');
                        $(".regular_order_btn").prop('disabled', false);
                    }

                });


                $(document).on('click', '.paypal_btn', function(event){
                    event.preventDefault();
                    paypal_form_submit();
                });

                function paypal_form_submit(){
                    $(".paypal_form_payment_23").submit();
                }

                $(document).on('change', '.billing_select_input', function(event){
                    let id = $(this).data('id');
                    billingAddressChange(id);
                });

                $(document).on('change', '.shipping_select_input', function(event){
                    let id = $(this).data('id');
                    shippingAddressChange(id);
                });

                function shippingAddressChange(id){
                    $('#shipping_address_id').val(id);
                }
                function billingAddressChange(id){
                    $('#billing_address_id').val(id);
                }

            });
       })(jQuery);



</script>
@include(theme('partials._guest_user_scripts'))
@endpush
