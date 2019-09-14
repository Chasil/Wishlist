<div class="wishlist-page">
    <?php if(isset($products)): ?>
        <?php foreach($products as $product): ?>
            <div class="wishlist-sp">
                <div class="wishlist-sp-ic">
                    <div class="wishlist-sp-ic-image"><img src="<?php echo $product['image']; ?>"></div>
                </div>
                <div class="wishlist-sp-tc">
                    <div class="wishlist-sp-ic-name"><a href="<?php echo $product['link']; ?>"><?php echo $product['name']; ?></a></div>
                </div>
                <div class="wishlist-sp-r">
                    <span class="wishlist-remove" data-id="<?php echo $product['id']; ?>">Usuń ze schowka</span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>