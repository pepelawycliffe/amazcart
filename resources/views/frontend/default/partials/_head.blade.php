<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <title>{{app('general_setting')->site_title}}</title>

    @laravelPWA

    <link rel="icon" href="{{asset(asset_path(app('general_setting')->favicon))}}" type="image/png">

    @php
        $themeColor = Modules\Appearance\Entities\ThemeColor::where('status',1)->first();
    @endphp
    <style>
        :root {
            --background_color : {{ $themeColor->background_color }};
            --base_color : {{ $themeColor->base_color }};
            --text_color : {{ $themeColor->text_color }};
            --feature_color : {{ $themeColor->feature_color }};
            --footer_color : {{ $themeColor->footer_color }};
            --navbar_color : {{ $themeColor->navbar_color }};
            --menu_color : {{ $themeColor->menu_color }};
            --border_color : {{ $themeColor->border_color }};
            --success_color : {{ $themeColor->success_color }};
            --warning_color : {{ $themeColor->warning_color }};
            --danger_color : {{ $themeColor->danger_color }};
        }
        .default_select{
            color: var(--text_color);
            border-color: var(--border_color);
            width: 100%;
            font-weight: 300;
        }
    </style>

    @if(isRtl())
        <link rel="stylesheet" href="{{asset(asset_path('frontend/default/compile_css/rtl_app.css'))}}" />
    @else
        <link rel="stylesheet" href="{{asset(asset_path('frontend/default/compile_css/app.css'))}}" />
    @endif


    <style>
        
        .toast-success {
            background-color: {{ $themeColor->success_color }}!important;
        }
        .toast-error{
            background-color: {{ $themeColor->danger_color }}!important;
        }
        .toast-warning{
            background-color: {{ $themeColor->warning_color }}!important;
        }
            /*----------------------------------------------------*/

        
        .newsletter_form_wrapper .newsletter_form_inner .newsletter_form_thumb {
            height: 100%;
            background-image: url({{ asset(asset_path($popupContent->image)) }});
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

    </style>

    @if(!isRtl())
    <style>
        @media (max-width: 575px) {
            .header_part .sub_menu .right_sub_menu .cart_menu:hover .cart_iner {
                right: 60%;
            }
            .header_part .sub_menu .right_sub_menu .cart_menu .cart_iner {
                right: 60%;
            }

            .header_part .sub_menu .left_sub_menu .list_visiable .select_option_list {
                margin-left: -85px;
            }
            .header_part .sub_menu .left_sub_menu .select_option .select_option_list {
                margin-left: -85px;
            }
            .notifica_menu{
                right: 0%!important;
                padding: 40px 30px!important;
            }

            .header_part .sub_menu .right_sub_menu .cart_menu:hover .cart_iner {
                right: -65%;
            }
            .header_part .sub_menu .left_sub_menu .select_option .select_option_list {
                right: 0;
            }
            .header_part .sub_menu .right_sub_menu .cart_menu:hover .cart_iner.user_account_iner {
                right: auto;
                left: -130px

            }

            .header_part .sub_menu .right_sub_menu .cart_menu:hover .cart_iner.cart_for_inner {
                right: auto;
                left: -70px;
            }
            .header_part .sub_menu .right_sub_menu .cart_menu:hover .cart_iner.notifica_menu {
                right: auto !important;
                left: 0;
            }

        }

    </style>
        @guest
        <style>
            @media (max-width: 575px) {
                .user_account_iner{
                    right: 22%!important;
                }
            }

        </style>
        @endguest
    @else
        <style>
            .cart_data_img_div {
                margin-right: 0;
                margin-left: 15px;
            }
            @media (max-width: 575px) {
                .header_part .sub_menu .left_sub_menu .select_option .select_option_list {
                    margin-left: 0;
                    left: 0;
                    right: auto;
                }
                .rtl .header_part .sub_menu .right_sub_menu ul {
                    display: flex;
                    justify-content: center;
                    flex-wrap: wrap;
                }
                .rtl .header_part .sub_menu .right_sub_menu .user_account:hover .user_account_iner {
                    right: auto;
                    left: -50px !important;
                }
                .rtl .header_part .sub_menu .right_sub_menu .cart_menu:hover .cart_iner {
                    right: auto;
                    left: -120px;
                }
            }
            .logo_div{
                justify-content: right;
            }
        </style>
    @endif


    @section('styles')
        @show

    @if (app('business_settings')->where('type', 'google_analytics')->first()->status == 1)
          <!-- Global site tag (gtag.js) - Google Analytics -->
          <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('ANALYTICS_TRACKING_ID') }}"></script>

          <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '{{ env('ANALYTICS_TRACKING_ID') }}');
          </script>
    @endif

    @if (app('business_settings')->where('type', 'facebook_pixel')->first()->status == 1)
          <!-- Facebook Pixel Code -->
          <script>
              !function(f,b,e,v,n,t,s)
              {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
              n.callMethod.apply(n,arguments):n.queue.push(arguments)};
              if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
              n.queue=[];t=b.createElement(e);t.async=!0;
              t.src=v;s=b.getElementsByTagName(e)[0];
              s.parentNode.insertBefore(t,s)}(window, document,'script',
              'https://connect.facebook.net/en_US/fbevents.js');
              fbq('init', {{ Modules\Setup\Entities\AnalyticsTool::where('type', 'facebook_pixel')->first()->facebook_pixel_id }});
              fbq('track', 'PageView');
          </script>
          <noscript>
              <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ Modules\Setup\Entities\AnalyticsTool::where('type', 'facebook_pixel')->first()->facebook_pixel_id }}/&ev=PageView&noscript=1"/>
          </noscript>
          <!-- End Facebook Pixel Code -->
    @endif

      
  </head>
