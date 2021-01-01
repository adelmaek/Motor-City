<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Motor City</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--Toaster Popup message CSS -->
    <link href="{{ asset('node_modules/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('dist/css/style.min.css')}}" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="{{ asset('dist/css/pages/dashboard1.css')}}" rel="stylesheet"> 
    <link href="{{asset('node_modules/datatables/media/css/dataTables.bootstrap4.css')}}" rel="stylesheet"> 

</head>
<body class="skin-default fixed-layout">
    <div id="app">
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <div class="preloader">
            <div class="loader">
                <div class="loader__figure"></div>
                <p class="loader__label">Motor City</p>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm border">
            @guest
          
                    
            @else
                <a class="navbar-brand float-left" href="{{ url('/') }}">
                    Motor City
                </a>
           
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endguest
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @guest
          
                    
                    @else
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('queryBrandAllTransactions')}}">All Transations</a>
                        </li>
                        <li class="nav-item dropdown">
                            <div class="dropdown">
                                <a href="javascript:void(0)" class="nav-link dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cash Accounts</a>
                                <div class="dropdown-menu animated flipInY">
                                    <a class="dropdown-item" href="{{route('queryBrandAccountTransaction',["cash"])}}">Cash</a>
                                    <a class="dropdown-item" href="{{route('queryBrandAccountTransaction',["cashDollar"])}}">Cash $</a>
                                    <a class="dropdown-item" href="{{route('queryBrandAccountTransaction',["custodyCash"])}}">Custody Cash</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <div class="dropdown">
                                <a href="javascript:void(0)" class="nav-link dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Banks Accounts</a>
                                <div class="dropdown-menu animated flipInY">
                                    @foreach ($bankAccounts as $bankAccount)
                                        <a class="dropdown-item" href="{{route('queryBankAccountTransaction',[$bankAccount->id])}}">{{$bankAccount->name}}</a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('queryBrandAccountTransaction',["check"])}}">Checks</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('queryBrandAccountTransaction',["visa"])}}">Visa</a>
                        </li>
                    </ul>
                    @endguest
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            
                        @else
                            @if (Route::has('register') && Auth::user()->admin)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('addAccount') }}">Add Account</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                            <li class="nav-item dropdown">
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="nav-link dropdown-toggle u-dropdown link hide-menu" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
                                    <div class="dropdown-menu animated flipInY">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </li>

                        @endguest
                    </ul>
                </div>
         
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
        
    </div>
    
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('node_modules/jquery/jquery-3.2.1.min.js')}}"></script>
    <!-- Bootstrap popper Core JavaScript -->
    <script src="{{ asset('node_modules/popper/popper.min.js')}}"></script>
    {{-- <script src="{{ asset('node_modules/bootstrap/dist/js/bootstrap.min.js')}}"></script> --}}
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('dist/js/perfect-scrollbar.jquery.min.js')}}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('dist/js/waves.js')}}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('dist/js/sidebarmenu.js')}}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('dist/js/custom.min.js')}}"></script>
    <script src="{{ asset('node_modules/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!-- Popup message jquery -->
    <script src="{{ asset('node_modules/toast-master/js/jquery.toast.js')}}"></script>
    <!-- datatable -->
    <script src="{{asset('node_modules/datatables/datatables.min.js')}}"></script>
     <!-- start - This is for export functionality only -->
     <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
     <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
     <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
     <!-- end - This is for export functionality only -->
     {{-- sweet alert --}} 
     <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script>
         $('.delete-confirm').on('click', function (event) {
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                title: 'Are you sure?',
                text: 'This record and it`s details will be permanantly deleted!',
                icon: 'warning',
                buttons: ["Cancel", "Yes!"],
            }).then(function(value) {
                if (value) {
                    window.location.href = url;
                }
            });
        });
        $('.settle-confirm').on('click', function (event) {
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                title: 'Are you sure?',
                text: 'This check will be settled',
                icon: 'warning',
                buttons: ["Cancel", "Yes!"],
            }).then(function(value) {
                if (value) {
                    window.location.href = url;
                }
            });
        });
     </script>
     {{-- end sweer alert --}}
    @yield('extraJS')
</body>
</html>
