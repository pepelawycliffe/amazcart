
<div class="product_thumb_div">
    @if($skus->variant_image != null)
    <img src="{{ asset(asset_path($skus->variant_image)) }}"
            alt="{{ $skus->product->product_name }}">
    @elseif ($skus->product->thumbnail_image_source != null)
        <img src="{{ asset(asset_path($skus->product->thumbnail_image_source)) }}"
            alt="{{ $skus->product->product_name }}">
    @else
        <img src="{{ asset(asset_path('frontend/img/no_image.png')) }}"
            alt="{{ $skus->product->product_name }}">
    @endif

</div>
