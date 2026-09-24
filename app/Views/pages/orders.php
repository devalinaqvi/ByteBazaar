<section class="shell page-section">
  <div class="section-heading">
    <div class="page-heading">
      <span class="eyebrow"><?= !empty(
    $is_admin
)
    ? "YOUR STORE AT A GLANCE"
    : "YOUR OWN CORNER OF BYTE BAZAAR" ?></span>
      <h1>
        <?= !empty($is_admin)
    ? "Store overview"
    : "Hello, " .
        e(
            $customer["name"] ?? ($viewer["name"] ?? "there"),
        ) ?><span class="orange-text">.</span>
      </h1>
      <p><?= !empty($is_admin)
    ? "Keep every order moving in the right direction."
    : "Your upgrades, all in one place." ?></p>
    </div>
    <a class="button <?= !empty(
    $is_admin
)
    ? ""
    : "orange" ?>" href="<?= e(
    url_path(!empty($is_admin) ? "admin/products/create" : "products"),
) ?>"><?= !empty($is_admin)
    ? "+ Add a product"
    : "Find your next upgrade ↗" ?></a>
  </div>
  <div class="account-strip">
    <div>
      <span class="eyebrow"><?= !empty($is_admin)
    ? "TOTAL STORE ORDERS"
    : "YOUR ORDERS" ?></span>
      <strong><?= (int) $total ?></strong>
    </div>
    <div>
      <span class="eyebrow"><?= !empty(
    $is_admin
)
    ? "FULFILLMENT"
    : "ACCOUNT EMAIL" ?></span>
      <p><?= !empty($is_admin)
    ? "Pending → Processing → Shipped → Delivered"
    : e($customer["email"] ?? ($viewer["email"] ?? "")) ?></p>
    </div>
  </div>
  <div class="section-heading compact">
    <h2><?= !empty($is_admin)
    ? "Recent orders"
    : "Order history" ?></h2>
    <span class="eyebrow">THE NEXT CHAPTER, IN MOTION</span>
  </div>
  <?php if (!$orders): ?>
  <div class="empty-state">
    <h2><?= !empty($is_admin)
    ? "Ready for your first order."
    : "Your story starts here." ?></h2>
    <p><?= !empty($is_admin)
    ? "New orders will appear here as customers check out."
    : "Your orders will appear here once you find your next upgrade." ?></p>
    <a class="button secondary" href="<?= e(
    url_path("products"),
) ?>">Explore the collection</a>
  </div>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order</th>
          <?php if (
    !empty($is_admin)
): ?>
          <th>Customer</th>
          <?php endif; ?>
          <th>Placed</th>
          <th>Status</th>
          <th>Total</th>
          <th><span class="sr-only">Details</span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (
    $orders
    as $order
): ?>
        <tr>
          <td><strong>#<?= (int) $order["id"] ?></strong></td>
          <?php if (
    !empty($is_admin)
): ?>
          <td>
            <?= e($order["customer_name"]) ?>
            <small><?= e(
    $order["customer_email"],
) ?></small>
          </td>
          <?php endif; ?>
          <td><?= e(
    date("M j, Y", strtotime($order["order_date"])),
) ?></td>
          <td><span class="status-badge"><?= e(
    ucfirst($order["status"]),
) ?></span></td>
          <td><?= money(
    $order["total_price"],
) ?></td>
          <td><a class="text-link" href="<?= e(
    url_path(
        (!empty($is_admin) ? "admin/order/" : "user/order/") . $order["id"],
    ),
) ?>">View order ↗</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php require base_path(
    "app/Views/partials/pagination.php",
);endif; ?>
</section>
