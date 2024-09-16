@extends('frontend.layouts.app')

@section('content')

<main class="main mt-5">
    <div class="page-content pb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-2 mb-lg-0">
                    <h2 class="title mb-1">Contact Us</h2>
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="contact-info">
                                <ul class="contact-list">
                                    <li>
                                        <i class="icon-map-marker"></i>
                                        {{ $companyDetails->address1 }}
                                    </li>
                                    <li>
                                        <i class="icon-phone"></i>
                                        <a href="tel:{{ $companyDetails->phone1 }}">{{ $companyDetails->phone1 }}</a>
                                    </li>
                                    <li>
                                        <i class="icon-envelope"></i>
                                        <a href="mailto:{{ $companyDetails->email1 }}">{{ $companyDetails->email1 }}</a>
                                    </li>
                                </ul>
                            </div>

                            <iframe style="width: 100%; height: 250px;"
                                src="{{ $companyDetails->google_map }}"
                                frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0">
                            </iframe>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="title mb-1">Got Any Questions?</h2>
                    <p class="mb-2">Use the form below to get in touch with the sales team</p>

                    <div id="success" class="mb-3">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <form name="sentMessage" id="contactForm" action="{{ route('contact.store') }}" method="POST" class="contact-form mb-3">
                        @csrf

                        <div class="row">
                            <div class="col-sm-6">
                                <label for="name" class="sr-only">Name *</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Your Name"
                                    required="required" data-validation-required-message="Please enter your name"
                                    @auth
                                        value="{{ auth()->user()->name }}"
                                    @endauth
                                />
                                <p class="help-block text-danger"></p>
                            </div>

                            <div class="col-sm-6">
                                <label for="email" class="sr-only">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Your Email"
                                    required="required" data-validation-required-message="Please enter your email"
                                    @auth
                                        value="{{ auth()->user()->email }}"
                                    @endauth
                                />
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <label for="phone" class="sr-only">Phone *</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Your Phone"
                                    required="required" data-validation-required-message="Please enter your phone number"
                                    @auth
                                        value="{{ auth()->user()->phone }}"
                                    @endauth
                                />
                                <p class="help-block text-danger"></p>
                            </div>

                            <div class="col-sm-6">
                                <label for="subject" class="sr-only">Subject *</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required="required" data-validation-required-message="Please enter a subject" />
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>

                        <label for="cmessage" class="sr-only">Message *</label>
                        <textarea class="form-control" cols="30" rows="4" id="message" name="message" placeholder="Message"
                            required="required"
                            data-validation-required-message="Please enter your message">
                        </textarea>
                        <p class="help-block text-danger"></p>

                        <button type="submit" id="sendMessageButton" class="btn btn-outline-primary-2 btn-minwidth-sm">
                            <span>SUBMIT</span>
                            <i class="icon-long-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection