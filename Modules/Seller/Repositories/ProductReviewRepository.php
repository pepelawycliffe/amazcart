<?php
namespace Modules\Seller\Repositories;

use Modules\Review\Entities\ProductReview;
use Modules\Review\Entities\ReviewReply;
use App\Models\User;

class ProductReviewRepository {

    public function getAll()
    {
        if(auth()->check() && auth()->user()->role_id == 6)
        {
            return ProductReview::where('seller_id',auth()->user()->sub_seller->seller_id)->where('status', 1);
        }elseif (auth()->check() && auth()->user()->role_id == 5) {
            return ProductReview::where('seller_id',auth()->user()->id)->where('status', 1);
        }elseif (auth()->check() && (auth()->user()->role->type == "admin" || auth()->user()->role->type == "staff")) {
            return ProductReview::where('seller_id',User::first()->id)->where('status', 1);
        }else{
            return abort(404);
        }
    }

    public function getById($id){
        return ProductReview::findOrFail($id);
    }
    public function reviewStore($data){
        return ReviewReply::create([
            'review_id' => $data['review_id'],
            'review' => $data['review'],
            'status' => 1
        ]);
    }
}
