<section class="shell page-section">
  <div class="section-heading">
    <div class="page-heading">
      <span class="eyebrow">A PLACE FOR EVERYTHING</span>
      <h1>
        Categories<span class="orange-text">.</span>
      </h1>
      <p>Make your collection easy to explore.</p>
    </div>
    <a class="button orange" href="<?= e(
    url_path("admin/categories/create"),
) ?>">+ Add category</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Category</th>
          <th>URL name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (
    $categories
    as $category
): ?>
        <tr>
          <td><strong><?= e($category->name) ?></strong></td>
          <td><?= e(
    $category->slug,
) ?></td>
          <td>
            <div class="table-actions">
              <a class="text-link" href="<?= e(
    url_path("admin/categories/" . $category->id . "/edit"),
) ?>">Edit ↗</a>
              <form
                method="post"
                action="<?= e(
    url_path("admin/categories/" . $category->id . "/delete"),
) ?>"
                data-async
                data-confirm="Delete this category? Products will stay in the catalog without a category."
              >
                <?= csrf_field() ?>
                <button class="text-button danger" type="submit">Delete</button>
                <p class="form-error" role="alert" hidden></p>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (
    !$categories
): ?>
    <div class="empty-state">
      <h2>Your collection starts here.</h2>
      <p>Add your first category to organize the store.</p>
    </div>
    <?php endif; ?>
  </div>
</section>
