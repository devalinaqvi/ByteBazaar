<section class="shell page-section">
  <nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="<?= e(
    url_path("/"),
) ?>">Home</a>
    <span>/</span>
    <a href="<?= e(
    url_path("products"),
) ?>">Shop</a>
    <span>/</span>
    <span><?= e(
    $product->name,
) ?></span>
  </nav>
  <div class="product-detail">
    <div class="detail-image">
      <span class="eyebrow">MEET YOUR NEXT UPGRADE</span>
      <img src="<?= e(
    upload_url($product->image_url) ?: asset("images/categories/default.webp"),
) ?>" alt="<?= e(
    $product->name,
) ?>" width="720" height="720" />
    </div>
    <div class="detail-copy">
      <span class="eyebrow">THE BYTE BAZAAR COLLECTION</span>
      <h1><?= e(
    $product->name,
) ?></h1>
      <div class="detail-price"><?= money(
    $product->price,
) ?></div>
      <p class="detail-description"><?= nl2br(
    e($product->description),
) ?></p>
      <p class="stock-line">
        <span class="status-dot <?= $product->stock < 1
    ? "unavailable"
    : "" ?>"></span>
        <?= $product->stock > 0
    ? (int) $product->stock . " available — ready for your setup"
    : "Currently out of stock" ?>
      </p>
      <form action="<?= e(
    url_path("cart/add/" . $product->id),
) ?>" method="post" data-async>
        <?= csrf_field() ?>
        <div class="buy-row">
          <div class="quantity-field">
            <label for="qty">Quantity</label>
            <input
              type="number"
              id="qty"
              name="qty"
              min="1"
              max="<?= max(
    1,
    min(999, $product->stock),
) ?>"
              value="1"
              required
              <?= $product->stock < 1
    ? "disabled"
    : "" ?>
            />
          </div>
          <button class="button orange" type="submit" <?= $product->stock <
1
    ? "disabled"
    : "" ?>>
            <?= $product->stock > 0
    ? "Add to bag"
    : "Sold out" ?>
            <span>↗</span>
          </button>
        </div>
        <p class="form-error" role="alert" hidden></p>
      </form>
      <div class="detail-promises">
        <div>
          <span>↗</span>
          <strong>Flat $5 shipping</strong>
          <p>Taxes shown at checkout.</p>
        </div>
        <div>
          <span>◇</span>
          <strong>Pay on delivery</strong>
          <p>No online payment required.</p>
        </div>
      </div>
      <a class="text-link" href="<?= e(
    url_path("products"),
) ?>">← Keep exploring</a>
    </div>
  </div>
</section>
