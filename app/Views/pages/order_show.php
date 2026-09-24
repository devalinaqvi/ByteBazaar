<section class="shell page-section">
  <nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="<?= e(
    url_path(!empty($is_admin) ? "admin/orders" : "user/dashboard"),
) ?>">Orders</a>
    <span>/</span>
    <span>#<?= (int) $order[
    "id"
] ?></span>
  </nav>
  <div class="order-heading">
    <div>
      <span class="eyebrow">YOUR NEXT CHAPTER IS IN MOTION</span>
      <h1>
        Order #<?= (int) $order[
    "id"
] ?><span class="orange-text">.</span>
      </h1>
      <p>Placed <?= e(
    date("F j, Y", strtotime($order["order_date"])),
) ?> · Payment on delivery</p>
    </div>
    <span class="status-badge"><?= e(
     ucfirst($order["status"]),
 ) ?></span>
  </div>
  <ol class="order-progress" aria-label="Order progress">
    <?php
$stages = [
    "pending" => "Order placed",
    "processing" => "Preparing",
    "shipped" => "On its way",
    "delivered" => "Delivered",
];
$current = array_search($order["status"], array_keys($stages), true);
foreach ($stages as $key => $label):
    $step = array_search(
        $key,
        array_keys($stages),
        true,
    ); ?>
    <li class="<?= $current !== false && $step <= $current
    ? "complete"
    : "" ?>" <?= $key === $order["status"]
    ? 'aria-current="step"'
    : "" ?>>
      <span><?= $step + 1 ?></span>
      <?= e($label) ?>
    </li>
    <?php
endforeach;
?>
  </ol>
  <div class="checkout-grid">
    <div>
      <div class="cart-list">
        <?php foreach (
    $order["items"]
    as $item
): ?>
        <article class="cart-row">
          <div class="cart-image">
            <img src="<?= e(
    upload_url($item["product_image"]) ?:
    asset("images/categories/default.webp"),
) ?>" alt="<?= e(
    $item["product_name"],
) ?>" width="160" height="160" />
          </div>
          <div>
            <span class="eyebrow">PART OF YOUR NEXT CHAPTER</span>
            <h2><?= e(
    $item["product_name"],
) ?></h2>
            <p>Qty <?= (int) $item["quantity"] ?> · <?= money(
     $item["unit_price"],
 ) ?> each</p>
          </div>
          <strong><?= money(
     (float) $item["unit_price"] * (int) $item["quantity"],
 ) ?></strong>
        </article>
        <?php endforeach; ?>
      </div>
      <div class="delivery-grid">
        <div>
          <span class="eyebrow">DELIVERING TO</span>
          <h3><?= e(
    $order["customer_name"],
) ?></h3>
          <p>
            <?= nl2br(e($order["shipping_address"])) ?>
            <br />
            <?= e(
    $order["shipping_city"],
) ?>, <?= e($order["shipping_postal_code"]) ?>
            <br />
            <?= e(
    $order["shipping_country"],
) ?>
          </p>
        </div>
        <div>
          <span class="eyebrow">CONTACT DETAILS</span>
          <p>
            <?= e(
    $order["customer_email"],
) ?>
            <br />
            <?= e(
    $order["phone"],
) ?>
          </p>
        </div>
      </div>
    </div>
    <aside class="summary-card">
      <h2>Order summary</h2>
      <dl>
        <?php if (
    $order["subtotal"] !== null
): ?>
        <div>
          <dt>Subtotal</dt>
          <dd><?= money(
    $order["subtotal"],
) ?></dd>
        </div>
        <div>
          <dt>Shipping</dt>
          <dd><?= money(
    $order["shipping_amount"],
) ?></dd>
        </div>
        <div>
          <dt>Tax</dt>
          <dd><?= money(
    $order["tax_amount"],
) ?></dd>
        </div>
        <?php else: ?>
        <p class="summary-note">A detailed price breakdown is unavailable for this older order.</p>
        <?php endif; ?>
        <div class="summary-total">
          <dt>Total</dt>
          <dd><?= money(
    $order["total_price"],
) ?></dd>
        </div>
      </dl>
      <p class="secure-note">Cash on delivery</p>
      <?php
$next =
    [
        "pending" => "processing",
        "processing" => "shipped",
        "shipped" => "delivered",
    ][$order["status"]] ?? null;
if (!empty($is_admin) && $next): ?>
      <form method="post" action="<?= e(
    url_path("admin/order/" . $order["id"] . "/update-status"),
) ?>" data-async>
        <?= csrf_field() ?>
        <input type="hidden" name="status" value="<?= e(
    $next,
) ?>" />
        <button class="button full" type="submit">Mark as <?= e(
    $next,
) ?> →</button>
        <p class="form-error" role="alert" hidden></p>
      </form>
      <?php endif;
?>
    </aside>
  </div>
</section>
