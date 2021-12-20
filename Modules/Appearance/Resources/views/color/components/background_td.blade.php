

<style>
    .background-color{
        height:50px;
        width:50px;
        background-color:{{ $data->background_color }}
    }

    .background-image{
        height:100px;
        width:100px;
    }

</style>
@if ($data->background_image)
    <img class="background-image" src="{{asset(asset_path($data->background_image))}}" alt="">
 @else
 <div class="background-color"></div>
<p>{{ $data->background_color }}</p>
@endif
