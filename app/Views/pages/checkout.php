<section class="shell page-section">
  <nav class="breadcrumbs" aria-label="Checkout progress">
    <a href="<?= e(
    url_path("cart"),
) ?>">01 Bag</a>
    <span>→</span>
    <strong>02 Checkout</strong>
    <span>→</span>
    <span>03 Confirmation</span>
  </nav>
  <div class="page-heading">
    <span class="eyebrow">LET’S MAKE IT YOURS</span>
    <h1>
      The final details<span class="orange-text">.</span>
    </h1>
    <p>One simple checkout. No account required.</p>
  </div>
  <?php if (
    !$cartItems
): ?>
  <div class="empty-state">
    <h2>Your bag is empty.</h2>
    <a class="button" href="<?= e(
    url_path("products"),
) ?>">Find your next upgrade</a>
  </div>
  <?php else: ?>
  <form action="<?= e(
    url_path("checkout"),
) ?>" method="post" data-async class="checkout-grid">
    <?= csrf_field() ?>
    <input type="hidden" name="checkout_key" value="<?= e(
    $checkout_key,
) ?>" />
    <div class="checkout-fields">
      <section class="form-panel">
        <div class="form-section-heading">
          <span>01</span>
          <div>
            <h2>Contact details</h2>
            <p>Who should we deliver to?</p>
          </div>
        </div>
        <div class="field-grid">
          <div class="field">
            <label for="customer_name">Full name</label>
            <input
              id="customer_name"
              name="customer_name"
              autocomplete="name"
              required
              maxlength="100"
              value="<?= e(
    $customer["name"] ?? "",
) ?>"
            />
          </div>
          <div class="field">
            <label for="customer_email">Email address</label>
            <input
              type="email"
              id="customer_email"
              name="customer_email"
              autocomplete="email"
              required
              maxlength="255"
              value="<?= e(
    $customer["email"] ?? "",
) ?>"
            />
          </div>
          <div class="field full-field">
            <label for="phone">Phone number</label>
            <input
              type="tel"
              id="phone"
              name="phone"
              autocomplete="tel"
              required
              maxlength="30"
              placeholder="For delivery updates"
            />
          </div>
        </div>
      </section>
      <section class="form-panel">
        <div class="form-section-heading">
          <span>02</span>
          <div>
            <h2>Delivery address</h2>
            <p>Your next upgrade, delivered to your door.</p>
          </div>
        </div>
        <div class="field-grid">
          <?php foreach (
    [
        "shipping_address" => [
            "Street address",
            "street-address",
            "123 Main Street",
            500,
        ],
        "shipping_city" => ["City", "address-level2", "Toronto", 100],
        "shipping_country" => ["Country", "country-name", "Canada", 100],
        "shipping_postal_code" => [
            "Postal / ZIP code",
            "postal-code",
            "A1A 1A1",
            20,
        ],
    ]
    as $name => [$label, $auto, $placeholder, $max]
): ?>
          <div class="field <?= $name === "shipping_address"
    ? "full-field"
    : "" ?>">
            <label for="<?= e($name) ?>"><?= e(
    $label,
) ?></label>
            <input
              id="<?= e($name) ?>"
              name="<?= e(
    $name,
) ?>"
              autocomplete="<?= e($auto) ?>"
              placeholder="<?= e(
    $placeholder,
) ?>"
              maxlength="<?= $max ?>"
              required
            />
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="form-panel">
        <div class="form-section-heading">
          <span>03</span>
          <div>
            <h2>Payment</h2>
            <p>Simple and straightforward.</p>
          </div>
        </div>
        <label class="payment-option">
          <input type="radio" name="payment_method" value="cash_on_delivery" checked required />
          <span>
            <strong>Cash on delivery</strong>
            <small>Pay when your order arrives. No card details needed.</small>
          </span>
          <span>◇</span>
        </label>
      </section>
    </div>
    <aside class="summary-card">
      <span class="eyebrow">READY FOR WHAT’S NEXT</span>
      <h2>Your order</h2>
      <div class="mini-cart">
        <?php foreach (
    $cartItems
    as $item
): ?>
        <div>
          <img src="<?= e(
    upload_url($item["image"]) ?: asset("images/categories/default.webp"),
) ?>" alt="" width="56" height="56" />
          <span>
            <?= e(
    $item["product_name"],
) ?>
            <small>Qty <?= (int) $item["quantity"] ?></small>
          </span>
          <strong><?= money(
    $item["subtotal"],
) ?></strong>
        </div>
        <?php endforeach; ?>
      </div>
      <dl>
        <div>
          <dt>Subtotal</dt>
          <dd><?= money(
    $totals["subtotal"],
) ?></dd>
        </div>
        <div>
          <dt>Shipping</dt>
          <dd><?= money(
    $totals["shipping"],
) ?></dd>
        </div>
        <div>
          <dt>Tax (8%)</dt>
          <dd><?= money(
    $totals["tax"],
) ?></dd>
        </div>
        <div class="summary-total">
          <dt>Total</dt>
          <dd><?= money(
    $totals["total"],
) ?></dd>
        </div>
      </dl>
      <p class="form-error" role="alert" hidden></p>
      <button class="button orange full" type="submit">
        Place order
        <span>↗</span>
      </button>
      <p class="secure-note">Payment due on delivery.</p>
      <a class="text-link" href="<?= e(
    url_path("cart"),
) ?>">← Edit your bag</a>
    </aside>
  </form>
  <?php endif; ?>
</section>
