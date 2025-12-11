<script>
$(function () {

    let timer;
    const $input = $("#search-input");
    const $results = $("#search-results");
    const $btn = $("#search-btn");

    function renderResults(products) {
        if (!products.length) {
            $results.html(`<div class="p-2 text-muted small">No products found</div>`).removeClass("d-none");
            return;
        }

        let html = "";
        products.forEach(p => {
            html += `
                <a href="/product/${p.slug}" class="search-product-item">
                    <img src="/images/products/${p.feature_image}" class="search-product-image" />
                    <div class="search-product-info">
                        <p>${p.name}</p>
                        <p class="price">{{ $currency }} ${parseFloat(p.price).toFixed(2)}</p>
                    </div>
                </a>
            `;
        });

        $results.html(html).removeClass("d-none");
    }

    function search() {
        let q = $input.val().trim();
        if (q.length < 2) {
            $results.addClass("d-none").html("");
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

});
</script>