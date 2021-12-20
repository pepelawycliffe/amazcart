<?php
namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Support\Facades\Session;
use Modules\Customer\Entities\CustomerAddress;
use Modules\GiftCard\Entities\GiftCard;
use Modules\Seller\Entities\SellerProductSKU;
use Modules\Setup\Entities\Country;


class CheckoutRepository{

    public function __construct(){

    }
    public function getCartItem()
    {
        if(!auth()->check()){
            $carts = Session::get('cart', collect());


            $product_ids = $carts->where('product_type', 'product')->pluck('product_id')->toArray();
            $product_ids = SellerProductSKU::where('status', 1)->whereIn('id', $product_ids)->whereHas('product', function($query){
                    return $query->where('status', 1)->activeSeller();
            })->pluck('id')->toArray();

            $giftcard_ids = $carts->where('product_type', 'gift_card')->pluck('product_id')->toArray();
            $giftcard_ids = GiftCard::where('status', 1)->whereIn('id', $giftcard_ids)->pluck('id')->toArray();

            $cart_product = $carts->where('product_type', 'product')->whereIn('product_id', $product_ids);
            $cart_giftcard = $carts->where('product_type', 'gift_card')->whereIn('product_id', $giftcard_ids);

            $carts  = $cart_product->merge($cart_giftcard);

            $recs = new \Illuminate\Database\Eloquent\Collection($carts);

            $grouped = $recs->where('is_select',1)->groupBy('seller_id')->transform(function($item, $k) {
                return $item->groupBy('shipping_method_id');
            });
        }else {

            $query =  Cart::where('user_id',auth()->user()->id)->where('is_select',1)->where('product_type', 'product')->whereHas('product', function($query){
                return $query->where('status', 1)->whereHas('product', function($q){
                    return $q->where('status', 1)->activeSeller();
                });
            })->orWhere('product_type', 'gift_card')->whereHas('giftCard', function($query){
                return $query->where('status', 1);
            })->get();
            
            $recs = new \Illuminate\Database\Eloquent\Collection($query);
            $grouped = $recs->groupBy('seller_id')->transform(function($item, $k) {
                return $item->groupBy('shipping_method_id');
            });
        }
        $group['gift_card_exist'] = count($recs->where('product_type', 'gift_card')->where('is_select', 1));
        $group['cartData'] = $grouped;
        return $group;
    }

    public function deleteProduct($data){
        $product = Cart::where('user_id',auth()->user()->id)->where('id',$data['id'])->firstOrFail();
        return $product->delete();
    }

    public function addressStore($data){
        return CustomerAddress::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone_number'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'postal_code' => $data['postal_code'],
            'is_shipping_default' => $data['form'] =='modal_form'?0:1,
            'is_billing_default' => $data['form'] =='modal_form'?0:1,
            'customer_id' => auth()->user()->id,
        ]);
    }

    public function guestAddressStore($data)
    {
        $cartData = [];
        $cartData['name'] = $data['name'];
        $cartData['email'] = $data['email'];
        $cartData['phone'] = $data['phone_number'];
        $cartData['address'] = $data['address'];
        $cartData['city'] = $data['city'];
        $cartData['state'] = $data['state'];
        $cartData['country'] = $data['country'];
        $cartData['postal_code'] = $data['postal_code'];
        $cartData['check_shipping_address'] = $data['check_shipping_address'];
        Session::put('billing_address', $cartData);
        if ($data['check_shipping_address'] == "different") {
            $cartDataShipping = [];
            $cartDataShipping['shipping_name'] = $data['shipping_address_name'];
            $cartDataShipping['shipping_email'] = $data['shipping_address_email'];
            $cartDataShipping['shipping_phone'] = $data['shipping_address_phone'];
            $cartDataShipping['shipping_address'] = $data['shipping_address_address'];
            $cartDataShipping['shipping_city'] = $data['shipping_address_city'];
            $cartDataShipping['shipping_state'] = $data['shipping_address_state'];
            $cartDataShipping['shipping_country'] = $data['shipping_address_country'];
            $cartDataShipping['shipping_postal_code'] = $data['shipping_address_postal_code'];
            Session::put('shipping_address', $cartDataShipping);
        }
    }

    public function shippingAddressChange($data){
        $address = CustomerAddress::where('customer_id',auth()->user()->id)->where('is_shipping_default',1)->firstOrFail();
        $address->update([
            'is_shipping_default' => 0
        ]);
        CustomerAddress::findOrFail($data['id'])->update([
            'is_shipping_default' => 1
        ]);
        return true;
    }
    public function billingAddressChange($data){
        $address = CustomerAddress::where('customer_id',auth()->user()->id)->where('is_billing_default',1)->firstOrFail();
        $address->update([
            'is_billing_default' => 0
        ]);
        CustomerAddress::findOrFail($data['id'])->update([
            'is_billing_default' => 1
        ]);
        return true;
    }

    public function emailChange($data){
        return CustomerAddress::findOrFail($data['id'])->update([
            'email' =>$data['email']
        ]);
    }
    public function phoneChange($data){
        return CustomerAddress::findOrFail($data['id'])->update([
            'phone' =>$data['phone']
        ]);
    }

    public function emailChangeGuest($data)
    {
        if (session()->get('billing_address')['check_shipping_address'] == "different") {
            $cartDataShipping = [];
            $cartDataShipping['shipping_name'] = Session::get('shipping_address')['shipping_name'];
            $cartDataShipping['shipping_email'] = $data['email'];
            $cartDataShipping['shipping_phone'] = Session::get('shipping_address')['shipping_phone'];
            $cartDataShipping['shipping_address'] = Session::get('shipping_address')['shipping_address'];
            $cartDataShipping['shipping_city'] = Session::get('shipping_address')['shipping_city'];
            $cartDataShipping['shipping_state'] = Session::get('shipping_address')['shipping_state'];
            $cartDataShipping['shipping_country'] = Session::get('shipping_address')['shipping_country'];
            $cartDataShipping['shipping_postal_code'] = Session::get('shipping_address')['shipping_postal_code'];
            Session::put('shipping_address', $cartDataShipping);
        }
    }
    public function phoneChangeGuest($data)
    {
        if (session()->get('billing_address')['check_shipping_address'] == "different") {
            $cartDataShipping = [];
            $cartDataShipping['shipping_name'] = Session::get('shipping_address')['shipping_name'];
            $cartDataShipping['shipping_email'] = Session::get('shipping_address')['shipping_email'];
            $cartDataShipping['shipping_phone'] = $data['phone'];
            $cartDataShipping['shipping_address'] = Session::get('shipping_address')['shipping_address'];
            $cartDataShipping['shipping_city'] = Session::get('shipping_address')['shipping_city'];
            $cartDataShipping['shipping_state'] = Session::get('shipping_address')['shipping_state'];
            $cartDataShipping['shipping_country'] = Session::get('shipping_address')['shipping_country'];
            $cartDataShipping['shipping_postal_code'] = Session::get('shipping_address')['shipping_postal_code'];
            Session::put('shipping_address', $cartDataShipping);
        }
    }

    public function getCountries(){

        return Country::where('status', 1)->orderBy('name')->get();
    }
}
