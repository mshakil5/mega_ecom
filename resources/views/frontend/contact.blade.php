@extends('frontend.layouts.app')

@section('content')

<div class="container-fluid">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">Contact Us</span></h2>
    <div class="row px-xl-5">
        <div class="col-lg-7 mb-5">
            <div class="contact-form bg-light p-30">
                <div id="success">
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
                <form name="sentMessage" id="contactForm" action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="control-group">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Your Name"
                            required="required" data-validation-required-message="Please enter your name"
                            @auth
                                value="{{ auth()->user()->name }}"
                            @endauth
                        />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Your Email"
                            required="required" data-validation-required-message="Please enter your email"
                            @auth
                                value="{{ auth()->user()->email }}"
                            @endauth
                        />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group">
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject"
                            required="required" data-validation-required-message="Please enter a subject" />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group">
                        <textarea class="form-control" rows="8" id="message" name="message" placeholder="Message"
                            required="required"
                            data-validation-required-message="Please enter your message"></textarea>
                        <p class="help-block text-danger"></p>
                    </div>
                    <div>
                        <button class="btn btn-primary py-2 px-4" type="submit" id="sendMessageButton">Send
                            Message</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-5 mb-5">
            <div class="bg-light p-30 mb-30">
                <iframe style="width: 100%; height: 250px;"
                        src="{{ $companyDetails->google_map }}"
                        frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
            </div>
            <div class="bg-light p-30 mb-3">
                <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>{{ $companyDetails->address1 }}</p>
                <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>{{ $companyDetails->email1 }}</p>
                <p class="mb-2"><i class="fa fa-phone-alt text-primary mr-3"></i>{{ $companyDetails->phone1 }}</p>
            </div>
        </div>
    </div>
</div>

@endsection