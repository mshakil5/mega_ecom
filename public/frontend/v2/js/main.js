/* =====================================================
   main.js — extracted + cleaned from body.html
   Comments kept as requested
   ===================================================== */

/// Product data (kept from original)
const productData = [
    { name: "Polo Shirt", color: "Pink/White", price: "1140.00", oldPrice: "1490.00" },
    { name: "Polo Shirt", color: "Dark Green", price: "990.00", oldPrice: "1250.00" },
    { name: "Polo Shirt", color: "Gray", price: "990.00", oldPrice: "1250.00" },
    { name: "Polo Shirt", color: "White/Gray", price: "1140.00", oldPrice: "1490.00" },
    { name: "Polo Shirt", color: "Navy Blue", price: "750.00", oldPrice: "990.00" },
    { name: "Polo Shirt", color: "Deep Green", price: "750.00", oldPrice: "990.00" },
    { name: "Polo Shirt", color: "Cream/Gray", price: "750.00", oldPrice: "990.00" }
];
const womenProductData = [
    { name: "Kurti Set", color: "Cream/Gold", price: "1860.00", oldPrice: "2400.00" },
    { name: "Tunic", color: "Maroon", price: "1990.00", oldPrice: "2400.00" },
    { name: "Kurti", color: "Brown", price: "1790.00", oldPrice: "2300.00" },
    { name: "Tunic", color: "Beige", price: "1790.00", oldPrice: "2400.00" },
    { name: "Kurti Set", color: "Light Blue", price: "2150.00", oldPrice: "2500.00" },
    { name: "Tunic", color: "Cream", price: "1290.00", oldPrice: "1650.00" },
    { name: "Kurti", color: "Lavender", price: "1140.00", oldPrice: "1590.00" }
];

/* Create a single product card HTML string */
function createProductCard(data, isPremium = false) {
    const tag = data.name.includes("Polo") ? 'POLO' : 'KURTI';
    const imageUrlText = data.color.replace('/', '+').toUpperCase();
    const premiumBadge = isPremium ? '<div class="position-absolute top-0 end-0 bg-dark text-white p-1 px-2 small rounded-pill fw-bold" style="margin: 4px;">PREMIUM</div>' : '';

    return `
        <div class="col-6 col-md-4 col-lg-3 mb-4">
            <div class="card bg-white product-card ">
                <div class="product-image-container">
                    <img src="https://placehold.co/400x600/d1d5db/4b5563?text=${imageUrlText}" alt="${data.name} - ${data.color}" class="img-fluid">
                    <span class="badge position-absolute top-0 start-0 product-tag">${tag}</span>
                    ${premiumBadge}
                </div>
                <div class="product-price">
                    <div><strong>৳ ${data.price}</strong> <strike>৳ ${data.oldPrice}</strike></div>
                </div>
            </div>
        </div>
    `;
}

/* Create the "View More" card HTML string */
function createViewMoreCard(containerHeight) {
    return `
        <div class="col-6 col-md-4 col-lg-3 mb-4">
            <a href="#" class="card view-more-card" style="min-height: ${containerHeight}px;">
                <div class="overlay"></div>
                <div class="view-more-content fw-bold fs-4">
                    <i data-lucide="eye" class="mb-2"></i>
                    <div class="tracking-wider">VIEW MORE</div>
                </div>
            </a>
        </div>
    `;
}

/* Render all product cards and the 'View More' card */
function renderProducts(data, containerId, isMenSection) {
    const container = document.getElementById(containerId);
    if (!container) return;

    let html = '';
    data.forEach(item => {
        html += createProductCard(item);
    });

    // approximate card height to match product-image-container
    const productCardHeight = 220; // image height + some room for price/spacing
    html += createViewMoreCard(productCardHeight);

    container.innerHTML = html;
}

/* DOM ready initialization (uses jQuery where helpful) */
$(document).ready(function () {
    // Render products
    renderProducts(productData, 'men-products-container', true);
    renderProducts(womenProductData, 'women-products-container', false);

    // Render lucide icons (replace tags like <i data-lucide="eye">)
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // --- Hover dropdown helper (adds .show class on hover) ---
    function setupHoverDropdown(dropdownId) {
        $(dropdownId).on('mouseenter', function () {
            $(this).find('.dropdown-menu').addClass('show');
        }).on('mouseleave', function () {
            const $self = $(this);
            setTimeout(function () {
                if (!$self.is(':hover')) {
                    $self.find('.dropdown-menu').removeClass('show');
                }
            }, 100);
        });
    }
    setupHoverDropdown('#shopDropdown');
    setupHoverDropdown('#cartDropdown');
    setupHoverDropdown('#wishlistDropdown');
    // account-dropdown remains click-based (bootstrap default)

    // --- Search behavior ---
    const $searchInput = $('#search-input');
    const $searchResults = $('#search-results');

    $searchInput.on('focus keyup', function () {
        // Show only on larger screens to avoid blocking small screens
        if ($(window).width() >= 992) {
            $searchResults.stop(true, true).slideDown(100).attr('aria-hidden', 'false');
        }
    });

    // Hide results when clicking outside
    $(document).on('click', function (e) {
        if (!$('.search-container').is(e.target) && $('.search-container').has(e.target).length === 0) {
            $searchResults.stop(true, true).slideUp(100).attr('aria-hidden', 'true');
        }
    });

    // --- Sticky navbar ---
    const $navbar = $('#main-navbar');
    const $topBar = $('.top-bar');
    const topBarHeight = $topBar.outerHeight() || 0;

    $(window).on('scroll resize', function () {
        const scrollPos = $(window).scrollTop();
        if (scrollPos > topBarHeight) {
            $navbar.addClass('sticky-navbar');
            // avoid content jump: add body top padding equal to navbar height
            $('body').css('padding-top', $navbar.outerHeight() + 'px');
        } else {
            $navbar.removeClass('sticky-navbar');
            $('body').css('padding-top', '');
        }
    });

    // --- Initialize bootstrap carousel programmatically (safe) ---
    const carouselElement = document.getElementById('heroCarousel');
    if (carouselElement && typeof bootstrap !== 'undefined') {
        new bootstrap.Carousel(carouselElement, { interval: 5000, pause: 'hover' });
    }
});
