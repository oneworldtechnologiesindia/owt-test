<!-- Page Header-->
<header class="section page-header">
    <!-- RD Navbar-->
    <div class="rd-navbar-wrap">
        <nav class="rd-navbar rd-navbar-classic" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed"
            data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static"
            data-lg-device-layout="rd-navbar-fixed" data-xl-layout="rd-navbar-static"
            data-xl-device-layout="rd-navbar-static" data-xxl-layout="rd-navbar-static"
            data-xxl-device-layout="rd-navbar-static" data-lg-stick-up-offset="46px" data-xl-stick-up-offset="46px"
            data-xxl-stick-up-offset="46px" data-lg-stick-up="true" data-xl-stick-up="true" data-xxl-stick-up="true">
            <div class="rd-navbar-main-outer">
                <div class="rd-navbar-main">
                    <!-- RD Navbar Panel-->
                    <div class="rd-navbar-panel">
                        <!-- RD Navbar Toggle-->
                        <button class="rd-navbar-toggle"
                            data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                        <!-- RD Navbar Brand-->
                        <div class="rd-navbar-brand">
                            <a class="brand" href="{{ route('index') }}">
                                <img src="{{ URL::asset('/assets/images/logo.svg') }}" alt="{{ config('app.name') }}"
                                    width="130" height="auto" />
                            </a>
                        </div>
                    </div>
                    <div class="rd-navbar-main-element">
                        <div class="rd-navbar-nav-wrap">
                            <!-- RD Navbar Share-->
                            {{-- <div class="rd-navbar-share fl-bigmug-line-share27"
                                data-rd-navbar-toggle=".rd-navbar-share-list">
                                <ul class="list-inline rd-navbar-share-list">
                                    <li class="rd-navbar-share-list-item"><a class="icon fa fa-facebook"
                                            href="#"></a></li>
                                    <li class="rd-navbar-share-list-item"><a class="icon fa fa-twitter"
                                            href="#"></a></li>
                                    <li class="rd-navbar-share-list-item"><a class="icon fa fa-google-plus"
                                            href="#"></a></li>
                                    <li class="rd-navbar-share-list-item"><a class="icon fa fa-instagram"
                                            href="#"></a></li>
                                </ul>
                            </div> --}}
                            <ul class="rd-navbar-nav">
                                <li class="rd-nav-item active"><a class="rd-nav-link" href="#home">Home</a>
                                </li>
                                <li class="rd-nav-item"><a class="rd-nav-link" href="#services">Services</a>
                                </li>
                                <li class="rd-nav-item"><a class="rd-nav-link" href="#features">Features</a>
                                </li>
                                {{-- <li class="rd-nav-item"><a class="rd-nav-link" href="#projects">Projects</a>
                                </li> --}}
                                 <li class="rd-nav-item"><a class="rd-nav-link" href="#pricing">Pricing</a>
                                </li>
                                <li class="rd-nav-item"><a class="rd-nav-link" href="#team">Team</a></li>
                                {{-- <li class="rd-nav-item"><a class="rd-nav-link" href="#news">News</a></li> --}}
                                <li class="rd-nav-item"><a class="rd-nav-link" href="#contacts">Contacts</a>
                                </li>
                                @if (isset($loginUser) && !empty($loginUser))
                                    @if($loginUser->role_type == 1)
                                    <li class="rd-nav-item">
                                        <a class="rd-nav-link" href="{{ route('home') }}">Dashboard </a>
                                    </li>
                                    @elseif($loginUser->role_type == 2)
                                    <li class="rd-nav-item">
                                        <a class="rd-nav-link" href="{{ route('dealer.home') }}">Dashboard </a>
                                    </li>
                                    @elseif($loginUser->role_type == 3)
                                    <li class="rd-nav-item">
                                        <a class="rd-nav-link" href="{{ route('customer.home') }}">Dashboard </a>
                                    </li>
                                    @endif
                                @else
                                    <li class="rd-nav-item">
                                        <a class="btn btn-primary btn-sm p-2" href="{{ route('login') }}">Login </a>
                                        {{-- /
                                        <a class="rd-nav-link" href="{{ route('register') }}"> Registrieren</a> --}}
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
