<div class="row">
    @if (count($cartData) > 0)
    <div class="col-xl-8 mb_30">
        @php
            $all_select_count = 0;
            $subtotal = 0;
            $shipping_cost = 0;
            $sellect_seller = 0;
            $selected_product_check  = 0;
            $all_select_data = 0;

            foreach ($cartData as $key => $items) {
                $all_select_count += count($items);
                $sellect_seller  = $key;
                $p = 0;

                foreach ($items as $key => $data) {
                    if (auth()->check()) {
                        if ($data->is_select == 1) {
                            $all_select_count = $all_select_count - 1;
                            $subtotal +=$data->total_price;
                            $selected_product_check ++;
                            $p = 1;
                            $all_select_data +=1;
                        }

                    }else{
                        if ($data['is_select'] == 1) {
                            $all_select_count = $all_select_count - 1;
                            $subtotal +=$data['total_price'];
                            $selected_product_check ++;
                            $p = 1;
                            $all_select_data += 1;
                        }

                    }

                }
                if($p == 1){
                    $shipping_cost += 20;

                }
            }
        @endphp

        <div class="cartv2_header bg-white d-flex align-items-center justify-content-between mb_20">
            <div class="cartv2_check d-flex align-items-center text-uppercase gap_15">
                <label class="cs_checkbox cartv2_check_box">
                    <input type="checkbox" {{$all_select_count == 0?'checked':''}} id="selectAllItem">
                    <span class="checkmark"></span>
                </label>
                <h6 class="f_s_14 f_w_600 mb-0" >SELECT ALL ({{$all_select_data}} ITEM(S))</h6>
            </div>
            <p class="d-flex align-items-center text-uppercase f_s_14 f_w_500 cartv2_delete gap_10" id="delete_all_btn"> <i class="ti-trash"></i> Delete</p>
        </div>

        <!-- cartv2_product_list  -->
        @foreach($cartData as $key =>$items)
            @php
                $seller = App\Models\User::where('id',$key)->first();
                $select_count = count($items);
            @endphp
            @foreach($items as $m => $data)
                @php
                    if(auth()->check()){
                        if($data->is_select == 1){
                            $select_count = $select_count - 1;
                        }else{
                            $select_count = $select_count;
                        }
                    }else {
                        if($data['is_select'] ==1){
                            $select_count = $select_count - 1;
                        }else{
                            $select_count = $select_count;
                        }
                    }
                @endphp
            @endforeach
        <div class="cartv2_product_list bg-white mb_30">
            <!-- cartv2_product_header  -->
            <div class="cartv2_product_header">
                <div class="cartv2_check d-flex  gap_15">
                    <label class="cs_checkbox cartv2_check_box">
                        <input class="item_check select_all_item_check" type="checkbox" {{$select_count == 0? 'checked':''}} id="sellerAllItem_{{$seller->id}}" data-unique_id="#sellerAllItem_{{$seller->id}}" data-seller_id="{{$seller->id}}" data-seller_item_unique=".seller_item_{{$seller->id}}">
                        <span class="checkmark"></span>
                    </label>
                    <div class="cart_d">
                        <a href="@if($seller->slug) {{route('frontend.seller',$seller->slug)}} @else {{route('frontend.seller',base64_encode($seller->id))}} @endif"><h6 class="f_s_16 f_w_600 mb-1" >@if($seller->role->type == 'seller') {{$seller->first_name .' '.$seller->last_name}} @else {{ app('general_setting')->company_name }} @endif <i class="ti-angle-right"></i> </h6></a>

                    </div>

                </div>
            </div>

            <!-- cartv2_product_body  -->
            <div class="cartv2_product_body">
                @foreach($items as $key => $data)

                    @if(auth()->check())
                        @php
                            $shipping_method = $data->shippingMethod;
                            $cart_Id = $data->id;
                            $shipping_methodGiftCard = \Modules\Shipping\Entities\ShippingMethod::first();
                        @endphp
                        @if ($data->product_type != "gift_card")
                            <div class="cartv2_product_single d-flex mb_10">
                                <div class="cartv2_product_body_left d-flex align-items-center">
                                    <label class="cs_checkbox cartv2_check_box">
                                        <input type="checkbox" class="item_check seller_item_{{$seller->id}} select_single_item_check" id="single_pro_{{$seller->id}}_{{$data->product_id}}" data-product_type="{{$data->product_type}}" data-product_id="{{$data->product_id}}" data-unique_id="#single_pro_{{$seller->id}}_{{$data->product_id}}" {{$data->is_select?'checked':''}}>
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="thumb">
                                        <img src="
                                            @if(@$data->product->product->product->product_type == 1)
                                            {{asset(asset_path(@$data->product->product->product->thumbnail_image_source))}}
                                            @else
                                            {{asset(asset_path(@$data->product->sku->variant_image?@$data->product->sku->variant_image:@$data->product->product->product->thumbnail_image_source))}}
                                            @endif
                                        " alt="">
                                    </div>
                                    <div class="product_info">
                                        <h5><a href="{{route('frontend.item.show',@$data->product->product->slug)}}">{{@$data->product->product->product->product_name}}</a></h5>
                                        <p>
                                            @if(@$data->product->product->product->product_type == 2)

                                                @foreach(@$data->product->product_variations as $key => $combination)
                                                    @if(@$combination->attribute->name == 'Color')
                                                        {{@$combination->attribute->name}}: {{@$combination->attribute_value->color->name}}
                                                    @else
                                                        {{@$combination->attribute->name}}: {{@$combination->attribute_value->value}}
                                                    @endif
                                                    @if($key < count(@$data->product->product_variations)-1),@endif

                                                @endforeach

                                            @endif
                                        </p>

                                        @php
                                            $current_shipping = \Modules\Shipping\Entities\ShippingMethod::find($data->shipping_method_id);
                                        @endphp
                                        <span class="Shipping_text {{@$data->product->product->product->is_physical == 1?'shipping_show':''}}" data-id="#shipping_method_modal_{{$data->id}}"> <span>{{__('common.shipping')}}: {{single_price($current_shipping->cost)}} </span> via {{substr($current_shipping->method_name,0,11) }} @if(strlen($current_shipping->method_name) > 11)... @endif - {{@$current_shipping->shipment_time}} <i class="ti-angle-right"></i></span>
                                    </div>
                                </div>
                                <div class="cartv2_product_body_right d-flex align-items-center">
                                    <div class="prise_heiglite d-flex align-items-center gap_10">

                                        @if($data->product->product->hasDeal)
                                            @if($data->product->product->hasDeal->discount > 0)
                                                @if($data->product->product->hasDeal->discount_type == 0)
                                                    <span class="offer_prise">-{{$data->product->product->hasDeal->discount}} %</span>
                                                    <span class="curent_prise">{{single_price($data->product->selling_price)}}</span>
                                                @else

                                                    <span class="offer_prise">-{{single_price($data->product->product->hasDeal->discount)}}</span>
                                                    <span class="curent_prise">{{single_price($data->product->selling_price)}}</span>
                                                @endif
                                            @endif
                                        @else
                                            @if(@$data->product->product->hasDiscount == 'yes')
                                                @if($data->product->product->discount_type == 0)
                                                    <span class="offer_prise">-{{$data->product->product->discount}} %</span>
                                                    <span class="curent_prise">{{single_price($data->product->selling_price)}}</span>
                                                @else
                                                    <span class="offer_prise"> -{{single_price($data->product->product->discount)}}</span>
                                                    <span class="curent_prise">{{single_price($data->product->selling_price)}}</span>
                                                @endif
                                            @else
                                                <span class="curent_prise price_rate">{{single_price($data->product->selling_price)}}</span>
                                            @endif
                                        @endif

                                    </div>
                                    <div class="product_count">
                                        <input type="hidden" id="maximum_qty_{{$data->id}}" value="{{$data->product->product->product->max_order_qty}}">
                                        <input type="hidden" id="minimum_qty_{{$data->id}}" value="{{$data->product->product->product->minimum_order_qty}}">

                                        <input type="text" name="qty" class="qty" id="qty_{{$data->id}}" maxlength="12" value="{{$data->qty}}" readonly>
                                        <div class="button-container">
                                            <button class="cart-qty-plus qty_change" id="qty_plus_btn_{{$data->id}}"  type="button" value="+" data-value="+" data-id="{{$data->id}}" data-product-id="{{$data->product_id}}" data-qty="#qty_{{$data->id}}"
                                                data-qty-plus-btn-id="#qty_plus_btn_{{$data->id}}" data-qty-minus-btn-id="#qty_minus_btn_{{$data->id}}" data-maximum-qty="#maximum_qty_{{$data->id}}" data-minimum-qty="#minimum_qty_{{$data->id}}"
                                                 data-stock-manage="{{$data->product->product->stock_manage}}" data-product-stock="{{$data->product->product_stock}}"><i class="ti-plus"></i></button>

                                            <button class="cart-qty-minus qty_change" id="qty_minus_btn_{{$data->id}}" value="-" type="button" data-value="-" data-id="{{$data->id}}" data-product-id="{{$data->product_id}}"
                                                 data-qty="#qty_{{$data->id}}" data-qty-plus-btn-id="#qty_plus_btn_{{$data->id}}" data-qty-minus-btn-id="#qty_minus_btn_{{$data->id}}"  data-maximum-qty="#maximum_qty_{{$data->id}}"
                                                  data-minimum-qty="#minimum_qty_{{$data->id}}" data-stock-manage="{{$data->product->product->stock_manage}}" data-product-stock="{{$data->product->product_stock}}"><i class="ti-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="oridinal_prise d-flex align-items-center">
                                        <h6 class="m-0 mr-1">{{single_price($data->total_price)}}</h6>
                                        <i class="ti-trash cart_item_delete_btn" data-id="{{$data->id}}" data-product_id="{{$data->product_id}}" data-unique_id="#delete_item_{{$data->id}}" id="delete_item_{{$data->id}}"></i>
                                    </div>
                                </div>
                            </div>
                            @if($data->product->product->product->is_physical == 1)
                                @include('frontend.default.partials._cart_shipping_method', ['data' => $data, 'shipping_methods' => @$data->product->product->product->shippingMethods])
                            @endif
                        @else
                            <div class="cartv2_product_single d-flex mb_10">
                                <div class="cartv2_product_body_left d-flex align-items-center">
                                    <label class="cs_checkbox cartv2_check_box">
                                        <input type="checkbox" class="item_check seller_item_{{$seller->id}} select_single_item_check" id="single_pro_{{$seller->id}}_{{$data->product_id}}" data-product_type="{{$data->product_type}}" data-product_id="{{$data->product_id}}" data-unique_id="#single_pro_{{$seller->id}}_{{$data->product_id}}" {{$data->is_select?'checked':''}}>
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="thumb">
                                        <img src="{{asset(asset_path(@$data->giftCard->thumbnail_image))}}" alt="">
                                    </div>
                                    <div class="product_info">
                                        <h5><a href="{{route('frontend.gift-card.show',$data->giftCard->sku)}}">{{$data->giftCard->name}}</a></h5>
                                        <span class="Shipping_text"> <span>{{__('common.shipping')}}: {{single_price(@$shipping_methodGiftCard->cost)}} </span> via {{substr($shipping_methodGiftCard->method_name,0,11)}} @if(strlen($shipping_methodGiftCard->method_name) > 11)... @endif - {{@$shipping_methodGiftCard->shipment_time}} <i class="ti-angle-right"></i></span>
                                    </div>
                                </div>
                                <div class="cartv2_product_body_right d-flex align-items-center">
                                    <div class="prise_heiglite d-flex align-items-center gap_10">
                                        @if($data->giftCard->hasDiscount())
                                            @if($data->giftCard->discount_type == 0)
                                                <span class="offer_prise">-{{$data->giftCard->discount}}%</span>
                                            @else
                                                <span class="offer_prise">-{{single_price($data->giftCard->discount)}}</span>
                                            @endif
                                            <span class="curent_prise">{{single_price($data->giftCard->selling_price)}}</span>
                                        @else
                                        <span class="curent_prise price_rate">{{single_price($data->giftCard->selling_price)}}</span>
                                        @endif

                                    </div>
                                    <div class="product_count">
                                        <input type="hidden" id="maximum_qty_{{$data->id}}" value="0">
                                        <input type="hidden" id="minimum_qty_{{$data->id}}" value="1">

                                        <input type="text" name="qty" id="qty_{{$data->id}}" class="qty" maxlength="12" value="{{$data->qty}}" readonly>
                                        <div class="button-container">
                                            <button class="cart-qty-plus qty_change" type="button" value="+" id="qty_plus_btn_{{$data->id}}" data-value="+" data-id="{{$data->id}}" data-product-id="{{$data->product_id}}"
                                                data-qty="#qty_{{$data->id}}" data-qty-plus-btn-id="#qty_plus_btn_{{$data->id}}" data-qty-minus-btn-id="#qty_minus_btn_{{$data->id}}" data-maximum-qty="#maximum_qty_{{$data->id}}" data-minimum-qty="#minimum_qty_{{$data->id}}" data-stock-manage="0" data-product-stock="1"><i class="ti-plus"></i></button>
                                            <button class="cart-qty-minus qty_change" type="button" value="-" id="qty_minus_btn_{{$data->id}}" {{ $data->qty<2?'disabled':'' }} data-value="-" data-id="{{$data->id}}" data-product-id="{{$data->product_id}}"
                                                data-qty="#qty_{{$data->id}}" data-qty-plus-btn-id="#qty_plus_btn_{{$data->id}}" data-qty-minus-btn-id="#qty_minus_btn_{{$data->id}}" data-maximum-qty="#maximum_qty_{{$data->id}}" data-minimum-qty="#minimum_qty_{{$data->id}}" data-stock-manage="0" data-product-stock="0"><i class="ti-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="oridinal_prise d-flex align-items-center">
                                        <h6 class="m-0 mr-1">{{single_price($data->total_price)}}</h6>
                                        <i class="ti-trash cart_item_delete_btn" data-id="{{$data->id}}" data-product_id="{{$data->product_id}}" data-unique_id="#delete_item_{{$data->id}}" id="delete_item_{{$data->id}}"></i>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        @if ($data['product_type'] == "gift_card")
                            @php
                                $product = \Modules\GiftCard\Entities\GiftCard::where('id',$data['product_id'])->first();
                                $rand = rand(8888,1000000);
                                $cart_Id = $data['cart_id'];
                                $shipping_method = \Modules\Shipping\Entities\ShippingMethod::where('id',$data['shipping_method_id'])->first();
                                $shipping_methodGiftCard = \Modules\Shipping\Entities\ShippingMethod::first();
                            @endphp
                            <div class="cartv2_product_single d-flex mb_10">
                                <div class="cartv2_product_body_left d-flex align-items-center">
                                    <label class="cs_checkbox cartv2_check_box">
                                        <input type="checkbox" class="select_single_item_check item_check seller_item_{{$seller->id}}" {{$data['is_select'] == 1 ?'checked':''}} id="single_pro_{{$seller->id}}_{{$data['product_id']}}" data-product_type="{{$data['product_type']}}" data-product_id="{{$data['product_id']}}" data-unique_id="#single_pro_{{$seller->id}}_{{$data['product_id']}}">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="thumb">
                                        <img src="{{asset(asset_path(@$product->thumbnail_image))}}" alt="">
                                    </div>
                                    <div class="product_info">
                                        <h5><a href="{{route('frontend.gift-card.show',$product->sku)}}">{{$product->name}}</a></h5>
                                        <span class="Shipping_text"> <span>{{__('common.shipping')}}: {{single_price(@$shipping_methodGiftCard->cost)}} </span> via {{substr($shipping_methodGiftCard->method_name,0,11)}} @if(strlen($shipping_methodGiftCard->method_name) > 11)... @endif - {{@$shipping_methodGiftCard->shipment_time}} <i class="ti-angle-right"></i></span>
                                    </div>
                                </div>
                                <div class="cartv2_product_body_right d-flex align-items-center">
                                    <div class="prise_heiglite d-flex align-items-center gap_10">
                                        @if($product->hasDiscount())
                                            @if($product->discount_type == 0)
                                                <span class="offer_prise">-{{$product->discount}}%</span>
                                            @else
                                                <span class="offer_prise">-{{single_price($product->discount)}}</span>
                                            @endif
                                            <span class="curent_prise">{{single_price($product->selling_price)}}</span>
                                        @else
                                        <span class="curent_prise price_rate">{{single_price($product->selling_price)}}</span>
                                        @endif
                                    </div>
                                    <div class="product_count">
                                        <input type="hidden" id="maximum_qty_{{$rand}}" value="0">
                                        <input type="hidden" id="minimum_qty_{{$rand}}" value="1">
                                        <input type="text" name="qty" class="qty" maxlength="12" value="{{$data['qty']}}" id="qty_{{$rand}}">
                                        <div class="button-container">
                                            <button class="cart-qty-plus qty_change" type="button" value="+" id="qty_plus_btn_{{$rand}}" data-value="+" data-id="{{$seller->id}}"
                                                 data-product-id="{{$product->id}}" data-qty="#qty_{{$rand}}" data-qty-plus-btn-id="#qty_plus_btn_{{$rand}}" data-qty-minus-btn-id="#qty_minus_btn_{{$rand}}" data-maximum-qty="#maximum_qty_{{$rand}}"
                                                 data-minimum-qty="#minimum_qty_{{$rand}}" data-stock-manage="0" data-product-stock="1"><i class="ti-plus"></i></button>
                                            <button class="cart-qty-minus qty_change" type="button" value="-" {{$data['qty']<2?'disabled':'' }} id="qty_plus_btn_{{$rand}}" data-value="-" data-id="{{$seller->id}}" data-product-id="{{$product->id}}"
                                                data-qty="#qty_{{$rand}}" data-qty-plus-btn-id="#qty_plus_btn_{{$rand}}" data-qty-minus-btn-id="#qty_minus_btn_{{$rand}}" data-maximum-qty="#maximum_qty_{{$rand}}" data-minimum-qty="#minimum_qty_{{$rand}}"
                                                data-stock-manage="0" data-product-stock="1"><i class="ti-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="oridinal_prise d-flex align-items-center">
                                        <h6 class="m-0 mr-1">{{single_price($data['total_price'])}}</h6>
                                        <i class="ti-trash cart_item_delete_btn" id="delete_item_{{$rand}}" data-id="{{$seller->id}}" data-product_id="{{$product->id}}" data-unique_id="#delete_item_{{$rand}}"></i>
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $product = \Modules\Seller\Entities\SellerProductSKU::with('product.product')->where('id',$data['product_id'])->first();
                                $rand = rand(8888,1000000);
                                $cart_Id = $data['cart_id'];
                                $shipping_method = \Modules\Shipping\Entities\ShippingMethod::where('id',$data['shipping_method_id'])->first();
                            @endphp

                            <div class="cartv2_product_single d-flex mb_10">
                                <div class="cartv2_product_body_left d-flex align-items-center">
                                    <label class="cs_checkbox cartv2_check_box">
                                        <input type="checkbox" class="select_single_item_check item_check seller_item_{{$seller->id}}" {{$data['is_select'] == 1 ?'checked':''}} id="single_pro_{{$seller->id}}_{{$data['product_id']}}" data-product_type="{{$data['product_type']}}" data-product_id="{{$data['product_id']}}" data-unique_id="#single_pro_{{$seller->id}}_{{$data['product_id']}}">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div class="thumb">
                                        <img src="
                                        @if(@$product->product->product->product_type == 1)
                                            {{asset(asset_path(@$product->product->product->thumbnail_image_source))}}
                                        @else
                                            {{asset(asset_path(@$product->sku->variant_image?@$product->sku->variant_image:@$product->product->product->thumbnail_image_source))}}
                                        @endif
                                        " alt="">
                                    </div>
                                    <div class="product_info">
                                        <h5><a href="{{route('frontend.item.show',$product->product->slug)}}">{{$product->product->product->product_name}}</a></h5>
                                        <p>
                                            @if($product->product->product->product_type == 2)

                                                    @foreach(@$product->product_variations as $key => $combination)
                                                        @if(@$combination->attribute->name == 'Color')
                                                            {{@$combination->attribute->name}}: {{@$combination->attribute_value->color->name}}
                                                         @else
                                                            {{@$combination->attribute->name}}: {{@$combination->attribute_value->value}}
                                                        @endif
                                                        @if($key < count(@$product->product_variations)-1),@endif

                                                    @endforeach

                                                @endif
                                        </p>
                                        <span class="Shipping_text {{@$product->product->product->is_physical == 1?'shipping_show':''}}" data-id="#shipping_method_modal_{{$cart_Id}}"> <span>{{__('common.shipping')}}: {{single_price($shipping_method->cost)}} </span> via {{substr($shipping_method->method_name,0,11)}} @if(strlen($shipping_method->method_name) > 11)... @endif - {{$shipping_method->shipment_time}} <i class="ti-angle-right"></i></span>

                                    </div>
                                </div>
                                <div class="cartv2_product_body_right d-flex align-items-center">
                                    <div class="prise_heiglite d-flex align-items-center gap_10">

                                        @if($product->product->hasDeal)
                                            @if($product->product->hasDeal->discount > 0)
                                                @if($product->product->hasDeal->discount_type == 0)
                                                    <span class="offer_prise">-{{$product->product->hasDeal->discount}} %</span>
                                                    <span class="curent_prise">{{single_price($product->selling_price)}}</span>
                                                @else

                                                    <span class="offer_prise">-{{single_price($product->product->hasDeal->discount)}}</span>
                                                    <span class="curent_prise">{{single_price($product->selling_price)}}</span>
                                                @endif
                                            @endif
                                        @else
                                            @if(@$product->product->hasDiscount == 'yes')
                                                @if($product->product->discount_type == 0)
                                                    <span class="offer_prise">-{{$product->product->discount}} %</span>
                                                    <span class="curent_prise">{{single_price($product->selling_price)}}</span>
                                                @else
                                                    <span class="offer_prise">-{{single_price($product->product->discount)}}</span>
                                                    <span class="curent_prise">{{single_price($product->selling_price)}}</span>
                                                @endif
                                            @else
                                                <span class="curent_prise price_rate">{{single_price($product->selling_price)}}</span>
                                            @endif
                                        @endif

                                    </div>
                                    <div class="product_count">
                                        <input type="hidden" id="maximum_qty_{{($product->product->product->max_order_qty) ? $product->product->product->max_order_qty : 0}}" value="{{($product->product->product->max_order_qty) ? $product->product->product->max_order_qty : 0}}">
                                        <input type="hidden" id="minimum_qty_{{$product->product->product->minimum_order_qty}}" value="{{$product->product->product->minimum_order_qty}}">

                                        <input type="text" name="qty" class="qty" maxlength="12" id="qty_{{$rand}}" value="{{$data['qty']}}" readonly>
                                        <div class="button-container">
                                            <button class="cart-qty-plus qty_change" type="button" value="+" id="qty_plus_btn_{{$rand}}" data-value="+" data-id="{{$seller->id}}" data-product-id="{{$product->id}}"
                                                data-qty="#qty_{{$rand}}" data-qty-plus-btn-id="#qty_plus_btn_{{$rand}}" data-qty-minus-btn-id="#qty_minus_btn_{{$rand}}"
                                                data-maximum-qty="#maximum_qty_{{($product->product->product->max_order_qty) ? $product->product->product->max_order_qty : 0}}"
                                                data-minimum-qty="#minimum_qty_{{$product->product->product->minimum_order_qty}}" data-stock-manage="{{$product->product->stock_manage}}" data-product-stock="{{$product->product_stock}}"><i class="ti-plus"></i></button>
                                            <button class="cart-qty-minus qty_change" type="button" value="-" {{$data['qty']<2?'disabled':'' }} id="qty_plus_btn_{{$rand}}" data-value="-" data-id="{{$seller->id}}" data-product-id="{{$product->id}}"
                                                data-qty="#qty_{{$rand}}" data-qty-plus-btn-id="#qty_plus_btn_{{$rand}}" data-qty-minus-btn-id="#qty_minus_btn_{{$rand}}" data-maximum-qty="#maximum_qty_{{$product->product->product->max_order_qty}}"
                                                data-minimum-qty="#minimum_qty_{{$product->product->product->minimum_order_qty}}" data-stock-manage="{{$product->product->stock_manage}}" data-product-stock="{{$product->product_stock}}"><i class="ti-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="oridinal_prise d-flex align-items-center">
                                        <h6 class="m-0 mr-1">{{single_price($data['total_price'])}}</h6>
                                        <i class="ti-trash cart_item_delete_btn" id="delete_item_{{$rand}}" data-id="{{$seller->id}}" data-product_id="{{$product->id}}" data-unique_id="#delete_item_{{$rand}}"></i>
                                    </div>
                                </div>
                            </div>
                            @if($product->product->product->is_physical == 1)
                                @include('frontend.default.partials._cart_shipping_method', ['data' => $data, 'shipping_methods' => @$product->product->product->shippingMethods])
                            @endif
                        @endif
                    @endif

                @endforeach
            </div>
        </div>

        @endforeach

    </div>
    <div class="col-xl-4">
        <div class="cartv2_subtotal  bg-white">
            <div class="subtotal_title d-flex align-items-center justify-content-between mb_10">
                <h5 class="f_s_16 f_w_600 m-0">{{ __('common.subtotal') }}</h5>
                <p class="f_s_14 f_w_400 m-0">+ {{single_price($subtotal)}}</p>
            </div>
            <div class="subtotal_title d-flex align-items-center justify-content-between mb_10">
                <h5 class="f_s_16 f_w_600 m-0">{{ __('defaultTheme.shipping') }}
                    <span class="shipping_package">{{ __('defaultTheme.package_wise_shipping_charge') }}</span>
                </h5>
                <p class="f_s_14 f_w_400 m-0">+ {{single_price($shipping_costs)}}</p>
            </div>

            <div class="subtotal__inc d-flex align-items-center justify-content-between mb_25">
                @php
                    $grand_total = $subtotal + $shipping_costs;
                @endphp
                <h5 class="f_s_14 f_w_600 m-0">{{__('common.grand_total')}}</h5>
                <h5 class="f_s_14 f_w_600 m-0">+ {{single_price($grand_total)}}</h5>
            </div>
            <div class="agree_check d-flex align-items-center gap_10 mb_25">
                <label class="cs_checkbox cartv2_check_box">
                    <input type="checkbox" id="term_check" checked>
                    <span class="checkmark"></span>
                </label>
                <p class="f_s_14 f_w_400">{{__('defaultTheme.I agree with the terms and conditions')}}</p>
            </div>
            <a class="btn_1 d-block text-center w-100 m-0 @if (count($cartData) > 0) process_to_checkout_check @endif" data-value="{{$selected_product_check}}">+ {{__('defaultTheme.proceed_to_checkout')}}</a>
        </div>
    </div>
    @else

    <div class="col-lg-12 text-center">
        <span class="product_not_found">{{ __('defaultTheme.no_product_found') }}</span>
    </div>

    @endif
</div>

