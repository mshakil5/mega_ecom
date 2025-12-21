@extends('frontend.layouts.app')

@section('content')

<main class="main mt-5 mb-5">
    <div class="page-content pb-0">
        <div class="container">
            <h2 class="fs-2 fw-bold mb-4 text-dark">Contact Us</h2>
            
            <div class="row">
                <!-- Left Column - Contact Info & Map -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="contact-info-section">
                        <h5 class="contact-section-title mb-3">Get In Touch</h5>
                        
                        <div class="contact-info">
                            <ul class="contact-list">
                                <li class="contact-item">
                                    <i class="bi bi-geo-alt-fill contact-icon"></i>
                                    <div class="contact-content">
                                        <span class="contact-label">Address</span>
                                        <p class="contact-text">{{ $companyDetails->address1 }}</p>
                                    </div>
                                </li>
                                <li class="contact-item">
                                    <i class="bi bi-telephone-fill contact-icon"></i>
                                    <div class="contact-content">
                                        <span class="contact-label">Phone</span>
                                        <a href="tel:{{ $companyDetails->phone1 }}" class="contact-text">{{ $companyDetails->phone1 }}</a>
                                    </div>
                                </li>
                                <li class="contact-item">
                                    <i class="bi bi-envelope-fill contact-icon"></i>
                                    <div class="contact-content">
                                        <span class="contact-label">Email</span>
                                        <a href="mailto:{{ $companyDetails->email1 }}" class="contact-text">{{ $companyDetails->email1 }}</a>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="map-container mt-4">
                            <iframe style="width: 100%; height: 300px; border-radius: 8px; border: 1px solid #eee;"
                                src="{{ $companyDetails->google_map }}"
                                frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0">
                            </iframe>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Contact Form -->
                <div class="col-lg-6">
                    <div class="contact-form-section">
                        <h5 class="contact-section-title mb-3">Send us a Message</h5>
                        <p class="text-muted mb-4">Have a question? Fill out the form below and our team will get back to you shortly.</p>

                        <div id="success" class="mb-3">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                        </div>

                        <form name="sentMessage" id="contactForm" action="{{ route('contact.store') }}" method="POST" class="contact-form">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <label for="name" class="form-label">Name *</label>
                                    <input type="text" class="form-control contact-input" id="name" name="name" placeholder="Your Name"
                                        required="required"
                                        @auth
                                            value="{{ auth()->user()->name }}"
                                        @endauth
                                    />
                                    <small class="text-danger error-message" id="name-error"></small>
                                </div>

                                <div class="col-sm-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control contact-input" id="email" name="email" placeholder="Your Email"
                                        required="required"
                                        @auth
                                            value="{{ auth()->user()->email }}"
                                        @endauth
                                    />
                                    <small class="text-danger error-message" id="email-error"></small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <label for="phone" class="form-label">Phone *</label>
                                    <input type="text" class="form-control contact-input" id="phone" name="phone" placeholder="Your Phone"
                                        required="required"
                                        @auth
                                            value="{{ auth()->user()->phone }}"
                                        @endauth
                                    />
                                    <small class="text-danger error-message" id="phone-error"></small>
                                </div>

                                <div class="col-sm-6">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <input type="text" class="form-control contact-input" id="subject" name="subject" placeholder="Subject" 
                                        required="required" />
                                    <small class="text-danger error-message" id="subject-error"></small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control contact-input" id="message" name="message" rows="5"
                                    required="required"></textarea>
                                <small class="text-danger error-message" id="message-error"></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" id="captcha-question">What is 5 + 3? *</label>
                                <input type="number" class="form-control contact-input" id="captcha-answer" placeholder="Your answer" required />
                                <small class="text-danger error-message d-none" id="captcha-error"></small>
                            </div>

                            <button type="submit" id="sendMessageButton" class="btn btn-submit">
                                <span>Send Message</span>
                                <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    /* Contact Page Styles */
    .contact-info-section,
    .contact-form-section {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .contact-section-title {
        font-size: 18px;
        font-weight: 700;
        color: #000;
        margin-bottom: 20px;
    }

    /* Contact List */
    .contact-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .contact-item {
        display: flex;
        margin-bottom: 25px;
        align-items: flex-start;
    }

    .contact-item:last-child {
        margin-bottom: 0;
    }

    .contact-icon {
        font-size: 24px;
        color: #333;
        margin-right: 15px;
        margin-top: 2px;
        min-width: 24px;
    }

    .contact-content {
        flex: 1;
    }

    .contact-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        margin-bottom: 5px;
        letter-spacing: 0.5px;
    }

    .contact-text {
        font-size: 14px;
        color: #333;
        margin: 0;
        text-decoration: none;
        line-height: 1.6;
    }

    .contact-text:hover {
        color: #000;
    }

    /* Form Styles */
    .contact-form {
        margin-top: 0;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .contact-input {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 12px 15px;
        font-size: 14px;
        color: #333;
        background-color: #fff;
        transition: all 0.3s ease;
    }

    .contact-input:focus {
        border-color: #000;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
        outline: none;
    }

    .contact-input.is-invalid {
        border-color: #dc3545;
    }

    .contact-input.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .contact-input::placeholder {
        color: #999;
    }

    textarea.contact-input {
        resize: vertical;
        min-height: 120px;
    }

    /* Error Messages */
    .error-message {
        display: block;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
    }

    /* Submit Button */
    .btn-submit {
        background-color: #000;
        color: white;
        border: 1px solid #000;
        padding: 12px 35px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }

    .btn-submit:hover {
        background-color: #333;
        border-color: #333;
        color: white;
    }

    .btn-submit:disabled {
        background-color: #999;
        border-color: #999;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Map Container */
    .map-container {
        overflow: hidden;
        border-radius: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .contact-info-section,
        .contact-form-section {
            padding: 20px;
        }

        .contact-section-title {
            font-size: 16px;
        }

        .contact-item {
            margin-bottom: 20px;
        }

        .row {
            flex-direction: column;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Generate CAPTCHA
    function generateCaptcha() {
        let num1 = Math.floor(Math.random() * 10) + 1;
        let num2 = Math.floor(Math.random() * 10) + 1;
        return { 
            question: `What is ${num1} + ${num2}?`, 
            answer: num1 + num2 
        };
    }

    let captcha = generateCaptcha();
    $('#captcha-question').text(captcha.question + ' *');

    // Form Validation
    function validateForm() {
        let isValid = true;
        
        // Clear all error messages
        $('.error-message').addClass('d-none').text('');
        $('.contact-input').removeClass('is-invalid');

        // Name validation
        let name = $('#name').val().trim();
        if (!name) {
            $('#name-error').removeClass('d-none').text('Name is required');
            $('#name').addClass('is-invalid');
            isValid = false;
        } else if (name.length < 2) {
            $('#name-error').removeClass('d-none').text('Name must be at least 2 characters');
            $('#name').addClass('is-invalid');
            isValid = false;
        }

        // Email validation
        let email = $('#email').val().trim();
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            $('#email-error').removeClass('d-none').text('Email is required');
            $('#email').addClass('is-invalid');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            $('#email-error').removeClass('d-none').text('Please enter a valid email');
            $('#email').addClass('is-invalid');
            isValid = false;
        }

        // Phone validation
        let phone = $('#phone').val().trim();
        let phoneRegex = /^[0-9\-\+\(\)\s]{10,}$/;
        if (!phone) {
            $('#phone-error').removeClass('d-none').text('Phone is required');
            $('#phone').addClass('is-invalid');
            isValid = false;
        } else if (!phoneRegex.test(phone)) {
            $('#phone-error').removeClass('d-none').text('Please enter a valid phone number');
            $('#phone').addClass('is-invalid');
            isValid = false;
        }

        // Subject validation
        let subject = $('#subject').val().trim();
        if (!subject) {
            $('#subject-error').removeClass('d-none').text('Subject is required');
            $('#subject').addClass('is-invalid');
            isValid = false;
        } else if (subject.length < 3) {
            $('#subject-error').removeClass('d-none').text('Subject must be at least 3 characters');
            $('#subject').addClass('is-invalid');
            isValid = false;
        }

        // Message validation
        let message = $('#message').val().trim();
        if (!message) {
            $('#message-error').removeClass('d-none').text('Message is required');
            $('#message').addClass('is-invalid');
            isValid = false;
        } else if (message.length < 10) {
            $('#message-error').removeClass('d-none').text('Message must be at least 10 characters');
            $('#message').addClass('is-invalid');
            isValid = false;
        }

        // CAPTCHA validation
        let userAnswer = parseInt($('#captcha-answer').val());
        if (!$('#captcha-answer').val()) {
            $('#captcha-error').removeClass('d-none').text('Please answer the security question');
            $('#captcha-answer').addClass('is-invalid');
            isValid = false;
        } else if (userAnswer !== captcha.answer) {
            $('#captcha-error').removeClass('d-none').text('Incorrect answer. Please try again.');
            $('#captcha-answer').addClass('is-invalid');
            captcha = generateCaptcha();
            $('#captcha-question').text(captcha.question + ' *');
            $('#captcha-answer').val('');
            isValid = false;
        }

        return isValid;
    }

    // Form Submit
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        if (validateForm()) {
            $('#captcha-error').addClass('d-none');
            let $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).text('Sending...');
            
            // Simulate form submission
            setTimeout(() => {
                this.submit();
            }, 500);
        }
    });

    // Clear error on input change
    $('.contact-input').on('input', function() {
        $(this).removeClass('is-invalid');
        let fieldId = $(this).attr('id');
        $('#' + fieldId + '-error').addClass('d-none').text('');
    });
</script>

@endsection