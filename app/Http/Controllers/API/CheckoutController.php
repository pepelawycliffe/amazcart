<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\GST\Entities\GstTax;
use Modules\Marketing\Entities\Coupon;
use Modules\Marketing\Entities\CouponProduct;
use Modules\Marketing\Entities\CouponUse;

class CheckoutController extends Controller
{
    // Checkout product list

    public function list(Request $request){

        $query = Cart::where('user_id', $request->user()->id)->where('product_type', 'product')->whereHas('product', function($query){
            return $query->where('status', 1)->whereHas('product', function($q){
                return $q->where('status', 1)->activeSeller();
            });
        })->orWhere('product_type', 'gift_card')->whereHas('giftCard', function($query){
            return $query->where('status', 1);
        });
        $query = $query->with('shippingMethod', 'seller.SellerBusinessInformation', 'customer.customerShippingAddress', 'giftCard', 'product.product.product', 'product.sku', 'product.product_variations.attribute', 'product.product_variations.attribute_value.color')->where('is_select', 1)->get();
        $same_state_gst_list = GstTax::whereIn('id', app('gst_config')['within_a_single_state'])->get();
        $differant_state_gst_list = GstTax::whereIn('id', app('gst_config')['between_two_different_states_or_a_state_and_a_Union_Territory'])->get();
        $flat_gst = GstTax::where('id', app('gst_config')['flat_tax_id'])->first();
        $is_gst_enable = 0;
        $is_gst_module_enable = 0;
        if(file_exists(base_path().'/Modules/GST/')){
            $is_gst_module_enable = 1;
        }
        if(app('gst_config')['enable_gst'] == "gst"){
            $is_gst_enable = 1;
        }
        if(count($query) > 0){
            $recs = new \Illuminate\Database\Eloquent\Collection($query);
            $cartItems = $recs->groupBy('seller_id')->transform(function($item, $k) {
                return $item->groupBy('shipping_method_id');
            });

            return response()->json([
                'items' => $cartItems,
                'same_state_gst_list' => $same_state_gst_list,
                'differant_state_gst_list' => $differant_state_gst_list,
                'flat_gst' => $flat_gst,
                'is_gst_enable' => $is_gst_enable,
                'is_gst_module_enable' => $is_gst_module_enable,
                'message' => 'success'
            ]);
        }else{
            return response()->json([
                'message' => 'select cart item first'
            ], 404);
        }

    }

    // Checkout coupon apply

    public function couponApply(Request $request){

        $request->validate([
            'coupon_code' => 'required',
            'shopping_amount' => 'required',
        ]);

        $coupon = Coupon::with('products.product')->where('coupon_code',$request->coupon_code)->first();

        if(isset($coupon)){
            if(date('Y-m-d')>=$coupon->start_date && date('Y-m-d')<=$coupon->end_date){
                if($coupon->is_multiple_buy){
                    if($coupon->coupon_type == 1){
                        $carts = Cart::where('user_id',$request->user()->id)->where('is_select',1)->pluck('product_id');
                        $products = CouponProduct::whereHas('product',function($query) use($carts){
                            return $query->whereHas('skus', function($q) use($carts){
                                return $q->whereIn('id', $carts);
                            });
                        })->pluck('product_id');

                        if(count($products) > 0){
                            return response()->json([
                                'coupon' =>$coupon,
                                'message' => 'success'
                            ]);

                        }else{
                            return response()->json([
                                'error' => 'This Coupon is not available for selected products'
                            ]);
                        }

                    }elseif($coupon->coupon_type == 2){
                        if($request->shopping_amount < $coupon->minimum_shopping){
                            return response()->json([
                                'error' => 'You Have more purchase to get This Coupon.'
                            ]);
                        }else{
                            return response()->json([
                                'coupon' =>$coupon,
                                'message' => 'success'
                            ]);
                        }
                    }elseif($coupon->coupon_type == 3){
                        return response()->json([
                            'coupon' =>$coupon,
                            'message' => 'success'
                        ]);
                    }
                }else{
                    if(CouponUse::where('user_id',$request->user()->id)->where('coupon_id',$coupon->id)->first() == null){
                        if($coupon->coupon_type == 1){
                            $carts = Cart::where('user_id',auth()->user()->id)->where('is_select',1)->pluck('product_id');
                            $products = CouponProduct::whereHas('product',function($query) use($carts){
                                return $query->whereHas('skus', function($q) use($carts){
                                    return $q->whereIn('id', $carts);
                                });
                            })->pluck('product_id');
                            if(count($products) > 0){
                                return response()->json([
                                    'coupon' =>$coupon,
                                    'message' => 'success'
                                ]);
                            }else{
                                return response()->json([
                                    'error' => 'This Coupon is not available for selected products'
                                ]);
                            }

                        }elseif($coupon->coupon_type == 2){
                            if($request->shopping_amount < $coupon->minimum_shopping){
                                return response()->json([
                                    'error' => 'You Have more purchase to get This Coupon.'
                                ]);
                            }else{
                                return response()->json([
                                    'coupon' =>$coupon,
                                    'message' => 'success'
                                ]);
                            }

                        }elseif($coupon->coupon_type == 3){
                            return response()->json([
                                'coupon' =>$coupon,
                                'message' => 'success'
                            ]);
                        }

                    }else{
                        return response()->json([
                            'error' => 'This coupon already used'
                        ]);
                    }
                }
            }else{
                return response()->json([
                    'error' => 'coupon is expired'
                ]);
            }
        }else{
            return response()->json([
                'error' => 'invalid Coupon'
            ]);
        }

    }

}
