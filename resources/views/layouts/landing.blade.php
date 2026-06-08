<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <title>@yield('title', 'Texts to Book')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Transform your ideas into professional books with AI" />
        <meta name="keywords" content="AI, book generation, professional books, authors" />
        <meta content="Texts to Book" name="author" />

        <link rel="shortcut icon" href="{{ asset('landing/images/favicon.ico') }}">
        <!-- Bootstrap css -->
        <link rel="stylesheet" href="{{ asset('landing/css/bootstrap.min.css') }}" type="text/css" />

        <!-- slider -->
        <link rel="stylesheet" href="{{ asset('landing/css/swiper-bundle.min.css') }}" />

        <!-- Icon -->
        <link rel="stylesheet" href="{{ asset('landing/css/materialdesignicons.min.css') }}" type="text/css" />

        <!-- css -->
        <link rel="stylesheet" href="{{ asset('landing/css/style.min.css') }}" type="text/css" />

        @stack('css')
    </head>
    <body data-bs-spy="scroll" data-bs-target="#navbar" data-bs-offset="71">

        <nav class="navbar navbar-expand-lg fixed-top navbar-white navbar-custom sticky" id="navbar">
            <div class="container">

                <!-- LOGO -->
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('welcome') }}">
                    <div class="bg-primary rounded-3 d-flex align-items-center justify-center p-2" style="width: 40px; height: 40px;">
                        <i class="mdi mdi-book-open-page-variant text-white fs-20"></i>
                    </div>
                    <span class="text-dark fw-bold fs-20">Texts to <span class="text-primary">Book</span></span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="mdi mdi-menu"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav mx-auto" id="navbar-navlist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#pricing">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                    </ul>
                 <div class="d-flex align-items-center">
                    <div class="me-5 flex-shrink-0 d-none d-lg-block">
                        @auth
                            <a class="btn btn-primary nav-btn" href="{{ route('dashboard') }}">
                                Dashboard
                            </a>
                        @else
                            <a class="btn btn-primary nav-btn" href="{{ route('register') }}">
                                Sign Up
                            </a>
                        @endauth
                    </div>
                </div>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->

        @yield('content')

        <!-- START FOOTER -->
        <footer class="section bg-footer">
            <div class="container">
                <div class="row g-sm-4">
                    <div class="col-lg-12">
                        <div class="mb-3 mb-sm-0">
                            <div class="d-flex align-items-center gap-2 mb-4">
                                <div class="bg-primary rounded-3 d-flex align-items-center justify-center p-2" style="width: 32px; height: 32px;">
                                    <i class="mdi mdi-book-open-page-variant text-white fs-16"></i>
                                </div>
                                <span class="text-dark fw-bold fs-18">Texts to <span class="text-primary">Book</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-6">
                        <h6 class="text-uppercase fw-semibold">About</h6>
                        <ul class="list-unstyled footer-link mt-3 mb-0 fs-14">
                            <li><a href="javascript:void(0)">About Us</a></li>
                            <li><a href="javascript:void(0)">Features</a></li>
                            <li><a href="javascript:void(0)">Pricing</a></li>
                        </ul>
                    </div><!-- End col -->

                    <div class="col-lg-3 col-md-4 col-6">
                        <h6 class="text-uppercase fw-semibold">Support</h6>
                        <ul class="list-unstyled footer-link mt-3 mb-0 fs-14">
                            <li><a href="javascript:void(0)">Help Center</a></li>
                            <li><a href="javascript:void(0)">Privacy Policy</a></li>
                            <li><a href="javascript:void(0)">Terms of Service</a></li>
                        </ul>
                    </div><!-- End col -->

                    <div class="col-lg-3 col-md-4 col-6 d-none d-sm-block">
                        <h6 class="text-uppercase fw-semibold">Resources</h6>
                        <ul class="list-unstyled footer-link mt-3 mb-0 fs-14">
                            <li><a href="javascript:void(0)">Blog</a></li>
                            <li><a href="javascript:void(0)">Documentation</a></li>
                            <li><a href="javascript:void(0)">API Reference</a></li>
                        </ul>
                    </div><!-- End col -->
                    <div class="col-lg-3 col-10">
                        <h6 class="text-uppercase fw-semibold">Connect with <span class="text-primary text-uppercase fs-18">Texts to Book</span></h6>
                            <p class="mt-md-3 pt-3 pt-md-2 fs-14">Stay updated with our latest AI book writing features.</p>
                        <div class="footer-subcribe text-end shadow-sm d-inline-block">
                            <form action="javascript:void(0)">
                                <input placeholder="Your Email Address" type="email">
                                <button type="submit" class="btn btn-primary"><i class="mdi mdi-bell-ring"></i></button>
                            </form>
                        </div>
                            <div class="mt-md-4 mt-3">
                                <ul class="list-inline footer-social mb-0">
                                    <li class="list-inline-item">
                                        <a href="javascript:void(0)" class="rounded">
                                            <i class="mdi mdi-facebook text-dark"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="javascript:void(0)" class="rounded">
                                            <i class="mdi mdi-linkedin text-dark"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="javascript:void(0)" class="rounded">
                                            <i class="mdi mdi-twitter text-dark"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                    </div>
                </div><!-- End row -->
            </div><!-- End container -->
        </footer>
        <!-- END FOOTER -->

        <!-- FOOTER-ALT -->
        <div class="footer-alt pt-3 pb-3">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-white">©
                                <script>document.write(new Date().getFullYear())</script> Texts to Book.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END FOOTER-ALT -->

        <!--start back-to-top-->
        <button onclick="topFunction()" id="back-to-top">
            <i class="mdi mdi-arrow-up"></i>
        </button>
        <!--end back-to-top-->

        <!--Custom js-->
        <script src="{{ asset('landing/js/counter.js') }}"></script>

        <script src="{{ asset('landing/js/swiper-bundle.min.js') }}"></script>

        <!--Bootstrap Js-->
        <script src="{{ asset('landing/js/bootstrap.bundle.min.js') }}"></script>

        <!-- contact -->
        <script src="{{ asset('landing/js/contact.js') }}"></script>

        <!-- App Js -->
        <script src="{{ asset('landing/js/app.js') }}"></script>

        @stack('js')
    </body>
</html>
