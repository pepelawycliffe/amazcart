<form action="{{route('frontend.order.store')}}" method="POST" enctype="multipart/form-data" id="mainOrderForm">
    @csrf
    <div class="row">
        @if (auth()->check())
            @include(theme('partials.checkout_info_auth_user'))
        @else
            @include(theme('partials.checkout_info_guest_user'))
        @endif
        <input type="hidden" name="payment_id" id="payment_id" @isset($payment_id) value="{{ $payment_id }}" @else value="0" @endisset>
    </div>
</form>
