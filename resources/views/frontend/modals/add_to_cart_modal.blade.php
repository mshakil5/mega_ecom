<div class="modal fade" id="quickAddToCartModal" tabindex="-1" role="dialog" aria-labelledby="quickAddToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body" style="padding-left: 20px; padding-top: 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <img id="modalProductImage" src="" alt="" class="product-image mb-1" style="max-width: 300px; height: auto; display: block; margin: 0 auto;">
                <h5 id="modalProductName" class="text-start my-2"></h5>
                
                <div class="product-price mb-1" style="margin: 0; padding: 0; color: black;">
                    <strong>Price:</strong>&nbsp;&nbsp;<span id="productPrice" style="font-weight: bold; color: black;"></span>
                </div>

                <div class="details-filter-row details-row-size">
                    <label>Color:</label>
                    <form id="colorForm" class="color-options"></form>
                </div>

                <div class="details-filter-row details-row-size">
                    <label for="size">Size:</label>
                    <form id="sizeForm" class="size-options"></form>
                </div>

                <div class="details-filter-row details-row-size">
                    <label for="type">Type:</label>
                    <form id="typeForm"></form>
                </div>

                <div class="details-filter-row details-row-size">
                    <label for="qty">Qty:</label>
                    <div class="product-details-quantity">
                        <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="" step="1" data-decimals="0" required>
                    </div>
                    <div class="product-details-action col-auto pt-3">
                        <label for=""></label>
                        <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="" data-offer-id="" data-price="" data-supplier-id="" data-campaign-id="">
                            <span>add to cart</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>