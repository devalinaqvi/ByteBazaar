<section class="shell empty-state error-page">
  <span class="eyebrow">LET’S GET YOU BACK ON TRACK</span>
  <h1><?= e(
    $title ?? "Something went wrong",
) ?></h1>
  <p><?= e(
    $error ?? "Please try again shortly.",
) ?></p>
  <div class="button-row">
    <a class="button orange" href="<?= e(
    url_path("products"),
) ?>">Explore the store ↗</a>
    <a class="button secondary" href="<?= e(
    url_path("cart"),
) ?>">Return to your bag</a>
  </div>
</section>
