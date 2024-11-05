@extends('frontend.layouts.app')

@section('content')

<div class="d-flex flex-column justify-content-center align-items-center vh-100 text-center">
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for shopping with us. Your order has been placed successfully...!</p>
    
    <a href="{{ $pdfUrl }}" class="btn btn-primary mt-4" target="_blank">
        <i class="fas fa-file-download"></i> Download Invoice (PDF)
    </a>
</div>

@endsection

@section('script')
<script>
    window.onload = function() {
        localStorage.removeItem('cart');
        localStorage.removeItem('wishlist');
    };
</script>
@endsection