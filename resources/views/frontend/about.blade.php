@extends('frontend.layouts.app')

@section('content')

<div class="container-fluid">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">About Us</span></h2>
    <div class="row px-xl-5">
        <div class="col-lg-12 mb-3">
            <div class="contact-form bg-light p-30">
                {!! $companyDetails->about_us !!}
            </div>
        </div>
    </div>
</div>

@endsection