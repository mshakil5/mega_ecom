@extends('frontend.layouts.app')

@section('content')

<main class="main mb-5 mt-5">
    <div class="page-content pb-0">
        <div class="container">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="accordion" id="faqAccordion" style="border: 1px solid #e0e0e0; border-radius: 8px; background-color: #fafafa;">
                        @foreach($faqQuestions as $faq)
                            <div class="accordion-item" style="border-bottom: 1px solid #e0e0e0;">
                                <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                    <button 
                                        class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $loop->index }}" 
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                        aria-controls="collapse{{ $loop->index }}"
                                        style="background-color: #f2f2f2; color: #444; border: none; text-align: left; padding: 15px; font-size: 16px; font-weight: 600;">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div 
                                    id="collapse{{ $loop->index }}" 
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                    aria-labelledby="heading{{ $loop->index }}" 
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body" style="padding: 15px; font-size: 14px; color: #555; background-color: #f9f9f9; border-radius: 5px;">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection