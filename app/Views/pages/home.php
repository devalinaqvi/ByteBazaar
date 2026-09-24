<section class="hero shell">
  <div class="hero-copy">
    <span class="eyebrow light">
      <span class="status-dot"></span>
      THE NEXT CHAPTER STARTS HERE
    </span>
    <h1>
      Small upgrades.
      <br />
      <em>Big possibilities.</em>
    </h1>
    <p>
      For the work you love. The games you lose yourself in. And everything you’re about to create.
    </p>
    <a class="button orange" href="<?= e(
    url_path("products"),
) ?>">
      Find your next upgrade
      <span>↗</span>
    </a>
    <div class="hero-footnote">
      <span>01 — THE EVERYDAY TECH COLLECTION</span>
      <span>BUILT AROUND YOU</span>
    </div>
  </div>
  <div class="hero-art">
    <span class="orbit orbit-one"></span>
    <span class="orbit orbit-two"></span>
    <div class="hero-label">
      <span class="status-dot"></span>
      MAKE ROOM FOR MORE
    </div>
    <img
      src="<?= e(
    asset("images/categories/laptops.jpg"),
) ?>"
      alt="A laptop ready for your next project"
      width="800"
      height="650"
      fetchpriority="high"
    />
    <div class="hero-caption">
      <span>
        YOUR NEXT
        <br />
        GREAT IDEA.
      </span>
      <span class="round-arrow">↗</span>
    </div>
  </div>
</section>
<section class="shell categories-section">
  <div class="section-heading">
    <div>
      <span class="eyebrow">A PLACE FOR EVERY POSSIBILITY</span>
      <h2>What’s your next move?</h2>
    </div>
    <a class="text-link" href="<?= e(
    url_path("products"),
) ?>">Explore all products ↗</a>
  </div>
  <div class="category-grid">
    <?php foreach (
    array_slice($categories, 0, 5)
    as $category
): ?>
    <a class="category-card" href="<?= e(
    url_path("products") .
        "?" .
        http_build_query(["categories" => [$category->id]]),
) ?>">
      <img src="<?= e(
    mapCategoryImage($category->name),
) ?>" alt="" width="180" height="140" loading="lazy" />
      <span><?= e(
    $category->name,
) ?></span>
      <span aria-hidden="true">↗</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<section class="shell collection-section">
  <div class="section-heading">
    <div>
      <span class="eyebrow">FRESH IN THE STORE</span>
      <h2>Your setup, reimagined.</h2>
    </div>
    <a class="text-link" href="<?= e(
    url_path("products"),
) ?>">Shop the collection ↗</a>
  </div>
  <div class="product-grid"><?php foreach (
    array_slice($products, 0, 4)
    as $product
) {
    require base_path("app/Views/partials/product-card.php");
} ?></div>
  <?php if (
    !$products
): ?>
  <div class="empty-state">
    <h3>New possibilities are on their way.</h3>
    <p>Our next collection will be here soon.</p>
  </div>
  <?php endif; ?>
</section>
<section class="shell editorial-banner">
  <div>
    <span class="eyebrow">LESS FRICTION. MORE POSSIBILITY.</span>
    <h2>
      Good tech should
      <br />
      fit your life.
    </h2>
  </div>
  <div class="benefit">
    <span>01</span>
    <h3>Know what’s available.</h3>
    <p>Clear stock levels, so you can choose with confidence.</p>
  </div>
  <div class="benefit">
    <span>02</span>
    <h3>Keep it simple.</h3>
    <p>Guest checkout, flat $5 shipping, and payment on delivery.</p>
  </div>
</section>
