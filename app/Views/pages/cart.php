<section class="shell page-section">
  <div class="page-heading">
    <span class="eyebrow">ONE STEP CLOSER</span>
    <h1>
      Your bag<span class="orange-text">.</span>
    </h1>
    <p>A few good choices. A whole new setup.</p>
  </div>
  <?php if (
    !$cartItems
): ?>
  <div class="empty-state">
    <span class="empty-symbol">b.</span>
    <h2>Room for something great.</h2>
    <p>Your bag is empty. Find the tech that fits what’s next.</p>
    <a class="button orange" href="<?= e(
    url_path("products"),
) ?>">Explore the collection ↗</a>
  </div>
  <?php else: ?>
  <div class="checkout-grid">
    <div class="cart-list">
      <?php
$allAvailable = true;
foreach ($cartItems as $item):
    $allAvailable =
        $allAvailable &&
        $item[
            "available"
        ]; ?>
      <article class="cart-row">
        <a class="cart-image" href="<?= e(
    url_path("product/" . $item["product_slug"]),
) ?>">
          <img src="<?= e(
    upload_url($item["image"]) ?: asset("images/categories/default.webp"),
) ?>" alt="<?= e(
    $item["product_name"],
) ?>" width="180" height="180" />
        </a>
        <div class="cart-item-content">
          <span class="eyebrow">YOUR NEXT UPGRADE</span>
          <h2><a href="<?= e(
    url_path("product/" . $item["product_slug"]),
) ?>"><?= e($item["product_name"]) ?></a></h2>
          <p><?= money(
    $item["price"],
) ?> each</p>
          <?php if (
     !$item["available"]
 ): ?>
          <p class="error-text">This quantity is unavailable. Update or remove this item.</p>
          <?php endif; ?>
          <div class="cart-controls">
            <form action="<?= e(
    url_path("cart/update"),
) ?>" method="post" data-async data-reload>
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $item[
    "id"
] ?>" />
              <label class="sr-only" for="qty-<?= (int) $item[
    "id"
] ?>">Quantity for <?= e(
    $item["product_name"],
) ?></label>
              <input
                id="qty-<?= (int) $item[
    "id"
] ?>"
                type="number"
                name="qty"
                min="1"
                max="<?= max(
    1,
    min(999, $item["stock"]),
) ?>"
                value="<?= (int) $item[
    "quantity"
] ?>"
                required
              />
              <button type="submit" class="text-button">Update</button>
              <p class="form-error" role="alert" hidden></p>
            </form>
            <form method="post" action="<?= e(
    url_path("cart/remove/" . $item["id"]),
) ?>" data-async data-reload>
              <?= csrf_field() ?>
              <button type="submit" class="text-button muted">Remove</button>
              <p class="form-error" role="alert" hidden></p>
            </form>
          </div>
        </div>
        <strong><?= money(
    $item["subtotal"],
) ?></strong>
      </article>
      <?php
endforeach;
?>
      <a class="text-link" href="<?= e(
    url_path("products"),
) ?>">← Continue exploring</a>
    </div>
    <aside class="summary-card">
      <span class="eyebrow">THE GOOD STUFF, ALL TOGETHER</span>
      <h2>Order summary</h2>
      <dl>
        <div>
          <dt>Subtotal</dt>
          <dd><?= money(
    $cartTotal,
) ?></dd>
        </div>
        <div>
          <dt>Shipping</dt>
          <dd>$5.00</dd>
        </div>
        <div>
          <dt>Tax</dt>
          <dd>Calculated at checkout</dd>
        </div>
      </dl>
      <p class="summary-note">Review your exact total before placing your order.</p>
      <?php if (
    $allAvailable
): ?>
      <a class="button orange full" href="<?= e(
    url_path("checkout"),
) ?>">Continue to checkout ↗</a>
      <?php else: ?>
      <p class="error-text">Please resolve unavailable items to continue.</p>
      <?php endif; ?>
      <p class="secure-note">◇ Guest checkout available · Pay on delivery</p>
    </aside>
  </div>
  <?php endif; ?>
</section>
