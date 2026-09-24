<article class="product-card">
  <a class="product-image" href="<?= e(
    url_path("product/" . $product->slug),
) ?>">
    <img src="<?= e(
    upload_url($product->image_url) ?: asset("images/categories/default.webp"),
) ?>" alt="<?= e(
    $product->name,
) ?>" loading="lazy" width="480" height="360" />
    <span class="stock-tag <?= $product->stock >
0
    ? ""
    : "sold-out" ?>"><?= $product->stock > 0
    ? "IN STOCK"
    : "SOLD OUT" ?></span>
  </a>
  <div class="product-card-body">
    <span class="eyebrow">THE TECH EDIT</span>
    <h3><a href="<?= e(
    url_path("product/" . $product->slug),
) ?>"><?= e($product->name) ?></a></h3>
    <p class="product-excerpt"><?= e(
    mb_strimwidth($product->description, 0, 105, "…"),
) ?></p>
    <div class="product-card-bottom">
      <strong><?= money(
    $product->price,
) ?></strong>
      <form action="<?= e(
    url_path("cart/add/" . $product->id),
) ?>" method="post" data-async>
        <?= csrf_field() ?>
        <button
          class="add-button"
          type="submit"
          <?= $product->stock <
1
    ? "disabled"
    : "" ?>
          aria-label="Add <?= e(
     $product->name,
 ) ?> to bag"
        >
          +
        </button>
        <p class="form-error" role="alert" hidden></p>
      </form>
    </div>
  </div>
</article>
