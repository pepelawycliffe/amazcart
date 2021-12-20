<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SearchTerm;
use App\Models\RecentViewProduct;
use Modules\Seller\Entities\SellerProduct;
use Modules\Product\Entities\Product;
use Modules\GiftCard\Entities\GiftCard;
use Modules\Product\Entities\Category;
use Carbon\Carbon;
use Modules\Product\Entities\CategoryProduct;
use Modules\Product\Entities\ProductTag;
use Modules\Review\Entities\ProductReview;
use Modules\Setup\Entities\Tag;

class ProductRepository
{

    protected $product;

    public function __construct(SellerProduct $product)
    {
        $this->product = $product;
    }

    public function getProductByID($id)
    {
        return $this->product::with('product', 'skus')->where('id', $id)->firstOrFail();
    }

    public function recentViewedProducts($product_ids)
    {
        return $this->product::with('product', 'skus')->whereIn('id', $product_ids)->latest()->get();
    }

    public function getProductBySlug($slug)
    {
        return $this->product::with('product', 'skus')->where('slug', $slug)->firstOrFail();
    }

    public function getActiveSellerProductBySlug($slug)
    {
        return $this->product::where('slug', $slug)->with('product', 'skus','seller')->activeSeller()->firstOrFail();
    }

    public function recentViewIncrease($id)
    {
        $sellerProduct = $this->getProductByID($id);
        return $sellerProduct->update([
            'recent_view' => Carbon::now(),
        ]);
    }

    public function recentViewStore($seller_product_id)
    {
        $total_products = RecentViewProduct::where('user_id', auth()->user()->id)->get();
        if (count($total_products) == app('recently_viewed_config')['max_limit']) {
            $old_product = RecentViewProduct::where('user_id', auth()->user()->id)->first()->delete();
        }
        $infoExist = RecentViewProduct::where('user_id', auth()->user()->id)->where('seller_product_id', $seller_product_id)->first();
        if ($infoExist) {
            return $infoExist->update([
                'viewed_at' => date('y-m-d')
            ]);
        } else {
            return RecentViewProduct::create([
                'user_id' => auth()->user()->id,
                'seller_product_id' => $seller_product_id,
                'viewed_at' => date('y-m-d')
            ]);
        }
    }

    public function lastRecentViewinfo()
    {
        return RecentViewProduct::where('user_id', auth()->user()->id)->latest()->get()->pluck('seller_product_id');
    }

    public function getReviewByPage($data)
    {
        return ProductReview::where('product_id', $data['product_id'])->latest()->paginate(10);
    }

    public function searchProduct($request)
    {
        $slugs = explode(' ', $request['keyword']);
            
        $tags = Tag::where(function($q) use ($slugs){
            foreach($slugs as $slug){
                $q = $q->orWhere('name', 'LIKE', "%{$slug}%");
            }
            return $q;
        });

        if ($request['cat_id'] != 0) {
            $productIds = CategoryProduct::where('category_id',$request['cat_id'])->pluck('product_id')->toArray();
 
            $tags = $tags->whereHas('products', function($q)use($productIds){
                return $q->whereIn('product_id',$productIds);
            });

         }
       
        return $tags->limit(6)->pluck('name');
    }
}
