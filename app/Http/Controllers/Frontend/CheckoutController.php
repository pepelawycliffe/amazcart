<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponApplyRequest;
use App\Models\Cart;
use App\Services\CheckoutService;
use Brian2694\Toastr\Facades\Toastr;
use \Modules\PaymentGateway\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Marketing\Entities\Coupon;
use Modules\Marketing\Entities\CouponProduct;
use Modules\Marketing\Entities\CouponUse;
use Modules\Setup\Repositories\CityRepository;
use Modules\Setup\Repositories\StateRepository;
use Modules\UserActivityLog\Traits\LogActivity;

class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $paymentGatewayService;
    public function __construct(CheckoutService $checkoutService,PaymentGatewayService $paymentGatewayService)
    {
        $this->checkoutService = $checkoutService;
        $this->paymentGatewayService = $paymentGatewayService;
        $this->middleware('maintenance_mode');

    }

    public function index(Request $request)
    {
        $cartDataGroup = $this->checkoutService->getCartItem();
        $cartData = $cartDataGroup['cartData'];

        $giftCardExist = $cartDataGroup['gift_card_exist'];
        $customer = auth()->user();
        $countries = $this->checkoutService->getCountries();
        $states = (new StateRepository())->getByCountryId(app('general_setting')->default_country)->where('status', 1);
        $cities = (new CityRepository())->getByStateId(app('general_setting')->default_state)->where('status', 1);
        $gateway_activations = $this->paymentGatewayService->gateway_active();

        if(count($cartData) < 1){
            Toastr::warning(__('defaultTheme.please_product_select_from_cart_first'), __('common.warning'));
            return back();
        }

        if ($request->has('payment_id') && $request->has('gateway_id')) {
            $payment_id = decrypt($request->payment_id);
            $gateway_id = decrypt($request->gateway_id);
            return view(theme('pages.checkout'),compact('cartData','customer','gateway_activations', 'gateway_id', 'payment_id','countries', 'giftCardExist', 'states', 'cities'));
        }
        return view(theme('pages.checkout'),compact('cartData','customer','gateway_activations','countries', 'giftCardExist', 'states', 'cities'));
    }

    public function destroy(Request $request){
        $this->checkoutService->deleteProduct($request->except('_token'));
        LogActivity::successLog('product delete by checkout successful.');
        return $this->reloadWithData();
    }

    public function addressStore(Request $request){


        $request->validate([
            'name' => 'required',
            'email' => 'required|email|max:255|unique:customer_addresses',
            'phone_number' => 'required|max:255|unique:customer_addresses,phone',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
        ]);

        $this->checkoutService->addressStore($request->except('_token'));
        LogActivity::successLog('address store successful.');
        return $this->reloadWithData();
    }

    public function shippingAddressChange(Request $request){

        $this->checkoutService->shippingAddressChange($request->except('_token'));
        LogActivity::successLog('Shipping address change successful.');
        return $this->reloadWithData();
    }
    public function billingAddressChange(Request $request){

        $this->checkoutService->billingAddressChange($request->except('_token'));
        LogActivity::successLog('Billing address change successful.');
        return $this->reloadWithData();
    }
    public function emailChange(Request $request){
        $request->validate([
            'email' =>'required|email|max:255|unique:customer_addresses,email,'.$request->id,
        ]);
        $this->checkoutService->emailChange($request->except('_token'));
        LogActivity::successLog('Email change successful.');
        return $this->reloadWithData();
    }
    public function phoneChange(Request $request){
        $request->validate([
            'phone' =>'required|max:255|unique:customer_addresses,phone,'.$request->id,
        ]);
        $this->checkoutService->phoneChange($request->except('_token'));
        LogActivity::successLog('Phone change successful.');
        return $this->reloadWithData();
    }

    public function couponApply(CouponApplyRequest $request){

        $coupon = Coupon::where('coupon_code',$request->coupon_code)->first();

        if(isset($coupon)){
            if(date('Y-m-d')>=$coupon->start_date && date('Y-m-d')<=$coupon->end_date){
                if($coupon->is_multiple_buy){
                    if($coupon->coupon_type == 1){
                        $carts = Cart::where('user_id',auth()->user()->id)->where('is_select',1)->pluck('product_id');

                        $products = CouponProduct::where('coupon_id', $coupon->id)->whereHas('product',function($query) use($carts){
                            return $query->whereHas('skus', function($q) use($carts){
                                return $q->whereIn('id', $carts);
                            });
                        })->pluck('product_id');
                        if(count($products) > 0){
                            Session::put('coupon_type', $coupon->coupon_type);
                            Session::put('coupon_discount', $coupon->discount);
                            Session::put('coupon_discount_type', $coupon->discount_type);
                            Session::put('coupon_products', $products);
                            Session::put('coupon_id', $coupon->id);
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
                            Session::put('coupon_type', $coupon->coupon_type);
                            Session::put('coupon_discount', $coupon->discount);
                            Session::put('coupon_discount_type', $coupon->discount_type);
                            Session::put('maximum_discount', $coupon->maximum_discount);
                            Session::put('coupon_id', $coupon->id);
                        }
                    }elseif($coupon->coupon_type == 3){
                        Session::put('coupon_type', $coupon->coupon_type);
                        Session::put('coupon_discount', $coupon->discount);
                        Session::put('coupon_discount_type', $coupon->discount_type);
                        Session::put('maximum_discount', $coupon->maximum_discount);
                        Session::put('coupon_id', $coupon->id);
                    }
                }else{
                    if(CouponUse::where('user_id',auth()->user()->id)->where('coupon_id',$coupon->id)->first() == null){
                        if($coupon->coupon_type == 1){
                            $carts = Cart::where('user_id',auth()->user()->id)->where('is_select',1)->pluck('product_id');
                            $products = CouponProduct::where('coupon_id', $coupon->id)->whereHas('product',function($query) use($carts){
                                return $query->whereHas('skus', function($q) use($carts){
                                    return $q->whereIn('id', $carts);
                                });
                            })->pluck('product_id');

                            if(count($products) > 0){
                                Session::put('coupon_type', $coupon->coupon_type);
                                Session::put('coupon_discount', $coupon->discount);
                                Session::put('coupon_discount_type', $coupon->discount_type);
                                Session::put('coupon_products', $products);
                                Session::put('coupon_id', $coupon->id);
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
                                Session::put('coupon_type', $coupon->coupon_type);
                                Session::put('coupon_discount', $coupon->discount);
                                Session::put('coupon_discount_type', $coupon->discount_type);
                                Session::put('maximum_discount', $coupon->maximum_discount);
                                Session::put('coupon_id', $coupon->id);
                            }

                        }elseif($coupon->coupon_type == 3){
                            Session::put('coupon_type', $coupon->coupon_type);
                            Session::put('coupon_discount', $coupon->discount);
                            Session::put('coupon_discount_type', $coupon->discount_type);
                            Session::put('maximum_discount', $coupon->maximum_discount);
                            Session::put('coupon_id', $coupon->id);
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
        return $this->reloadWithData();

    }
    public function couponDelete(){
        Session::forget('coupon_type');
        Session::forget('coupon_discount');
        Session::forget('coupon_discount_type');
        Session::forget('maximum_discount');
        Session::forget('maximum_products');
        Session::forget('coupon_id');
        return $this->reloadWithData();
    }


    private function reloadWithData()
    {
        $cartDataGroup = $this->checkoutService->getCartItem();
        $cartData = $cartDataGroup['cartData'];
        $giftCardExist = $cartDataGroup['gift_card_exist'];
        $customer = (auth()->check())? auth()->user() : null;
        $gateway_activations = $this->paymentGatewayService->gateway_active();
        $countries = $this->checkoutService->getCountries();
        $states = (new StateRepository())->getByCountryId(app('general_setting')->default_country)->where('status', 1);
        $cities = (new CityRepository())->getByStateId(app('general_setting')->default_state)->where('status', 1);
        if ($customer != null) {
            return response()->json([
                'MainCheckout' =>  (string)view(theme('partials._checkout_details'),compact('cartData','giftCardExist','customer','gateway_activations','countries', 'states', 'cities')),
                'AddressesShipping' =>  (string)view(theme('partials._customer_address_list_shipping'),compact('customer','states', 'cities')),
                'AddressesBilling' =>  (string)view(theme('partials._customer_address_list_billing'),compact('customer','states', 'cities'))
            ]);
        }
        else {
            return response()->json([
                'MainCheckout' =>  (string)view(theme('partials._checkout_details'),compact('cartData','giftCardExist','customer','gateway_activations','countries', 'states', 'cities'))
            ]);
        }
    }

    public function guestAddressStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|max:30',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
            'shipping_address_name' => 'required_if:check_shipping_address,different',
            'shipping_address_email' => 'required_if:check_shipping_address,different',
            'shipping_address_phone' => 'required_if:check_shipping_address,different|max:30',
            'shipping_address_city' => 'required_if:check_shipping_address,different',
            'shipping_address_state' => 'required_if:check_shipping_address,different',
            'shipping_address_country' => 'required_if:check_shipping_address,different',
            'shipping_address_address' => 'required_if:check_shipping_address,different',
            'shipping_address_postal_code' => 'required_if:check_shipping_address,different',
        ]);

        try {
            $this->checkoutService->guestAddressStore($request->except('_token'));
            LogActivity::successLog('Guest address store successful.');
            return $this->reloadWithData();
        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());
            return $e;
        }

    }
    public function guestEmailChange(Request $request)
    {
        $request->validate([
            'email' =>'required|email|max:255',
        ]);
        $this->checkoutService->emailChangeGuest($request->except('_token'));
        LogActivity::successLog('Guest email change successful.');
        return $this->reloadWithData();
    }
    public function guestPhoneChange(Request $request)
    {
        $request->validate([
            'phone' =>'required|max:30',
        ]);
        $this->checkoutService->phoneChangeGuest($request->except('_token'));
        LogActivity::successLog('Guest phone change successful.');
        return $this->reloadWithData();
    }
}
