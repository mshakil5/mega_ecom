<script>
$(function () {

    let timer;
    const $input = $("#search-input");
    const $results = $("#search-results");
    const $btn = $("#search-btn");

    function renderResults(products) {
        if (!products.length) {
            $results.html(`<div class="p-2 text-muted small">No products found</div>`)
                    .removeClass("d-none")
                    .show();
            return;
        }

        let html = "";
        products.forEach(p => {
            html += `
                <a href="/product/${p.slug}" class="search-product-item d-flex align-items-center p-2 border-bottom">
                    <img src="/images/products/${p.feature_image}" class="search-product-image me-2" width="50"/>
                    <div class="search-product-info">
                        <p class="mb-0">${p.name}</p>
                        <p class="price mb-0">{{ $currency }} ${parseFloat(p.price).toFixed(2)}</p>
                    </div>
                </a>
            `;
        });

        $results.html(html)
                .removeClass("d-none")
                .show(); // ensure it's visible
    }

    function search() {
        let q = $input.val().trim();
        if (q.length < 2) {
            $results.addClass("d-none").hide(); // hide on empty
            return;
        }

        $.get("{{ route('search.products') }}", { query: q }, function (res) {
            renderResults(res.products);
        });
    }

    $input.on("keyup", function () {
        clearTimeout(timer);
        timer = setTimeout(search, 250);
    });

    $btn.on("click", search);

    // Optional: hide when clicking outside
    $(document).on("click", function(e) {
        if (!$(e.target).closest('#search-input, #search-results').length) {
            $results.addClass("d-none").hide();
        }
    });

});
</script>