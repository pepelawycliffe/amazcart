<?php
namespace App\Repositories;

use App\Models\Wishlist;
use Modules\Seller\Entities\SellerProductSKU;
use Modules\Seller\Entities\SellerProduct;
use Session;

use function Clue\StreamFilter\fun;
use DB;

class WishlistRepository
{
    public function myWishlist($user_id)
    {
        if (auth()->user()->role_id != 4)
        {
            return Wishlist::with('user', 'seller', 'product', 'product.product')->whereHas('product', function($query){
                $query->where('status', 1)->whereHas('product', function($query){
                    $query->where('status', 1);
                });
            })->where('user_id',$user_id)->paginate(12);
        }else {
            return Wishlist::with('user', 'seller', 'product', 'product.product')->whereHas('product', function($query){
                $query->where('status', 1)->whereHas('product', function($query){
                    $query->where('status', 1);
                });
            })->where('user_id',$user_id)->paginate(6);
        }
    }

    public function myWishlistWithPaginate($data){
        if($data['paginate']){
            $paginate = $data['paginate'];
        }else{
            $paginate = (auth()->user()->role_id != 4) ? 12 : 6;
        }
        if($data['sort_by'] == 'new'){
            return Wishlist::where('user_id',auth()->user()->id)->whereHas('product', function($query){
                $query->where('status', 1)->whereHas('product', function($query){
                    $query->where('status', 1);
                });
            })->orderBy('id')->paginate($paginate);
        }
        elseif($data['sort_by'] == 'old'){
            return Wishlist::where('user_id',auth()->user()->id)->whereHas('product', function($query){
                $query->where('status', 1)->whereHas('product', function($query){
                    $query->where('status', 1);
                });
            })->orderBy('id', 'DESC')->paginate($paginate);
        }
        elseif($data['sort_by'] == 'low_to_high'){
            return Wishlist::where('user_id',auth()->user()->id)->whereHas('product', function($query){
                return $query->where('status', 1)->whereHas('product', function($query){
                    $query->where('status', 1);
                })->orderBy('max_sell_price');
            })->paginate($paginate);
        }
        elseif($data['sort_by'] == 'high_to_low'){
            return Wishlist::where('user_id',auth()->user()->id)->whereHas('product', function($query){
                return $query->where('status', 1)->whereHas('product', function($query){
                    $query->where('status', 1);
                })->orderBy('max_sell_price', 'DESC');
            })->paginate($paginate);
        }

        return Wishlist::with('user', 'seller', 'product')->where('user_id',auth()->user()->id)->paginate($paginate);

    }

    public function store(array $data, $customer)
    {
        if($customer){

            $product = Wishlist::where('user_id',$customer->id)->where('type', $data['type'])->where('seller_product_id',$data['seller_product_id'])->first();
            
            if($product){
                
                return 3;
            }else{
                Wishlist::create([
                    'user_id' => $customer->id,
                    'seller_id' => $data['seller_id'],
                    'type' => $data['type'],
                    'seller_product_id' => $data['seller_product_id']
                ]);
                return 1;
            }

        }
    }

    public function remove($id, $user_id)
    {
        $product =  Wishlist::where('user_id', $user_id)->where('id', $id)->first();
        if($product){
            $product->delete();
            return true;
        }else{
            return false;
        }
    }

    public function myWishlistAPI($user_id){

        return Wishlist::with('user', 'seller','giftcard', 'product', 'product.product')->where('user_id',$user_id)->get()->groupBy('seller_id');

    }

    public function getCustomerWishlistForAPI($user_id){
        return Wishlist::with('user', 'seller', 'product', 'product.product')->where('user_id',$user_id)->get();
    }

    public function totalWishlistItem($user_id){
        return count(Wishlist::with('user', 'seller', 'product', 'product.product')->where('user_id',$user_id)->pluck('id'));
    }
}
