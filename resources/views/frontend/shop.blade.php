@extends('frontend.layouts.app')

@section('content')

    <div class="container-fluid main-content-area">
        <button class="filter-toggle-btn d-lg-none" id="filterToggle">
            <i class="fas fa-filter me-2"></i> Filters
        </button>
        <div class="row">

            {{-- -------------------------- SIDEBAR (Categories & Subcategories) -------------------------- --}}
            <div class="col-lg-2 sidebar-menu">
                <h3 class="sidebar-title">Categories</h3>
                <hr>

                {{-- Loop through main Categories --}}
                @foreach($categories as $category)
                    {{-- 1. Checkbox for the main Category --}}
                    <div class="d-flex align-items-center mb-1">
                        <input type="checkbox" 
                            class="filter-checkbox category-checkbox mx-2" 
                            data-filter-id="{{ $category->id }}"
                            data-filter-name="{{ $category->name }}"
                            data-filter-type="category"
                            {{ isset($selectedCategory) && $selectedCategory == $category->id ? 'checked' : '' }}
                            >

                        <h4 class="sidebar-subtitle mb-0" style="display: inline-block;">
                            <a href="#" class="filter-link category-link" data-id="{{ $category->id }}" data-type="category">
                                {{ $category->name }} 
                            </a>
                        </h4>
                    </div>
                    
                    @if($category->subcategories->count() > 0)
                        <ul class="list-unstyled sidebar-list ms-3">
                            {{-- Loop through Subcategories --}}
                            @foreach($category->subcategories as $subcategory)
                                <li>
                                    {{-- 2. Checkbox for Subcategory (Already exists, just added unique class/data) --}}
                                    <input type="checkbox" 
                                        class="filter-checkbox subcategory-checkbox mx-2" 
                                        data-filter-id="{{ $subcategory->id }}"
                                        data-filter-name="{{ $subcategory->name }}"
                                        data-filter-type="subcategory"
                                        {{ isset($selectedSubcategory) && $selectedSubcategory == $subcategory->id ? 'checked' : '' }}>
                                    <a href="#" class="filter-link subcategory-link" data-id="{{ $subcategory->id }}" data-type="subcategory">
                                        {{ $subcategory->name }} 
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach
                
            </div>

            {{-- -------------------------- PRODUCT DISPLAY -------------------------- --}}
            <div class="col-lg-10 product-display">
                
                <div class="row mt-3 d-none">
                    <div class="col-12">
                        <div class="secondary-search-bar">
                            <input type="text" class="form-control secondary-search-input" placeholder="Search a product">
                            <button class="btn secondary-search-btn"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>

                {{-- Filter Tags will be inserted here by JavaScript --}}
                <div class="row mt-3 filter-section">
                    <div class="col-12 d-flex align-items-center" id="active-filters-container">
                        {{-- Dynamic filter tags will appear here --}}
                        <button class="btn btn-clear-adjustments d-none">Clear adjustments</button>
                    </div>
                </div>

                {{-- Products Grid Container --}}
                <div class="row product-grid mt-4" id="product-list-container">
                    @include('frontend.partials.product_grid', ['products' => $products])
                </div>

                

            </div>
        </div>
    </div>


    <script>
// Filter Toggle for Mobile
document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById('filterToggle');
    const sidebar = document.querySelector('.sidebar-menu');
    
    if (filterBtn && sidebar) {
        filterBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});
</script>

@endsection

@section('script')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // --- Core Filter Function ---
    function fetchProducts() {
        // 1. Collect all active filter IDs
        const activeCategories = [];
        const activeSubcategories = [];

        $('.filter-checkbox[data-filter-type="category"]:checked').each(function() {
            activeCategories.push($(this).data('filter-id'));
        });
        $('.filter-checkbox[data-filter-type="subcategory"]:checked').each(function() {
            activeSubcategories.push($(this).data('filter-id'));
        });

        // 2. Prepare UI for loading
        $('#product-list-container').html('<div class="col-12 text-center py-5">Loading Products...</div>');

        // 3. Send AJAX request
        $.ajax({
            url: '{{ route("shop.filter") }}', 
            method: 'POST',
            data: {
                category_ids: activeCategories, // Now sending an array
                subcategory_ids: activeSubcategories, // Now sending an array
            },
            success: function(response) {
                $('#product-list-container').html(response.html);
                updateFilterTags(); // Update tags after successful product fetch
            },
            error: function(xhr) {
                console.error("Error fetching products:", xhr.responseText);
                $('#product-list-container').html('<div class="col-12 text-center py-5 text-danger">Failed to load products.</div>');
            }
        });
    }

    // --- Filter Tag Display Function ---
    function updateFilterTags() {
        const $filterContainer = $('#active-filters-container');
        $filterContainer.empty(); // Clear existing tags
        
        const $activeCheckboxes = $('.filter-checkbox:checked');
        let hasFilters = false;

        $activeCheckboxes.each(function() {
            hasFilters = true;
            const id = $(this).data('filter-id');
            const name = $(this).data('filter-name');
            const type = $(this).data('filter-type');
            
            $filterContainer.append(`
                <div class="active-filter-tag me-2 mb-2" data-filter-id="${id}" data-filter-type="${type}">
                    ${name}
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
            `);
        });

        // Add the 'Clear adjustments' button if any filters are active
        if (hasFilters) {
            $filterContainer.append('<button class="btn btn-clear-adjustments">Clear adjustments</button>');
        }
    }


    // --- Event Handlers ---
    
    // 1. Link click handler (toggles checkbox state)
    $(document).on('click', '.filter-link', function(e) {
        e.preventDefault(); 
        // Find the adjacent or siblings checkbox
        const $checkbox = $(this).closest('.sidebar-subtitle, li').find('.filter-checkbox');
        $checkbox.prop('checked', !$checkbox.prop('checked')); // Toggle the state
        $checkbox.trigger('change'); // Manually trigger change event
    });

    // 2. Checkbox change handler (runs the main filter logic)
    $(document).on('change', '.filter-checkbox', function() {
        fetchProducts();
    });
    
    // 3. Remove individual filter tag handler
    $(document).on('click', '.active-filter-tag .btn-close', function() {
        const $tag = $(this).closest('.active-filter-tag');
        const id = $tag.data('filter-id');
        const type = $tag.data('filter-type');

        // Uncheck the corresponding checkbox in the sidebar
        $(`.filter-checkbox[data-filter-id="${id}"][data-filter-type="${type}"]`).prop('checked', false);
        
        // Remove the tag and refetch products
        $tag.remove();
        fetchProducts(); 
    });

    // 4. Clear All Filters handler
    $(document).on('click', '.btn-clear-adjustments', function() {
        $('.filter-checkbox').prop('checked', false);
        $('#active-filters-container').empty();
        fetchProducts(); // Fetch all products
    });
    
    // Initial load
    $(document).ready(function() {
        // Run fetchProducts once to ensure tags are correct if filters were previously applied
        updateFilterTags(); 

        if ($('.category-checkbox:checked').length) {
            fetchProducts();
            updateFilterTags();
        }
        if ($('.subcategory-checkbox:checked').length) {
            fetchProducts();
            updateFilterTags();
        }
    });

</script>
@endsection