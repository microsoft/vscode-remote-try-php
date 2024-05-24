<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->

<head>
    <base href="">

    <title>Newwave Sulution</title>
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="Keenthemes | Metronic" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="{{ asset('./icon.png') }}" />

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet"
          type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
          type="text/css" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .required-field {
            color: red;
            font-size: 18px;
        }
    </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body"
      class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
      style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
<div class="d-flex flex-column flex-root">
    <div class="page d-flex flex-row flex-column-fluid">
        @include('layouts.nav_left')
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <div id="kt_header" style="" class="header align-items-stretch">

                <div class="container-fluid d-flex align-items-stretch justify-content-between">
                    <!--begin::Aside mobile toggle-->
                    <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show aside menu">
                        <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                             id="kt_aside_mobile_toggle">
                            <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                            <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                         viewBox="0 0 24 24" fill="none">
                                        <path
                                            d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                            fill="currentColor" />
                                        <path opacity="0.3"
                                              d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                              fill="currentColor" />
                                    </svg>
                                </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        <a href="../../demo1/dist/index.html" class="d-lg-none">
                            <img alt="Logo" src="{{ asset('assets/media/logos/logo-2.svg') }}" class="h-30px" />
                        </a>
                    </div>
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                        <!--begin::Navbar-->
                        <div class="d-flex align-items-stretch" id="kt_header_nav">
                            <div class="header-menu align-items-stretch" data-kt-drawer="true"
                                 data-kt-drawer-name="header-menu"
                                 data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                                 data-kt-drawer-width="{default:'200px', '300px': '250px'}"
                                 data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle"
                                 data-kt-swapper="true" data-kt-swapper-mode="prepend"
                                 data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                            </div>
                        </div>
                        <div class="d-flex align-items-stretch flex-shrink-0">
                            <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                                <div class="cursor-pointer symbol symbol-30px symbol-md-40px"
                                     data-kt-menu-trigger="click" data-kt-menu-attach="parent"
                                     data-kt-menu-placement="bottom-end">

                                    @if (Auth::user()->avartar == '')
                                        <img src="{{ asset('logo_user.png') }}" alt="user" />
                                    @else
                                        <img src="{{ asset('images/' . Auth::user()->avartar) }}" alt="user" />
                                    @endif
                                </div>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                                     data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <div class="symbol symbol-50px me-5">
                                                @if (Auth::user()->avartar == '')
                                                    <img src="{{ asset('logo_user.png') }}" alt="user" />
                                                @else
                                                    <img src="{{ asset('images/' . Auth::user()->avartar) }}" alt="user" />
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="fw-bolder d-flex align-items-center fs-5">
                                                    {{ Auth::user()->full_name }}
                                                    <span
                                                        class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2">
                                                            @if (Auth::user()->role= 'AD')
                                                            Admin
                                                        @else
                                                            Student
                                                        @endif
                                                        </span>
                                                </div>
                                                <a href="#"
                                                   class="fw-bold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="separator my-2"></div>
                                    <div class="menu-item px-5">
                                        <a href=""  class="menu-link px-5">{{ __('Profile') }}</a>
                                    </div>
                                    <div class="separator my-2"></div>
                                    <div class="menu-item px-5" data-kt-menu-trigger="hover"
                                         data-kt-menu-placement="left-start">
                                        <a href="#" class="menu-link px-5">
                                                <span class="menu-title position-relative">{{ __('Language') }}
                                                    @if (Session::get('language') == 'en')
                                                        <span
                                                            class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">English
                                                            <img class="w-15px h-15px rounded-1 ms-2"
                                                                 src="{{ asset('assets/media/flags/united-states.svg') }}"
                                                                 alt="" /></span>
                                                </span>
                                            @else
                                                <span
                                                    class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">{{ __('Vietnamese') }}
                                                    <img class="w-15px h-15px rounded-1 ms-2"
                                                         src="{{ asset('vietnam.png') }}"
                                                         alt="" /></span></span>
                                            @endif
                                        </a>
                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="{{ route('language.change', ['en']) }}" class="menu-link d-flex px-5 active">
                                                        <span class="symbol symbol-20px me-4">
                                                            <img class="rounded-1"  src="{{ asset('assets/media/flags/united-states.svg') }}"  alt="" />
                                                        </span>English
                                                </a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a  href="{{ route('language.change', ['vi']) }}"class="menu-link d-flex px-5">
                                                        <span class="symbol symbol-20px me-4">
                                                            <img class="rounded-1" src="{{ asset('vietnam.png') }}"
                                                                 alt="" />
                                                        </span>{{ __('Vietnamese') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="{{ route('logout') }}" class="menu-link px-5"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center d-lg-none ms-2 me-n3" title="Show header menu">
                                <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                                     id="kt_header_menu_mobile_toggle">
                                        <span class="svg-icon svg-icon-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 viewBox="0 0 24 24" fill="none">
                                                <path
                                                    d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3"
                                                      d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z"
                                                      fill="currentColor" />
                                            </svg>
                                        </span>
                                    <!--end::Svg Icon-->
                                </div>
                            </div>
                            <!--end::Header menu toggle-->
                        </div>
                        <!--end::Toolbar wrapper-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Container-->
            </div>
            <div class="card">
                <div class="card-body py-3">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ __(session('success')) }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ __(session('error')) }}
                        </div>
                    @endif
                </div>
            </div>
            @yield('content')
        </div>
    </div>
</div>


<script>
    var hostUrl = "assets/";
</script>
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
<script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>

</body>

</html>
