@extends('layouts.landing')

@section('content')
<!-- Start Home -->
<section class="section home" id="home">
    <div class="container">
        <div class="row align-items-center mt-5 mt-lg-0">
            <div class="col-lg-5">
                <div class="home-heading">
                    <h6 class="text-uppercase text-muted">AI-Powered Authorship</h6>
                    <h1 class="lh-sm">Texts to <span class="text-primary">Book</span></h1>
                    <p class="text-muted fs-17">Transform your ideas into beautifully formatted professional books with the power of Artificial Intelligence. From outline to final draft, we've got you covered.</p>
                </div>
                <div class="home-btn d-grid d-sm-block gap-3">
                    @auth
                        <a class="btn btn-outline-primary rounded-pill me-sm-3" href="{{ route('reports.create') }}">Create Book
                            <span class="avatar-xs">
                                <span class="avatar-title rounded-circle btn-icon">
                                    <i class="mdi mdi-chevron-double-right"></i>
                                </span>
                            </span>
                        </a>
                    @else
                        <a class="btn btn-outline-primary rounded-pill me-sm-3" href="{{ route('register') }}">Get Started
                            <span class="avatar-xs">
                                <span class="avatar-title rounded-circle btn-icon">
                                    <i class="mdi mdi-chevron-double-right"></i>
                                </span>
                            </span>
                        </a>
                    @endauth
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target=".watchvideomodal">
                        <div class="d-inline-flex align-items-center">
                            <div class="flex-grow-1 me-2">
                                <span class="text-muted fs-14">How it works</span>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle modal-btn">
                                        <i class="mdi mdi-play"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- Modal -->
                    <div class="modal fade bd-example-modal-lg watchvideomodal" data-keyboard="false"
                        tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg">
                            <div class="modal-content home-modal">
                                <div class="modal-header border-0">
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL -->
                </div>
            </div><!-- end col-->
            <div class="col-lg-7">
                <div class="ms-md-4">
                    <img class="home-img img-fluid" src="{{ asset('landing/images/home.png') }}" alt="AI Writing">
                </div>
            </div><!-- end col-->
        </div><!-- end row-->
    </div><!--end container-->
    <div class="container-fluid">
        <div class="row">
            <div class="home-shape-arrow">
                <a href="#features" class="mouse-down"><i class="mdi mdi-arrow-down arrow-icon text-dark h5"></i></a>
            </div>
        </div><!--end row-->
    </div><!--end container-->
</section>
<!-- End Home -->

<!-- Start features -->
<section class="section features features-bg" id="features">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center mb-5">
                    <h3 class="heading">Powerful AI Features</h3>
                    <p class="text-muted fs-17">Our platform uses advanced Gemini AI to help you create high-quality books in record time.</p>
                </div>
            </div><!-- end col-->
        </div><!-- end row-->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="card features-card">
                    <div class="card-body">
                        <div class="avatar-md mb-4">
                            <div class="avatar-title bg-primary rounded-circle">
                                <i class="mdi mdi-brain"></i>
                            </div>
                        </div>
                        <h5>Intelligent Outlines</h5>
                        <p class="text-muted">AI-powered book structure generation that organizes your ideas logically and professionally.</p>
                    </div>
                </div>
            </div><!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card features-card">
                    <div class="card-body">
                        <div class="avatar-md mb-4">
                            <div class="avatar-title bg-primary rounded-circle">
                                <i class="mdi mdi-pen"></i>
                            </div>
                        </div>
                        <h5>Auto Content Generation</h5>
                        <p class="text-muted">Professional chapter content generation based on your unique vision and style.</p>
                    </div>
                </div>
            </div><!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card features-card">
                    <div class="card-body">
                        <div class="avatar-md mb-4">
                            <div class="avatar-title bg-primary rounded-circle">
                                <i class="mdi mdi-image-multiple"></i>
                            </div>
                        </div>
                        <h5>Rich Illustrations</h5>
                        <p class="text-muted">Automatically source beautiful, relevant images to illustrate your book's sections.</p>
                    </div>
                </div>
            </div><!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card features-card">
                    <div class="card-body">
                        <div class="avatar-md mb-4">
                            <div class="avatar-title bg-primary rounded-circle">
                                <i class="mdi mdi-file-pdf-box"></i>
                            </div>
                        </div>
                        <h5>Professional PDF Export</h5>
                        <p class="text-muted">Export your completed book to a perfectly formatted PDF, ready for reading or publishing.</p>
                    </div>
                </div>
            </div><!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card features-card">
                    <div class="card-body">
                        <div class="avatar-md mb-4">
                            <div class="avatar-title bg-primary rounded-circle">
                                <i class="mdi mdi-earth"></i>
                            </div>
                        </div>
                        <h5>Multi-language Support</h5>
                        <p class="text-muted">Create books in English or French with full localization support for global reach.</p>
                    </div>
                </div>
            </div><!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card features-card">
                    <div class="card-body">
                        <div class="avatar-md mb-4">
                            <div class="avatar-title bg-primary rounded-circle">
                                <i class="mdi mdi-lightning-bolt"></i>
                            </div>
                        </div>
                        <h5>Real-time Generation</h5>
                        <p class="text-muted">Watch your book being generated in real-time with our interactive progress tracking.</p>
                    </div>
                </div>
            </div><!-- end col -->
        </div><!-- end row -->
    </div><!-- end container -->
</section>
<!-- end Features -->

<!-- start about -->
<section class="section" id="about">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center mb-lg-5">
                    <h3 class="heading">The Future of Writing</h3>
                    <p class="text-muted fs-17 mb-0">We empower authors, researchers, and creators to turn their expertise into comprehensive books.</p>
                </div>
            </div><!--  end col  -->
        </div><!--  end row  -->
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-6">
                <div class="card border-0">
                    <img src="{{ asset('landing/images/about.png') }}" alt="About Texts to Book">
                </div>
            </div><!--  end col  -->
            <div class="col-lg-5">
                <div class="card border-0">
                    <div class="card-body">
                        <div class="about-title">
                            <span></span>
                            <h6 class="text-uppercase">Seamless Workflow</h6>
                        </div>
                        <h4>Everything You Need in One Place.</h4>
                        <p class="text-muted lh-base">No more writer's block. Our AI assists you at every step, from brainstorming the initial outline to refining the final chapters. Focus on your ideas, while we handle the formatting and research.</p>
                        <div class="about-link">
                            <a href="#features">Learn More <i class="mdi mdi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div><!--  end col  -->
        </div><!--  end row  -->
    </div><!--  end container  -->
</section>
<!--  end about  -->

<!-- START pricing -->
<section class="section pricing" id="pricing">
    <div class="bg-shape"></div>
    <div class="container">
        <div class="row gy-5 justify-content-center">
            <div class="col-lg-12">
                <div class="text-center">
                    <h3 class="heading">Choose Your Creative Plan</h3>
                    <p class="text-muted">Flexible pricing for every type of author.</p>
                </div>
            </div><!-- End col -->
            <div class="col-lg-4 col-md-6">
                <div class="card pricing-box border-light h-100 py-5 mx-1">
                    <div class="pb-4 text-center border-bottom">
                        <h6 class="text-info">Starter</h6>
                        <h1 class="mb-0 pt-2 fw-bold">$0 <sub class="fs-14 fw-normal text-muted">/Free</sub></h1>
                    </div>
                    <div class="p-4 pb-0">
                            <ul class="list-unstyled">
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>1 Book Per Month</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>AI Outlines</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>PDF Export</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                    </div>
                    <div class="mx-auto">
                        <a href="{{ route('register') }}" class="btn btn-outline-dark">Sign Up Free</a>
                    </div>
                </div><!-- End card -->
            </div>
            <!-- end col -->
            <div class="col-lg-4 col-md-6">
                <div class="card pricing-box border-light h-100 py-5 mx-1 active">
                    <div class="pb-4 text-center border-bottom">
                        <h6 class="text-danger">Pro</h6>
                        <h1 class="mb-0 pt-2 fw-bold">$29 <sub class="fs-14 fw-normal text-muted">/Month</sub></h1>
                    </div>
                    <div class="p-4 pb-0">
                            <ul class="list-unstyled">
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Unlimited Books</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Priority AI Processing</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Advanced Customization</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                    </div>
                    <div class="mx-auto">
                        <a href="{{ route('register') }}" class="btn btn-outline-dark active">Go Pro</a>
                      </div>
                </div><!-- End card -->
            </div>
            <!-- col end -->
            <div class="col-lg-4 col-md-6">
                <div class="card pricing-box border-light h-100 py-5 mx-1">
                    <div class="pb-4 text-center border-bottom">
                        <h6 class="text-primary">Enterprise</h6>
                        <h1 class="mb-0 pt-2 fw-bold">Custom</h1>
                    </div>
                    <div class="p-4 pb-0">
                            <ul class="list-unstyled">
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>API Access</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Custom AI Training</span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shring-0">
                                            <i class="mdi mdi-circle-medium"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span>Dedicated Support</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                    </div>
                    <div class="mx-auto">
                        <a href="#contact" class="btn btn-outline-dark">Contact Us</a>
                      </div>
                </div><!-- End card -->
            </div>
            <!-- col end -->
        </div><!-- End row -->
    </div><!-- End container -->
</section>
<!-- END pricing -->

<!-- Start contact -->
<section class="section" id="contact">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mb-5">
                    <h3 class="heading">Get in Touch</h3>
                    <p class="text-muted mt-2">Have questions about our AI book generation? Our team is here to help you.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-around">
            <div class="col-lg-6">
                <form method="post" action="#" class="contact-form">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="position-relative mb-3">
                                <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                <input name="name" id="name" type="text" class="form-control" placeholder="Enter your name*">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="position-relative mb-3">
                                <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                <input name="email" id="email" type="email" class="form-control" placeholder="Enter your email*">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="position-relative mb-3">
                                <span class="input-group-text"><i class="mdi mdi-file-document-outline"></i></span>
                                <input name="subject" id="subject" type="text" class="form-control" placeholder="Subject">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="position-relative mb-3">
                            <span class="input-group-text align-items-start"><i class="mdi mdi-comment-text-outline"></i></span>
                                <textarea name="comments" id="comments" rows="4" class="form-control" placeholder="Enter your message*"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-4">
                <div class="contact-details mb-4 mb-lg-0">
                    <p class="mb-3"><i class="mdi mdi-email-outline align-middle text-muted fs-20 me-2"></i> <span class="fw-medium">support@textstobook.com</span></p>
                    <p class="mb-3"><i class="mdi mdi-web align-middle text-muted fs-20 me-2"></i> <span class="fw-medium">www.textstobook.com</span></p>
                    <p class="mb-3"><i class="mdi mdi-hospital-building text-muted fs-20 me-2"></i> <span class="fw-medium">9:00 AM - 6:00 PM</span></p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End contact -->
@endsection
