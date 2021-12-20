@extends('frontend.default.layouts.app')

@section('breadcrumb')
    {{ $contactContent->mainTitle }}
@endsection

@section('content')

    @include('frontend.default.partials._breadcrumb')
    <!-- contact info part here -->
    <section class="contact_info padding_top bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="contact_info_text">
                        <h4>{{ $contactContent->subTitle }}</h4>
                        @php echo $contactContent->description; @endphp
                        <a href="mailto:{{ $contactContent->email }}" class="contact_btn">{{ $contactContent->email }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact info part end -->

    <!-- send query part here -->
    <section class="send_query padding_top bg-white contact_form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <form id="contactForm" action="#" name="#" class="send_query_form">
                        <div class="form-group">
                            <label for="name">{{__('common.name')}} <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" placeholder="{{__('defaultTheme.enter_name')}}" class="form-control">
                            <span class="text-danger"  id="error_name"></span>
                        </div>

                        <div class="form-group">
                            <label for="email">{{__('defaultTheme.email_address')}} <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" placeholder="{{__('defaultTheme.enter_email_address')}}" class="form-control">
                            <span class="text-danger"  id="error_email"></span>
                        </div>
                        <div class="form-group">
                            <label for="query_type">{{__('defaultTheme.inquery_type')}} <span class="text-danger">*</span></label>
                            <select name="query_type" id="query_type" class="form-control nc_select">
                                @foreach($QueryList as $key => $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>

                        </div>
                        <span class="text-danger"  id="error_query_type"></span>
                        <div class="form-group">
                            <label for="textarea">{{__('defaultTheme.message')}} <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" placeholder="{{__('defaultTheme.write_messages')}}"></textarea>
                            <span class="text-danger"  id="error_message"></span>
                        </div>
                        <div class="send_query_btn">
                            <button id="contactBtn" type="submit" class="btn_1">{{__('defaultTheme.send_message')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- send query part end -->


@endsection
@push('scripts')
<script>

    (function($){
        "use strict";

        $(document).ready(function() {

            $('#contactForm').on('submit', function(event) {
                event.preventDefault();
                console.log('ok')
                $("#contactBtn").prop('disabled', true);
                $('#contactBtn').text('{{ __('common.submitting') }}');

                var formElement = $(this).serializeArray()
                var formData = new FormData();
                formElement.forEach(element => {
                    formData.append(element.name, element.value);
                });
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('contact.store') }}",
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        toastr.success("{{__('defaultTheme.message_sent_successfully')}}","{{__('common.success')}}");
                        $("#contactBtn").prop('disabled', false);
                        $('#contactBtn').text("{{ __('defaultTheme.send_message') }}");
                        resetErrorData();

                    },
                    error: function(response) {
                        toastr.error("{{__('common.error_message')}}", "{{__('common.error')}}");
                        $("#contactBtn").prop('disabled', false);
                        $('#contactBtn').text("{{ __('defaultTheme.send_message') }}");
                        showErrorData(response.responseJSON.errors)

                    }
                });
            });

            function showErrorData(errors){
                $('#contactForm #error_name').text(errors.name);
                $('#contactForm #error_email').text(errors.email);
                $('#contactForm #error_query_type').text(errors.query_type);
                $('#contactForm #error_message').text(errors.message);
            }

            function resetErrorData(){
                $('#contactForm')[0].reset();
                $('#contactForm #error_name').text('');
                $('#contactForm #error_email').text('');
                $('#contactForm #error_query_type').text('');
                $('#contactForm #error_message').text('');
            }
        });
    })(jQuery);


</script>
@endpush
