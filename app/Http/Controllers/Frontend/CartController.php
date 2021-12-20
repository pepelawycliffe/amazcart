<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Session;
use Modules\UserActivityLog\Traits\LogActivity;

class CartController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->middleware('maintenance_mode');

    }

    public function index()
    {
        try{
            $Data = $this->cartService->getCartData();
            $cartData = $Data['cartData'];
            $shipping_costs = $Data['shipping_charge'];
            return view(theme('pages.cart'),compact('cartData','shipping_costs'));
        }catch(Exception $e){
            LogActivity::errorLog($e->getMessage());

            return $e;
        }
    }

    public function store(Request $request){
        $result = $this->cartService->store($request->except('_token'));

        if($result == 'out_of_stock'){
            return 'out_of_stock';
        }else{
            $carts = collect();
            if(auth()->check()){
                $carts = \App\Models\Cart::where('user_id',auth()->user()->id)->get();
            }else {
                if (Session::has('cart')) {
                    $carts = Session::get('cart');
                }
            }
            LogActivity::successLog('cart store successful.');
            return view(theme('partials._cart_details_submenu'),compact('carts'));
        }

    }

    public function updateCartShippingInfo(Request $request)
    {
        try {
            $this->cartService->updateCartShippingInfo($request->except('_token'));
            LogActivity::successLog('update cart info successful.');
            return $this->reloadWithData();
        } catch (\Exception $e) {
            LogActivity::errorLog($e->getMessage());

            return $e;
        }

    }


    public function updateQty(Request $request)
    {
        $this->cartService->updateQty($request->except('_token'));
        LogActivity::successLog('update cart qty successful.');
        return $this->reloadWithData();
    }

    public function selectAll(Request $request)
    {
        $this->cartService->selectAll($request->except('_token'));
        $Data = $this->cartService->getCartData();
        $cartData = $Data['cartData'];
        $shipping_costs = $Data['shipping_charge'];
        return view(theme('partials._cart_details'),compact('cartData','shipping_costs'));
    }
    public function selectAllSeller(Request $request)
    {

        $this->cartService->selectAllSeller($request->except('_token'));
        $Data = $this->cartService->getCartData();
        $cartData = $Data['cartData'];
        $shipping_costs = $Data['shipping_charge'];
        return view(theme('partials._cart_details'),compact('cartData','shipping_costs'));
    }
    public function selectItem(Request $request)
    {
        $this->cartService->selectItem($request->except('_token'));
        $Data = $this->cartService->getCartData();
        $cartData = $Data['cartData'];
        $shipping_costs = $Data['shipping_charge'];
        return view(theme('partials._cart_details'),compact('cartData','shipping_costs'));

    }

    public function destroy(Request $request)
    {

        $this->cartService->deleteCartProduct($request->except('_token'));
        LogActivity::successLog('delete cart successful.');

        return $this->reloadWithData();

    }
    public function destroyAll(Request $request)
    {

        $this->cartService->deleteAll();
        LogActivity::successLog('delete all cart successful.');

        return $this->reloadWithData();
    }

    private function reloadWithData(){

        $Data = $this->cartService->getCartData();
        $cartData = $Data['cartData'];
        $shipping_costs = $Data['shipping_charge'];
        $carts = collect();

        if(auth()->check()){
            $carts = \App\Models\Cart::where('user_id',auth()->user()->id)->get();
        }else {
            if (Session::has('cart')) {
                $carts = Session::get('cart');
            }
        }
        return response()->json([
            'MainCart' =>  (string)view(theme('partials._cart_details'),compact('cartData','shipping_costs')),
            'SubmenuCart' =>  (string)view(theme('partials._cart_details_submenu'),compact('carts'))
        ]);
    }

}
