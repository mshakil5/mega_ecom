<div class="modal fade" id="quickAddToCartModal" tabindex="-1" role="dialog" aria-labelledby="quickAddToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickAddToCartModalLabel">Quick Add to Cart</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding-left: 20px;">
                <img id="modalProductImage" src="" alt="" class="product-image mb-3" style="max-width: 300px; height: auto; display: block; margin: 0 auto;">
                
                <div class="product-price mb-2">
                    <strong>Price: </strong><span id="productPrice"></span>
                </div>

                <div class="details-filter-row details-row-size mb-2">
                    <label>Color:</label>
                    <form id="colorForm">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="color-1" name="color" value="Black">
                            <label class="custom-control-label" for="color-1">Black</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="color-2" name="color" value="White">
                            <label class="custom-control-label" for="color-2">White</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="color-3" name="color" value="Red">
                            <label class="custom-control-label" for="color-3">Red</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="color-4" name="color" value="Blue">
                            <label class="custom-control-label" for="color-4">Blue</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="color-5" name="color" value="Green">
                            <label class="custom-control-label" for="color-5">Green</label>
                        </div>
                    </form>
                </div>

                <div class="details-filter-row details-row-size mb-3">
                    <label for="size">Size:</label>
                    <form id="sizeForm">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="size-1" name="size" value="XS">
                            <label class="custom-control-label" for="size-1">XS</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="size-2" name="size" value="S">
                            <label class="custom-control-label" for="size-2">S</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="size-3" name="size" value="M">
                            <label class="custom-control-label" for="size-3">M</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="size-4" name="size" value="L">
                            <label class="custom-control-label" for="size-4">L</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="size-5" name="size" value="XL">
                            <label class="custom-control-label" for="size-5">XL</label>
                        </div>
                    </form>
                </div>

                <div class="details-filter-row details-row-size mb-3">
                    <label for="qty">Qty:</label>
                    <div class="product-details-quantity">
                        <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="" step="1" data-decimals="0" required>
                    </div>
                    <div class="product-details-action col-auto pt-3">
                        <label for=""></label>
                        <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="" data-offer-id="" data-price="">
                            <span>add to cart</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>