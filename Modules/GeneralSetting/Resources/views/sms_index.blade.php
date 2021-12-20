@extends('backEnd.master')

@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="white_box_30px">
                            @include('generalsetting::page_components.sms_settings')
                        </div>
                        
                    </div>
                </div>
            </div>
        </section>

@endsection
