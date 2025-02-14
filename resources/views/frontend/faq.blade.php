@extends('frontend.layouts.app')

@section('content')

<main class="main mb-5 mt-5">
    <div class="page-content pb-0">
        <div class="container">
            <div class="row">
              <div class="col-10 mx-auto" data-aos="fade-up" data-aos-delay="0">
                  <div class="accordion" id="faqAccordion">
                    @foreach($faqQuestions as $faq)
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="heading{{ $loop->index }}">
                          <button 
                              class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                              type="button" 
                              data-bs-toggle="collapse" 
                              data-bs-target="#collapse{{ $loop->index }}" 
                              aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                              aria-controls="collapse{{ $loop->index }}"
                              style="background-color: var(--bs-red); color: #fff; font-weight: 500;">
                              {{ $faq->question }}
                          </button>
                      </h2>
                      <div 
                          id="collapse{{ $loop->index }}" 
                          class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                          aria-labelledby="heading{{ $loop->index }}" 
                          data-bs-parent="#faqAccordion">
                          <div class="accordion-body">
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