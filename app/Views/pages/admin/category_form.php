<section class="shell page-section">
  <nav class="breadcrumbs">
    <a href="<?= e(
    url_path("admin/categories"),
) ?>">Categories</a>
    <span>/</span>
    <span><?= e(
    $title,
) ?></span>
  </nav>
  <div class="page-heading">
    <span class="eyebrow">ORGANIZE YOUR COLLECTION</span>
    <h1>
      <?= e(
    $title,
) ?><span class="orange-text">.</span>
    </h1>
  </div>
  <form class="editor-form narrow" method="post" data-async action="<?= e(
    url_path(
        $category
            ? "admin/categories/" . $category->id
            : "admin/categories/create",
    ),
) ?>">
    <?= csrf_field() ?>
    <div class="form-panel">
      <div class="field">
        <label for="name">Category name</label>
        <input id="name" name="name" maxlength="100" value="<?= e(
    $category?->name ?? "",
) ?>" required />
        <small>A readable URL name is generated automatically.</small>
      </div>
    </div>
    <p class="form-error" role="alert" hidden></p>
    <div class="form-actions">
      <a class="button secondary" href="<?= e(
    url_path("admin/categories"),
) ?>">Cancel</a>
      <button class="button orange" type="submit">Save category ↗</button>
    </div>
  </form>
</section>
