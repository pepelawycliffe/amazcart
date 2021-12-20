<p><strong>{{ $review->review }}</strong></p>
<div class="row mt-10">

    <div class="product_img_div">
        <img class="product_img" src="
            @if($review->type == 'product')
            {{ asset(asset_path(@$review->product->thum_img?@$review->product->thum_img:$review->product->product->thumbnail_image_source)) }}
            @else
            {{ asset(asset_path(@$review->giftcard->thumbnail_image)) }}
            @endif
        " alt="">
    </div>
    <p>{{($review->type == 'product')?$review->product->product_name:$review->giftcard->name}}</p>
</div>
