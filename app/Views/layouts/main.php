<?php
$viewer = $viewer ?? null;
$adminArea =
    str_starts_with(
        parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?: "/",
        url_path("admin"),
    ) && ($viewer["is_admin"] ?? false);
$bagCount = $bagCount ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <link rel="icon" href="<?= e(asset('images/favicon.svg')) ?>" type="image/svg+xml" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
      name="description"
      content="Thoughtfully selected computers, components, and everyday tech. Find your next upgrade at Byte Bazaar."
    />
    <meta name="csrf-token" content="<?= e(
    csrf_hash(),
) ?>" />
    <meta name="app-base" content="<?= e(url_path()) ?>" />
    <title><?= e($title ?? "Your next upgrade") ?> · Byte Bazaar</title>
    <link rel="stylesheet" href="<?= e(asset("css/app.css")) ?>" />
    <script src="<?= e(
    asset("js/app.js"),
) ?>" defer></script>
  </head>
  <body>
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="announcement">
      <span>GOOD TECH. GREAT POSSIBILITIES.</span>
      <span>Flat $5 shipping · Pay on delivery</span>
    </div>
    <header class="site-header">
      <div class="shell header-inner">
        <a class="brand" href="<?= e(
    url_path("/"),
) ?>" aria-label="Byte Bazaar home">
          <span class="brand-mark" aria-hidden="true">b.</span>
          <span>
            BYTE
            <span class="brand-light">BAZAAR</span>
          </span>
        </a>
        <nav class="primary-nav" aria-label="Main navigation">
          <a href="<?= e(
    url_path("products"),
) ?>">Shop all</a>
          <a href="<?= e(
    url_path("products?sort=newest"),
) ?>">Latest arrivals</a>
        </nav>
        <div class="header-actions">
          <?php if ($viewer): ?>
          <a class="account-link" href="<?= e(
    url_path("user/dashboard"),
) ?>">My account</a>
          <?php if (
    $viewer["is_admin"]
): ?>
          <a class="account-link" href="<?= e(
    url_path("admin"),
) ?>">Manage store</a>
          <?php endif; ?>
          <form action="<?= e(
    url_path("logout"),
) ?>" method="post">
            <?= csrf_field() ?>
            <button class="text-button" type="submit">Sign out</button>
          </form>
          <?php else: ?>
          <a class="account-link" href="<?= e(
    url_path("login"),
) ?>">Sign in</a>
          <?php endif; ?>
          <a class="bag-link" href="<?= e(
    url_path("cart"),
) ?>">
            <svg
              width="19"
              height="19"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.7"
              aria-hidden="true"
            >
              <path d="M5 7h14l1 14H4L5 7Z" />
              <path d="M8 8V6a4 4 0 0 1 8 0v2" />
            </svg>
            Bag
            <span data-cart-count><?= (int) $bagCount ?></span>
          </a>
        </div>
      </div>
    </header>
    <?php if (
    $adminArea
): ?>
    <nav class="admin-nav shell" aria-label="Store management">
      <?php foreach (
    [
        "admin" => "Overview",
        "admin/products" => "Inventory",
        "admin/categories" => "Categories",
        "admin/users" => "Customers",
    ]
    as $path => $label
): ?>
      <?php
      $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
      $active = $path === 'admin'
          ? in_array($currentPath, [url_path('admin'), url_path('admin/orders')], true) || str_starts_with($currentPath, url_path('admin/order/'))
          : str_starts_with($currentPath, url_path($path));
      ?>
      <a href="<?= e(url_path($path)) ?>" <?= $active ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <span class="eyebrow">STORE MANAGEMENT</span>
    </nav>
    <?php endif; ?>
    <main id="main" tabindex="-1"><?php
$viewPath = base_path("app/Views/" . ($view ?? "pages/error") . ".php");
if (is_file($viewPath)) {
    require $viewPath;
} else {
    http_response_code(500);
    echo '<div class="shell empty-state"><h1>Page unavailable</h1><p>Please try again shortly.</p></div>';
}
?></main>
    <footer class="site-footer">
      <div class="shell footer-top">
        <div>
          <a class="brand" href="<?= e(
    url_path("/"),
) ?>">
            <span class="brand-mark">b.</span>
            <span>
              BYTE
              <span class="brand-light">BAZAAR</span>
            </span>
          </a>
          <p>
            A little more power.
            <br />
            A whole new possibility.
          </p>
        </div>
        <div>
          <span class="eyebrow">EXPLORE</span>
          <a href="<?= e(
    url_path("products"),
) ?>">All products</a>
          <a href="<?= e(
    url_path("cart"),
) ?>">Your bag</a>
        </div>
        <div>
          <span class="eyebrow">YOUR SPACE</span>
          <a href="<?= e(
    url_path("user/dashboard"),
) ?>">Account & orders</a>
          <a href="<?= e(
    url_path("register"),
) ?>">Create an account</a>
        </div>
        <div class="footer-note">
          <span class="status-dot"></span>
          Built for your next chapter.
          <p>Computers, components, and everyday essentials, all in one place.</p>
        </div>
      </div>
      <div class="shell footer-bottom">
        <span>© <?= date(
    "Y",
) ?> Byte Bazaar</span>
        <span>Find your next upgrade.</span>
      </div>
    </footer>
    <div class="toast" role="status" aria-live="polite" id="toast" hidden></div>
  </body>
</html>
