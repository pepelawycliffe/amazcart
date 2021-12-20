<div class="dropdown CRM_dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false"> {{__('common.select')}}
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu2">
        @if (permissionCheck('customer.show_details'))
            <a href="{{route('customer.show_details',$customer->id)}}" class="dropdown-item" type="button">{{__('common.details')}}</a>
        @endif
    </div>
</div>
