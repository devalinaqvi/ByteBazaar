<section class="shell page-section">
  <div class="page-heading">
    <span class="eyebrow">THE BYTE BAZAAR COLLECTION</span>
    <h1>
      Find your next upgrade<span class="orange-text">.</span>
    </h1>
    <p>More focus. More play. More of what you love.</p>
  </div>
  <form method="get" action="<?= e(
    url_path("products"),
) ?>" class="catalog-toolbar">
    <div class="search-field">
      <label class="sr-only" for="search">Search products</label>
      <svg
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.7"
        aria-hidden="true"
      >
        <circle cx="10" cy="10" r="6" />
        <path d="m15 15 5 5" />
      </svg>
      <input
        id="search"
        name="q"
        value="<?= e(
    $q,
) ?>"
        placeholder="Search your next great find…"
        maxlength="200"
      />
      <button type="submit" class="text-button">Search →</button>
    </div>
    <div class="sort-field">
      <label for="sort">Sort by</label>
      <select name="sort" id="sort">
        <?php foreach (
    [
        "newest" => "Newest first",
        "price_asc" => "Price: low to high",
        "price_desc" => "Price: high to low",
        "name" => "Name: A–Z",
    ]
    as $value => $label
): ?>
        <option value="<?= e($value) ?>" <?= $sort === $value
    ? "selected"
    : "" ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="filter-row">
      <span class="eyebrow">CATEGORIES</span>
      <?php foreach (
    $categories
    as $category
): ?>
      <label class="filter-chip">
        <input type="checkbox" name="categories[]" value="<?= (int) $category->id ?>" <?= in_array(
    $category->id,
    $selected,
)
    ? "checked"
    : "" ?> />
        <span><?= e(
    $category->name,
) ?></span>
      </label>
      <?php endforeach; ?>
      <button class="button small" type="submit">Apply filters</button>
      <?php if (
    $q ||
    $selected ||
    $sort !== "newest"
): ?>
      <a class="text-link" href="<?= e(
    url_path("products"),
) ?>">Clear all</a>
      <?php endif; ?>
    </div>
  </form>
  <div class="results-heading">
    <span><?= e($total . ' ' . ($total === 1 ? 'product' : 'products') . ($q ? ' for “' . $q . '”' : ' to make it yours')) ?></span>
    <span class="eyebrow">CURATED FOR EVERYDAY POSSIBILITY</span>
  </div>
  <div class="product-grid"><?php foreach ($products as $product) {
    require base_path("app/Views/partials/product-card.php");
} ?></div>
  <?php
if (
    !$products
): ?>
  <div class="empty-state">
    <span class="empty-symbol">⌕</span>
    <h2>No matches this time.</h2>
    <p>Try a different search or remove a filter to see more possibilities.</p>
    <a class="button" href="<?= e(
    url_path("products"),
) ?>">Browse all products</a>
  </div>
  <?php endif;
require base_path("app/Views/partials/pagination.php");
?>
</section>
