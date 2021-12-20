<?php
namespace App\Repositories;

use App\Models\Cart;
use Modules\Seller\Entities\SellerProductSKU;
use Modules\Shipping\Entities\ShippingMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Modules\GiftCard\Entities\GiftCard;

class CartRepository{

    protected $cart;

    public function __construct(Cart $cart){
        $this->cart = $cart;
    }

    public function store($data){
        $customer = auth()->user();
        $total_price = $data['price']*$data['qty'];
        $is_out_of_stock = 0;
        if($customer){
            $product = $this->cart::where('user_id',$customer->id)->where('product_id',$data['product_id'])->where('seller_id', $data['seller_id'])->where('product_type',$data['type'])->first();

        
            if($data['type'] == 'product' && $product){
                $sku = SellerProductSKU::where('id', $data['product_id'])->first();
                if($sku->product_stock <= $sku->product->product->minimum_order_qty && $sku->product->stock_manage == 1){
                    $is_out_of_stock = 1;
                }
            }

            if($is_out_of_stock == 0){
                if($product){
                    $product->update([
                        'qty' => $product->qty+$data['qty'],
                        'total_price' => $product->total_price + $total_price
                    ]);
                }else{
                    $this->cart::create([
                        'user_id' => $customer->id,
                        'product_type' => ($data['type'] == 'gift_card') ? 'gift_card' : 'product',
                        'product_id' => $data['product_id'],
                        'price' => $data['price'],
                        'qty' => $data['qty'],
                        'total_price' => $total_price,
                        'seller_id' => $data['seller_id'],
                        'shipping_method_id' => $data['shipping_method_id'],
                        'sku' => null,
                        'is_select' => 1
                    ]);
                }
            }else{
                return 'out_of_stock';
            }

        }else{

            $cartData = [];
            $cartData['product_type'] = ($data['type'] == 'gift_card') ? 'gift_card' : 'product';
            $cartData['product_id'] = intval($data['product_id']);
            $cartData['cart_id'] = rand(1111,1000000).Str::random(40);
            $cartData['price'] = intval($data['price']);
            $cartData['qty'] = intval($data['qty']);
            $cartData['total_price'] = $data['price']*$data['qty'];
            $cartData['seller_id'] = intval($data['seller_id']);
            $cartData['shipping_method_id'] = intval($data['shipping_method_id']);
            $cartData['sku'] = null;
            $cartData['is_select'] = 1;

            if(Session::has('cart')){
                $foundInCart = false;
                $cart = collect();

                foreach (Session::get('cart') as $key => $cartItem){
                    if($cartItem['product_id'] == $data['product_id']){
                        if($data['type'] == 'product'){
                            $sku = SellerProductSKU::where('id', $data['product_id'])->first();
                            if($sku->product_stock <= $sku->product->product->minimum_order_qty && $sku->product->stock_manage == 1){
                                $is_out_of_stock = 1;
                            }
                        }
                    }

                    if($is_out_of_stock == 0){
                        if($cartItem['product_id'] == $data['product_id'] && $cartItem['shipping_method_id'] == $data['shipping_method_id'] && $cartItem['product_type'] == $data['type'] && $cartItem['seller_id'] == $data['seller_id']){

                            $foundInCart = true;
                            $cartItem['qty'] += $cartData['qty'];
                            $cartItem['total_price'] +=$cartData['total_price'];
                        }
                        $cart->push($cartItem);
                    }else{
                        return 'out_of_stock';
                    }
                    
                }

                if (!$foundInCart) {
                    $cart->push($cartData);
                }
                Session::put('cart', $cart);
            }
            else{
                $cart = collect([$cartData]);
                Session::put('cart', $cart);
            }
        }
    }

    public function updateCartShippingInfo($data){
        if (auth()->check()) {
            $product =  $this->cart::findOrFail($data['cartId']);
            $product->update([
                'shipping_method_id' => $data['shipping_method_id']
            ]);
        }else {
            if(Session::has('cart')){
                $cart = session()->get('cart', collect([]));
                $cart = $cart->map(function ($object, $key) use ($data) {
                    if($object['cart_id'] == $data['cartId']){
                        $object['shipping_method_id'] = intval($data['shipping_method_id']);
                    }
                    return $object;
                });
                Session::put('cart', $cart);
            }
        }
    }

    public function getCartData(){
        if(auth()->check()){
            $cart_ids = $this->cart::where('user_id',auth()->user()->id)->where('product_type', 'product')->whereHas('product', function($query){
                return $query->where('status', 1)->whereHas('product', function($q){
                    return $q->where('status', 1)->activeSeller();
                });
            })->orWhere('user_id',auth()->user()->id)->where('product_type', 'gift_card')->whereHas('giftCard', function($query){
                return $query->where('status', 1);
            })->pluck('id')->toArray();

            $cartData = $this->cart::whereIn('id',$cart_ids)->get()->groupBy('seller_id');
            $query = $this->cart::whereIn('id',$cart_ids)->where('is_select', 1)->get();
            $recs = new \Illuminate\Database\Eloquent\Collection($query);
            $grouped = $recs->groupBy('seller_id')->transform(function($item, $k) {
                return $item->groupBy('shipping_method_id');
            });

            $shipping_charge = 0;
            $method_shipping_cost = 0;
            $additional_charge = 0;
            foreach($grouped as $key => $group){
                foreach($group as $key=> $item){
                     $method_shipping_cost += $item[0]->shippingMethod->cost;
                     foreach($item as $key => $data){
                        if($data->product_type != "gift_card" && $data->product->sku->additional_shipping > 0){
                            $additional_charge +=  $data->product->sku->additional_shipping;
                        }
                     }
                }

            }
            $shipping_charge = $method_shipping_cost + $additional_charge;

            return [
                'shipping_charge' => $shipping_charge,
                'cartData' => $cartData
            ];

        }else{
            if(Session::has('cart')){
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
                $cartData = $this->group_by('seller_id',$carts);
                $query = $carts->where('is_select', 1);
                $recs = new \Illuminate\Database\Eloquent\Collection($query);
                $grouped = $recs->groupBy('seller_id')->transform(function($item, $k) {
                    return $item->groupBy('shipping_method_id');
                });

                $shipping_charge = 0;
                $method_shipping_cost = 0;
                $additional_charge = 0;
                foreach($grouped as $key => $group){

                    foreach($group as $key=> $item){
                        $shipping_method = ShippingMethod::where('id',$key)->first();

                        $method_shipping_cost += $shipping_method->cost;

                         foreach($item as $key => $data){
                            $product = SellerProductSKU::where('id',$data['product_id'])->first();
                            if($data['product_type'] != "gift_card" && $product->sku->additional_shipping > 0){
                                $additional_charge +=  $product->sku->additional_shipping;
                            }
                         }
                    }

                }
                $shipping_charge = $method_shipping_cost + $additional_charge;
                return [
                    'shipping_charge' => $shipping_charge,
                    'cartData' => isset($cartData)?$cartData:null
                ];

            }else{
                return [
                    'shipping_charge' => 0,
                    'cartData' => []
                ];
            }

        }

    }

    function group_by($key, $data) {
        $result = array();
        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }


    public function updateQty($data){
        if(auth()->check()){
            $product =  $this->cart::findOrFail($data['id']);
            $product->update([
                'qty' => $data['qty'],
                'total_price' => $product->price *$data['qty']
            ]);
        }else{
            if(Session::has('cart')){
                $cart = Session::get('cart', collect([]));
                $cart = $cart->map(function ($object, $key) use ($data) {
                    if($object['product_id'] == $data['p_id'] && $object['seller_id'] == $data['id']){

                        $object['qty'] = intval($data['qty']);
                        $object['total_price'] = $data['qty'] * $object['price'];
                    }
                    return $object;
                });

                Session::put('cart', $cart);
            }
        }
        return 1;
    }

    public function selectAll($data){
        if(auth()->check()){
            $carts = $this->cart::where('user_id',auth()->user()->id)->get();
            foreach($carts as $key => $cart){
                $cart->update([
                    'is_select' => intval($data['checked'])
                ]);
            }
        }else{
            if(Session::has('cart')){
                $carts = Session::get('cart', collect([]));
                $new_carts = collect();
                foreach($carts as $key => $cart){

                    $cart['is_select'] = intval($data['checked']);
                    $new_carts[] = $cart;
                }
                Session::put('cart', $new_carts);
            }
        }
        return 1;
    }
    public function selectAllSeller($data){
        if(auth()->check()){
            $carts = $this->cart::where('user_id',auth()->user()->id)->get();
            foreach($carts as $key => $cart){
                if($cart->seller_id == $data['seller_id']){
                    $cart->update([
                        'is_select' => intval($data['checked'])
                    ]);
                }
            }
        }else{
            if(Session::has('cart')){
                $carts = Session::get('cart', collect([]));
                $new_carts = collect();
                foreach($carts as $key => $cart){

                    if($cart['seller_id'] == $data['seller_id']){
                        $cart['is_select'] = intval($data['checked']);
                    }
                    $new_carts[] = $cart;
                }
                Session::put('cart', $new_carts);
            }
        }
        return 1;
    }
    public function selectItem($data){
        if(auth()->check()){
            $cart = $this->cart::where('user_id',auth()->user()->id)->where('product_id',$data['product_id'])->where('product_type', $data['product_type'])->firstorFail();

            $cart->update([
                'is_select' => intval($data['checked'])
            ]);
        }else{
            
            if(Session::has('cart')){
                $carts = Session::get('cart', collect([]));
                $new_carts = collect();
                foreach($carts as $key => $cart){

                    if($cart['product_id'] == $data['product_id'] && $cart['product_type'] == $data['product_type']){
                        $cart['is_select'] = intval($data['checked']);
                    }
                    $new_carts[] = $cart;
                }
                Session::put('cart', $new_carts);
            }
        }
        return 1;
    }

    public function deleteCartProduct($data){
        
        if(auth()->check()){
            return $this->cart::findOrFail($data['id'])->delete();
        }else{
            if(Session::has('cart')){
                $carts = Session::get('cart', collect());
                foreach($carts as $key => $cart){

                    if($cart['seller_id'] == $data['id'] && $cart['product_id'] == $data['p_id']){
                        $carts->forget($key);
                    }
                }
                Session::put('cart', $carts);
            }
        }
        return 1;
    }
    public function deleteAll(){
        if(auth()->check()){
            $carts = $this->cart::where('user_id',auth()->user()->id)->get();
            foreach($carts as $cart){
                $cart->delete();
            }
        }else{
            if(Session::has('cart')){
                Session::forget('cart');
            }
        }
        return 1;
    }
}
