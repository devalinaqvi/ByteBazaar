<section class="shell page-section">
  <div class="section-heading">
    <div class="page-heading">
      <span class="eyebrow">KEEP THE POSSIBILITIES COMING</span>
      <h1>
        Inventory<span class="orange-text">.</span>
      </h1>
      <p><?= (int) $total ?> products in your collection.</p>
    </div>
    <a class="button orange" href="<?= e(
     url_path("admin/products/create"),
 ) ?>">+ Add product</a>
  </div>
  <form class="admin-search" method="get">
    <label class="sr-only" for="inventory-search">Search inventory</label>
    <input
      id="inventory-search"
      name="q"
      value="<?= e(
    $q,
) ?>"
      placeholder="Search inventory…"
      maxlength="200"
    />
    <button type="submit" class="button">Search</button>
  </form>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (
    $products
    as $product
): ?>
        <tr>
          <td>
            <div class="table-product">
              <img src="<?= e(
    upload_url($product->image_url) ?: asset("images/categories/default.webp"),
) ?>" alt="" width="56" height="56" />
              <div>
                <strong><?= e(
    $product->name,
) ?></strong>
                <small><?= e(
    $product->slug,
) ?></small>
              </div>
            </div>
          </td>
          <td><?= money(
    $product->price,
) ?></td>
          <td><?= (int) $product->stock ?></td>
          <td><span class="status-badge"><?= $product->stock >
0
    ? "Available"
    : "Sold out" ?></span></td>
          <td>
            <div class="table-actions">
              <a class="text-link" href="<?= e(
    url_path("admin/products/" . $product->id . "/edit"),
) ?>">Edit ↗</a>
              <form
                action="<?= e(
    url_path("admin/products/" . $product->id . "/delete"),
) ?>"
                method="post"
                data-async
                data-confirm="Remove this product from the catalog? Existing orders will be preserved."
              >
                <?= csrf_field() ?>
                <button class="text-button danger" type="submit">Archive</button>
                <p class="form-error" role="alert" hidden></p>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (
    !$products
): ?>
    <div class="empty-state">
      <h2>No products found.</h2>
      <p>Try another search or add your first product.</p>
    </div>
    <?php endif; ?>
  </div>
  <?php require base_path(
    "app/Views/partials/pagination.php",
); ?>
</section>
