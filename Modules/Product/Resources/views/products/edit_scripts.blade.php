@push('scripts')
    <script src="{{asset(asset_path('backend/vendors/js/icon-picker.js'))}}"></script>
    <script type="text/javascript">
    (function($) {
        "use strict";

        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200,
                codeviewFilter: true,
			    codeviewIframeFilter: true
            });
            getActiveFieldAttribute();

            $(document).on('click',".prod_type",function(){
                if($('#product_type').val($(this).val())){
                    getActiveFieldAttribute();
                }
                
            });

            $(document).on('change',"#choice_options",function(){
                get_combinations();
            });

            $(document).on('change', '#stock_manage', function(){
                if($('#product_type').val() == 1){
                    if($(this).val() == 1){
                        $('#single_stock_div').removeClass('d-none');
                        $('#stock_manage_div').addClass('col-lg-6');
                        $('#stock_manage_div').removeClass('col-lg-12');
                    }else{
                        $('#single_stock_div').addClass('d-none');
                        $('#stock_manage_div').addClass('col-lg-12');
                        $('#stock_manage_div').removeClass('col-lg-6');
                    }
                }else{
                    $('#single_stock_div').addClass('d-none');
                    if($(this).val() == 1){
                        $('.stock_td').removeClass('d-none');
                    }else{
                        $('.stock_td').addClass('d-none');
                    }
                }
                
            });

            $(document).on('change',"#pdf",function(){
                getFileName($(this).val(),'#pdf_place');
            });

            $(document).on('change',"#meta_image",function(){
                $('#metaImgDelete').addClass('d-none');
                getFileName($(this).val(),'#meta_image_file');
                imageChangeWithFile($(this)[0],'#MetaImgDiv');
            });

            $(document).on('change',"#thumbnail_image",function(){
                getFileName($(this).val(),'#thumbnail_image_file');
                imageChangeWithFile($(this)[0],'#ThumbnailImg');
            });

            $(document).on('change', '.variant_img_change', function(event){
                let name_id = $(this).data('name_id');
                let img_id = $(this).data('img_id');
                getFileName($(this).val(), name_id);
                imageChangeWithFile($(this)[0], img_id);
            });

            $(document).on('change', '.variant_digital_file_change', function(event){
                let name_id = $(this).data('name_id');
                getFileName($(this).val(),name_id);

            });


            getActiveFieldShipping();
            get_combinations();


            $(document).on('change','#galary_image', function(event){
                galleryImage($(this)[0],'#galler_img_prev');
            });

            $(document).on('change','#relatedProductAll', function(event){
                relatedProductAll($(this)[0]);
            });

            $(document).on('change','#upSaleAll', function(event){
                upSaleAll($(this)[0]);
            });

            $(document).on('change','#crossSaleAll', function(event){
                crossSaleAll($(this)[0]);
            });

            $(document).on('click','#metaImgDelete', function(event){
                event.preventDefault();
                $("#pre-loader").removeClass('d-none');
                let id = $(this).data('id');
                let data = {
                    '_token' : '{{ csrf_token() }}',
                    'id' : id
                };
                $.post("{{route('product.meta-img-delete')}}",data, function(response){
                    if(response == 1){
                        $('#meta_image_div').html(
                            `<div class="meta_img_div">
                                <img id="MetaImgDiv" src="{{ asset(asset_path('backend/img/default.png')) }}"alt="">
                            </div>
                            `
                        );
                        toastr.success("{{__('common.deleted_successfully')}}", "{{__('common.success')}}");
                    }else{
                        toastr.error("{{__('common.error_message')}}", "{{__('common.error')}}");
                    }
                    $("#pre-loader").addClass('d-none');
                }).fail(function(response) {
               if(response.responseJSON.error){
                    toastr.error(response.responseJSON.error ,"{{__('common.error')}}");
                    $('#pre-loader').addClass('d-none');
                    return false;
                }

            });
            });


            $(document).on('click', '#add_new_category', function(event){
                event.preventDefault();
                $('#create_category_modal').modal('show');
            });

            $(document).on('mouseover', 'body', function(){
                $('#icon').iconpicker({
                    animation:true
                });
            });

            $(document).on('click','.in_sub_cat', function(event){
                $(".in_parent_div").toggleClass('d-none');
                $('.upload_photo_div').toggleClass('d-none');
            });

            $(document).on('change', '#image', function(event){
                getFileName($('#image').val(),'#image_file');
                imageChangeWithFile($(this)[0],'#catImgShow');
            });

            $(document).on('keyup', '#category_name', function(event){
                processSlug($('#category_name').val(), '#category_slug');
            });


            $(document).on('click', '#add_new_brand', function(event){
                event.preventDefault();
                $('#create_brand_modal').modal('show');
            });

            $(document).on('click', '#add_new_unit', function(event){
                event.preventDefault();
                $('#create_unit_modal').modal('show');
            });

            $(document).on('click', '#add_new_shipping', function(event){
                event.preventDefault();
                $('#create_shipping_modal').modal('show');

            });

            $(document).on("change", "#thumbnail_logo", function (event) {
                event.preventDefault();
                imageChangeWithFile($(this)[0],'#shipping_logo');
                getFileName($(this).val(),'#shipping_logo_file');
            });

            $(document).on("change", "#Brand_logo", function (event) {
                event.preventDefault();
                getFileName($(this).val(),'#logo_file');
                imageChangeWithFile($(this)[0],'#logoImg')
            });

            $(document).on('submit', '#add_category_form',  function(event) {
                event.preventDefault();
                $("#pre-loader").removeClass('d-none');
                var formElement = $(this).serializeArray()
                var formData = new FormData();
                formElement.forEach(element => {
                    formData.append(element.name, element.value);
                });
                //image validaiton
                var validFileExtensions = ['jpeg', 'jpg', 'png'];
                var single_image=document.getElementById('image').files.length;
                if(single_image ==1){
                    var size = (document.getElementById('image').files[0].size / 1024 / 1024).toFixed(2);
                    if (size > 1) {
                       toastr.error("{{__('product.file_must_be_less_than_1_mb')}}","{{__('common.error')}}");
                       return false;
                    }
                    var value=$('#image').val();
                    var type=value.split('.').pop().toLowerCase();
                    if ($.inArray(type, validFileExtensions) == -1) {
                       toastr.error("{{__('product.invalid_type_type_should_be_jpeg_jpg_png')}}","{{__('common.error')}}");
                       return false;
                    }
                    formData.append('image', document.getElementById('image').files[0]);

                }

                formData.append('_token', "{{ csrf_token() }}");

                resetCategoryValidationErrors();

                $.ajax({
                    url: "{{ route('product.category.store') }}",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {

                        $('#category_select_div').html(response.categorySelect);
                        $('#sub_cat_div').html(response.categoryParentList);
                        toastr.success("{{__('common.created_successfully')}}", "{{__('common.success')}}");

                        $('#create_category_modal').modal('hide');
                        $('#add_category_form')[0].reset();
                        $('#category_id').niceSelect();
                        $('#parent_id').niceSelect();
                        $('#sub_cat_div').addClass('d-none');
                        $('#category_image_div').removeClass('d-none');
                        $('#category_image_preview_div').removeClass('d-none');

                        $("#pre-loader").addClass('d-none');
                        $('#category_image_div').html(
                        `
                            <label class="primary_input_label" for="">{{__('common.upload_photo')}} ({{__('common.file_less_than_1MB')}})</label>

                            <div class="primary_input mb-25">
                                <div class="primary_file_uploader">
                                  <input class="primary-input" type="text" id="image_file" placeholder="{{__('common.browse_image_file')}}" readonly="">
                                  <button class="" type="button">
                                      <label class="primary-btn small fix-gr-bg" for="image">{{__("common.browse")}} </label>
                                      <input type="file" class="d-none" name="image" id="image">
                                  </button>
                               </div>


                                <span class="text-danger" id="error_category_image"></span>

                            </div>
                        `
                        );
                        $('#category_image_preview_div').html(
                        `
                        <img id="catImgShow" src="{{ asset(asset_path('backend/img/default.png')) }}" alt="">
                        `
                        );
                    },
                    error: function(response) {
                        if(response.responseJSON.error){
                            toastr.error(response.responseJSON.error ,"{{__('common.error')}}");
                            $('#pre-loader').addClass('d-none');
                            return false;
                        }
                        toastr.error("{{__('common.error_message')}}", "{{__('common.error')}}");
                        showCategoryValidationErrors('#add_category_form', response.responseJSON.errors);
                        $("#pre-loader").addClass('d-none');
                    }
                });
            });


            $(document).on('submit', '#create_brand_form', function(event){
                event.preventDefault();
                $('#pre-loader').removeClass('d-none');

                resetBrandError();

                let formElement = $(this).serializeArray()
                let formData = new FormData();
                formElement.forEach(element => {
                        formData.append(element.name,element.value);
                });

                let logo = $('#Brand_logo')[0].files[0];

                if(logo){
                    formData.append('logo',logo);
                }


                formData.append('_token',"{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('product.brand.store')}}",
                    type:"POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success:function(response){
                        $('#brand_select_div').html(response);
                        toastr.success('{{__("product.brand")}} {{__("common.created_successfully")}}');
                        $('#pre-loader').addClass('d-none');
                        $('#create_brand_modal').modal('hide');
                        $('#brand_id').niceSelect();
                        $('#create_brand_form')[0].reset();
                        $('#brand_logo_img_div').html(
                            `
                            <div class="primary_input mb-25">
                                            <div class="primary_file_uploader">
                                                <input class="primary-input" type="text" id="logo_file" placeholder="{{__('common.browse_image_file')}}" readonly="">
                                                <button class="" type="button">
                                                    <label class="primary-btn small fix-gr-bg" for="Brand_logo">{{__("common.logo")}} </label>
                                                    <input type="file" class="d-none" name="logo" id="Brand_logo">
                                                </button>
                                            </div>


                                            <span class="text-danger" id="error_brand_logo"></span>

                            </div>
                            `
                        );
                        $('#brand_logo_preview_div').html(
                            `<img id="logoImg" src="{{ asset(asset_path('backend/img/default.png')) }}" alt="">`
                        );
                        $('#brand_status').val(1);
                        $('#brand_status').niceSelect('update');
                        $('#brand_des_div').html(
                            `<div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{__("common.description")}} </label>
                                            <textarea class="summernote" name="description"></textarea>
                                        </div>`

                        );
                        $('.summernote').summernote({
                            height: 200,
                            codeviewFilter: true,
			                codeviewIframeFilter: true
                        });


                    },
                    error:function(response) {
                        if(response.responseJSON.error){
                            toastr.error(response.responseJSON.error ,"{{__('common.error')}}");
                            $('#pre-loader').addClass('d-none');
                            return false;
                        }
                        showBrandValidationErrors(response.responseJSON.errors);
                        $('#pre-loader').addClass('d-none');
                    }
                });
            });

            $(document).on('submit', '#create_unit_form', function(event){
                event.preventDefault();
                $('#pre-loader').removeClass('d-none');

                resetUnitError();

                let formElement = $(this).serializeArray()
                let formData = new FormData();
                formElement.forEach(element => {
                    formData.append(element.name,element.value);
                });

                formData.append('_token',"{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('product.units.store')}}",
                    type:"POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success:function(response){
                        $('#unit_select_div').html(response);
                        toastr.success('{{__("product.unit")}} {{__("common.created_successfully")}}');
                        $('#pre-loader').addClass('d-none');
                        $('#create_unit_modal').modal('hide');
                        $('#unit_type_id').niceSelect();
                        $('#create_unit_form')[0].reset();
                        $('#unit_active_status').prop('checked',true);
                        $('#unit_inactive_status').prop('checked',false);

                    },
                    error:function(response) {
                        if(response.responseJSON.error){
                            toastr.error(response.responseJSON.error ,"{{__('common.error')}}");
                            $('#pre-loader').addClass('d-none');
                            return false;
                        }
                        showUnitValidationErrors(response.responseJSON.errors);
                        $('#pre-loader').addClass('d-none');
                    }
                });
            });

            $(document).on('submit', '#create_shipping_form', function(event){
                event.preventDefault();
                $('#pre-loader').removeClass('d-none');

                let shipment_time = $('#shipment_time').val();
                $('#error_shipping_shipment_time').text('');

                let userKeyRegExp1 = /^[0-9]\-[0-9] [a-z]{4}?$/;
                let userKeyRegExp2 = /^[0-9]\-[0-9]{2}\ [a-z]{4}?$/;
                let userKeyRegExp3 = /^[0-9]\-[0-9]{3}\ [a-z]{4}?$/;
                let userKeyRegExp4 = /^[0-9]{2}\-[0-9]{2}\ [a-z]{4}?$/;
                let userKeyRegExp5 = /^[0-9]{2}\-[0-9]{3}\ [a-z]{4}?$/;
                let userKeyRegExp6 = /^[0-9]{3}\-[0-9]{3}\ [a-z]{4}?$/;

                let userKeyRegExp7 = /^[0-9]\-[0-9]\ [a-z]{3}?$/;
                let userKeyRegExp8 = /^[0-9]\-[0-9]{2}\ [a-z]{3}?$/;
                let userKeyRegExp9 = /^[0-9]\-[0-9]{3}\ [a-z]{3}?$/;
                let userKeyRegExp10 = /^[0-9]{2}\-[0-9]{2}\ [a-z]{3}?$/;
                let userKeyRegExp11 = /^[0-9]{2}\-[0-9]{3}\ [a-z]{3}?$/;
                let userKeyRegExp12 = /^[0-9]{3}\-[0-9]{3}\ [a-z]{3}?$/;

                let valid1 = userKeyRegExp1.test(shipment_time);
                let valid2 = userKeyRegExp2.test(shipment_time);
                let valid3 = userKeyRegExp3.test(shipment_time);
                let valid4 = userKeyRegExp4.test(shipment_time);
                let valid5 = userKeyRegExp5.test(shipment_time);
                let valid6 = userKeyRegExp6.test(shipment_time);
                let valid7 = userKeyRegExp7.test(shipment_time);
                let valid8 = userKeyRegExp8.test(shipment_time);
                let valid9 = userKeyRegExp9.test(shipment_time);
                let valid10 = userKeyRegExp10.test(shipment_time);
                let valid11 = userKeyRegExp11.test(shipment_time);
                let valid12 = userKeyRegExp12.test(shipment_time);

                if(valid1 !=false || valid2!=false || valid3!=false || valid4!=false || valid5!=false ||
                    valid6!=false || valid7!=false || valid8!=false || valid9!=false || valid10!=false || valid11!=false || valid12!=false){
                    let data1 = shipment_time.split(" ");

                    if(data1[1] == 'days' || data1[1] == 'hrs'){

                    }else{
                        $('#pre-loader').addClass('d-none');
                        $('#error_shipping_shipment_time').text('Format must be like 3-5 days or 3-5 hrs');
                        return false;
                    }

                }
                else{
                    $('#pre-loader').addClass('d-none');
                    $('#error_shipping_shipment_time').text('Format must be like 3-5 days or 3-5 hrs');
                    return false;
                }

                $('#error_shipping_shipment_time').text('');

                let formElement = $(this).serializeArray()
                let formData = new FormData();
                formElement.forEach(element => {
                    formData.append(element.name,element.value);
                });

                let method_logo = $('#thumbnail_logo')[0].files[0];

                if(method_logo){
                    formData.append('method_logo',method_logo);
                }

                resetShippingError();


                formData.append('_token',"{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('shipping_methods.store')}}",
                    type:"POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success:function(response){
                        $('#shipping_method_div').html(response);
                        toastr.success('{{__("common.created_successfully")}}');
                        $('#pre-loader').addClass('d-none');
                        $('#create_shipping_modal').modal('hide');
                        $('#shipping_methods').niceSelect();
                        $('#create_shipping_form')[0].reset();
                        $('#method_logo_img_div').html(
                            `
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="primary_input mb-25">
                                        <label class="primary_input_label" for="">{{ __('shipping.logo') }} </label>
                                        <div class="primary_file_uploader">
                                            <input class="primary-input" type="text" id="logo_file" placeholder="{{ __('shipping.logo') }}" readonly="">
                                            <button class="" type="button">
                                                <label class="primary-btn small fix-gr-bg" for="thumbnail_logo">{{ __('product.Browse') }} </label>

                                                <input type="file" class="d-none" name="method_logo" id="thumbnail_logo">
                                            </button>
                                            <span class="text-danger" id="error_shipping_thumbnail_logo"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <img id="shipping_logo" class="" src="{{ asset(asset_path('backend/img/default.png')) }}" alt="">
                                </div>
                            </div>
                            `
                        );


                    },
                    error:function(response) {
                        if(response.responseJSON.error){
                            toastr.error(response.responseJSON.error ,"{{__('common.error')}}");
                            $('#pre-loader').addClass('d-none');
                            return false;
                        }
                            $.each(response.responseJSON.errors, function (key, message) {
                                    $("#" +"error_shipping_" + key).html(message[0]);
                                });
                            $('#pre-loader').addClass('d-none');
                    }
                });
            });


            function showBrandValidationErrors(errors){
                $('#error_brand_name').text(errors.name);
                $('#error_brand_logo').text(errors.logo);
            }
            function resetBrandError(){
                $('#error_brand_name').text('');
                $('#error_brand_logo').text('');
            }

            function showUnitValidationErrors(errors){
                $('#error_unit_name').text(errors.name);
                $('#error_unit_status').text(errors.status);
            }
            function resetUnitError(){
                $('#error_unit_name').text('');
                $('#error_unit_status').text('');
            }
            function resetShippingError(){
                $('#error_shipping_method_name').text('');
                $('#error_shipping_phone').text('');
                $('#error_shipping_shipment_time').text('');
                $('#error_shipping_cost').text('');
                $('#error_shipping_cost').text('');
            }

            function showCategoryValidationErrors(formType, errors) {
                $(formType +' #error_category_name').text(errors.name);
                $(formType +' #error_category_slug').text(errors.slug);
                $(formType +' #error_category_searchable').text(errors.searchable);
                $(formType +' #error_category_icon').text(errors.icon);
                $(formType +' #error_category_status').text(errors.status);
                $(formType +' #error_category_image').text(errors.image);
            }

            function resetCategoryValidationErrors(){
                $('#error_category_name').text('');
                $('#error_category_slug').text('');
                $('#error_category_searchable').text('');
                $('#error_category_icon').text('');
                $('#error_category_status').text('');
                $('#error_category_image').text('');
            }


        });



        $(document).on('click','.saveBtn',function() {

            $('#error_single_sku').text('');
            $('#error_product_name').text('');
            $('#error_category_ids').text('');
            $('#error_unit_type').text('');
            $('#error_minumum_qty').text('');
            $('#error_selling_price').text('');
            $('#error_tax').text('');
            $('#error_discunt').text('');
            $('#error_thumbnail').text('');
            $('#error_shipping_method').text('');
            $('#error_tags').text('');
            var requireMatch = 0;
            if ($("#product_name").val() === '') {
                requireMatch = 1;
                $('#error_product_name').text("{{ __('product.please_input_product_name') }}");
            }
            if ($("#sku_single").val() === '') {
                requireMatch = 1;
                $('#error_single_sku').text("{{ __('product.please_input_product_sku') }}");
            }
            if ($("#category_id").val().length < 1) {
                requireMatch = 1;
                $('#error_category_ids').text("{{ __('product.please_select_category') }}");
            }
            if ($("#unit_type_id").val() === null) {
                requireMatch = 1;
                $('#error_unit_type').text("{{ __('product.please_select_product_unit') }}");
            }
            if ($("#minimum_order_qty").val() === '') {
                requireMatch = 1;
                $('#error_minumum_qty').text("{{ __('product.please_input_minimum_order_qty') }}");
            }
            if ($("#selling_price").val() === '') {
                requireMatch = 1;
                $('#error_selling_price').text("{{ __('product.please_input_selling_price') }}");
            }
            if ($("#tax").val() === '') {
                requireMatch = 1;
                $('#error_tax').text("{{ __('product.please_input_tax') }}");
            }
            if ($("#discount").val() === '') {
                requireMatch = 1;
                $('#error_discunt').text("{{ __('product.please_input_discount_minimum_0') }}");
            }
            if ($("#shipping_methods").val().length < 1) {
                requireMatch = 1;
                $('#error_shipping_method').text("{{ __('product.please_select_shipping_method') }}");
            }
            if ($("#tags").val() === '') {
                requireMatch = 1;
                $('#error_tags').text("{{ __('product.please_input_tags') }}");
            }
            if ($('#product_type').val() === '2' && $(".choice_attribute").val().length === 0) {
                requireMatch = 1;
                toastr.warning("{{ __('product.please_select_attribute') }}");
            }
            if (requireMatch == 1) {
                event.preventDefault();
            }
        });
        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                var a_id = $(this).val();
                var a_name = $(this).text();
                $.post('{{ route('product.attribute.values') }}', {
                    _token: '{{ csrf_token() }}',
                    id: a_id
                }, function(data) {
                    add_more_customer_choice_option(a_id, a_name, data);
                });

            });
        });



        var ENDPOINT = "{{ url('/') }}";
        var Rpage = 0;
        var Upage = 0;
        var Cpage = 0;
        var product_id = '{{$product->id}}';
        $(".lodeMoreRelatedSale").on('click',function() {
            event.preventDefault();

            Rpage++;
            var new_url = '/product/get-related-product-for-admin?except_id='+product_id+'&page=';
            var tbl_name = "#tablecontentsrelatedProduct";
            infinteLoadMore(Rpage, new_url, tbl_name)
        });
        $(".lodeMoreUpSale").on('click',function() {
            event.preventDefault();
            Upage++;
            var new_url = '/product/get-upsale-product-for-admin?except_id='+product_id+'&page=';
            var tbl_name = "#tablecontentsupSaleAll";
            infinteLoadMore(Upage, new_url, tbl_name)
        });
        $(".lodeMoreCrossSale").on('click',function() {
            event.preventDefault();
            Cpage++;
            var new_url = '/product/get-cross-sale-product-for-admin?except_id='+product_id+'&page=';
            var tbl_name = "#tablecontentscrossSaleAll";
            infinteLoadMore(Cpage, new_url, tbl_name)
        });
        function infinteLoadMore(page, new_url, tbl_name) {
            $.ajax({
                    url: ENDPOINT + new_url + page,
                    datatype: "html",
                    type: "get",
                    beforeSend: function () {
                        $('.auto-load').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        toastr.warning("{{ __('product.no_more_data_to_show') }}");
                        return;
                    }
                    $('.auto-load').hide();
                    $(tbl_name).append(response);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occured');
            });
        }

        function shipping_div_hide() {
            $('.shipping_title_div').hide();
            $('.shipping_type_div').hide();
            $('.shipping_cost_div').hide();
            $('#shipping_cost').val(0);
        }

        function shipping_div_show() {
            $('.shipping_title_div').show();
            $('.shipping_type_div').show();
            $('.shipping_cost_div').show();
            $('#shipping_cost').val(0);
        }

        function add_more_customer_choice_option(i, name, data) {
            var option_value = '';
            $.each(data.values, function(key, item) {
                if (item.color) {
                    option_value += `<option value="${item.id}">${item.color.name}</option>`
                } else {
                    option_value += `<option value="${item.id}">${item.value}</option>`
                }
            });
            $('#customer_choice_options').append(
                '<div class="row"><div class="col-lg-4"><input type="hidden" name="choice_no[]" value="' + i +
                '"><div class="primary_input mb-25"><input class="primary_input_field" width="40%" name="choice[]" type="text" value="' +
                name + '" readonly></div></div><div class="col-lg-8">' +
                '<div class="primary_input mb-25">' +
                '<select name="choice_options_' + i +
                '[]" id="choice_options" class="primary_select mb-15" onchange="get_combinations()" multiple>' +
                option_value +
                '</select' +
                '</div>' +
                '</div></div>');
            $('select').niceSelect();
        }

        function get_combinations(el) {
            $.ajax({
                type: "POST",
                url: '{{ route('product.sku_combination_edit') }}',
                data: $('#choice_form').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(data) {
                    $('.sku_combination').html(data);
                    if ($('#is_physical').is(":checked")) {
                        $('.variant_physical_div').show();
                        $('.variant_digital_div').hide();
                    } else {
                        $('.variant_physical_div').hide();
                        $('.variant_digital_div').show();
                    }

                    if($('#stock_manage').val() == 1){
                        $('.stock_td').removeClass('d-none');
                    }else{
                        $('.stock_td').addClass('d-none');
                    }
                }
            });
        }

        function getActiveFieldAttribute() {
            var product_type = $('#product_type').val();
            if (product_type == 1) {
                $('.attribute_div').hide();

                $('.variant_physical_div').hide();
                $('.customer_choice_options').hide();
                $('.sku_combination').hide();

                $('.sku_single_div').show();
                $('.selling_price_div').show();
                $("#sku_single").removeAttr("disabled");
                $("#purchase_price").removeAttr("disabled");
                $("#selling_price").removeAttr("disabled");

                if($('#stock_manage').val() == 1){
                    $('#single_stock_div').removeClass('d-none');
                    $('#stock_manage_div').addClass('col-lg-6');
                    $('#stock_manage_div').removeClass('col-lg-12');
                }else{
                    $('#single_stock_div').addClass('d-none');
                    $('#stock_manage_div').removeClass('col-lg-6');
                    $('#stock_manage_div').addClass('col-lg-12');
                }
            } else {
                $('.attribute_div').show();
                $('.sku_single_div').hide();

                
                $('.variant_physical_div').show();
                $('.sku_combination').show();
                $('.customer_choice_options').show();

                $('.selling_price_div').hide();
                $("#sku_single").attr('disabled', true);
                $("#purchase_price").attr('disabled', true);
                $("#selling_price").attr('disabled', true);

                $('#single_stock_div').addClass('d-none');
                $('#stock_manage_div').removeClass('col-lg-6');
                $('#stock_manage_div').addClass('col-lg-12');
            }
        }

        function getActiveFieldShipping() {
            var shipping_type = $('#shipping_type').val();
            if (shipping_type == 1) {
                $('.shipping_cost_div').hide();
                $('#shipping_cost').val(0);
            } else {
                $('.shipping_cost_div').show();
                $('#shipping_cost').val(0);
            }
        }

        function galleryImage(data, divId) {
            if (data.files) {

                $.each(data.files, function(key, value) {
                    $('#gallery_img_prev').empty();
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#gallery_img_prev').append(
                            `
                                <div class="galary_img_div">
                                    <img class="galaryImg" src="`+ e.target.result +`" alt="">
                                </div>
                            `
                        );

                    };
                    reader.readAsDataURL(value);
                });
            }
        }



        //related product
        function relatedProductAll(el){
            if(el.checked){
                $("input[name*='related_product']").prop('checked',true);
            }else{
                $("input[name*='related_product']").prop('checked',false);
            }
        }

        //up sale
        function upSaleAll(el){
            if(el.checked){
                $("input[name*='up_sale']").prop('checked',true);
            }else{
                $("input[name*='up_sale']").prop('checked',false);
            }
        }

        //cross sale
        function crossSaleAll(el){
            if(el.checked){
                $("input[name*='cross_sale']").prop('checked',true);
            }else{
                $("input[name*='cross_sale']").prop('checked',false);
            }
        }


        // tag
         // when page load get tag before focus
         var sentence = $("#product_name").val();
                $.get('/setup/getTagBySentence',{sentence:sentence},function(result){
                    $("#tag_show").append(result);
                })
        $(document).on('click', '.tag-add', function(e){
            e.preventDefault();
            $('#tag-input-upload-shots').tagsinput('add', $(this).text());
        });
        $(document).on('focusout', '#product_name', function(){
            // tag get
            $("#tag_show").html('<li></li>');
            var sentence = $(this).val();
            $.get('/setup/getTagBySentence',{sentence:sentence},function(result){
                $("#tag_show").append(result);
            })
        });

    })(jQuery);




    </script>
@endpush
