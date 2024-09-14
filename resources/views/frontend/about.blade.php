@extends('frontend.layouts.app')

@section('content')

<main class="main mb-5 mt-5">
    <div class="page-content pb-0">
        <div class="container">
            <div class="row">
            {!! $companyDetails->about_us !!}
            </div>
        </div>
    </div>
</main>

@endsection