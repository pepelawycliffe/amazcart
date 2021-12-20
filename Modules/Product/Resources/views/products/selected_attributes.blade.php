@foreach($attributes as $key => $id)
@php
    $attribute = \Modules\Product\Entities\Attribute::where('id',$id)->first();
@endphp
<div class="row">
    <div class="col-lg-4"><input type="hidden" name="choice_no[]" value="{{$id}}">
        <div class="primary_input mb-25"><input class="primary_input_field" width="40%" name="choice[]" type="text"
                value="{{$attribute->name}}" readonly></div>
    </div>
    <div class="col-lg-8">
        <div class="primary_input mb-25">
            <select name="choice_options_{{$id}}[]" id="choice_options" class="primary_select mb-15" multiple>
                @foreach(@$attribute->values as $key => $item)
                    @if($item->color)
                        <option value="{{$item->id}}">{{@$item->color->name}}</option>
                    @else
                        <option value="{{$item->id}}">{{$item->value}}</option>
                    @endif
                @endforeach
            </select>

        </div>
    </div>
</div>
@endforeach
