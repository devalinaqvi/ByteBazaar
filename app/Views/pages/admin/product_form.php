<section class="shell page-section">
  <nav class="breadcrumbs">
    <a href="<?= e(
    url_path("admin/products"),
) ?>">Inventory</a>
    <span>/</span>
    <span><?= $product
    ? "Edit product"
    : "New product" ?></span>
  </nav>
  <div class="page-heading">
    <span class="eyebrow">CURATE SOMETHING GREAT</span>
    <h1>
      <?= e(
    $title,
) ?><span class="orange-text">.</span>
    </h1>
    <p>Clear details help customers find their perfect fit.</p>
  </div>
  <form
    class="editor-form"
    method="post"
    enctype="multipart/form-data"
    data-async
    action="<?= e(
    url_path(
        $product ? "admin/products/" . $product->id : "admin/products/create",
    ),
) ?>"
  >
    <?= csrf_field() ?>
    <div class="form-panel">
      <h2>The essentials</h2>
      <div class="field-grid">
        <div class="field full-field">
          <label for="name">Product name</label>
          <input id="name" name="name" value="<?= e(
    $product?->name ?? "",
) ?>" maxlength="255" required />
        </div>
        <div class="field full-field">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="6" maxlength="10000" required>
<?= e(
    $product?->description ?? "",
) ?></textarea>
          <small>Describe the real features and specifications. Plain text only.</small>
        </div>
        <div class="field">
          <label for="price">Price ($)</label>
          <input
            id="price"
            type="number"
            name="price"
            min="0.01"
            max="999999.99"
            step="0.01"
            value="<?= e(
    $product?->price ?? "",
) ?>"
            required
          />
        </div>
        <div class="field">
          <label for="stock">Available stock</label>
          <input
            id="stock"
            type="number"
            name="stock"
            min="0"
            max="1000000"
            value="<?= e(
    $product?->stock ?? 0,
) ?>"
            required
          />
        </div>
        <div class="field full-field">
          <label for="category_id">Category</label>
          <select id="category_id" name="category_id" required>
            <option value="">Choose a category</option>
            <?php foreach (
    $categories
    as $category
): ?>
            <option value="<?= (int) $category->id ?>" <?= $product?->category_id ===
$category->id
    ? "selected"
    : "" ?>><?= e(
    $category->name,
) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
    <div class="form-panel">
      <h2>Make a good first impression</h2>
      <div class="upload-panel">
        <img
          data-image-preview
          src="<?= e(
    upload_url($product?->image_url) ?: asset("images/categories/default.webp"),
) ?>"
          alt="Product image preview"
          width="220"
          height="180"
        />
        <div class="field">
          <label for="image">Product image</label>
          <input
            id="image"
            type="file"
            name="image"
            accept="image/jpeg,image/png,image/webp"
            data-image-input
          />
          <small>
            JPEG, PNG, or WebP. Up to 2 MB and 24 megapixels. Leave empty to keep the current image.
          </small>
        </div>
      </div>
    </div>
    <p class="form-error" role="alert" hidden></p>
    <div class="form-actions">
      <a class="button secondary" href="<?= e(
    url_path("admin/products"),
) ?>">Cancel</a>
      <button class="button orange" type="submit"><?= $product
    ? "Save changes"
    : "Create product" ?> ↗</button>
    </div>
  </form>
</section>
