<?php if (($pages ?? 1) > 1):
    $query = $_GET; ?>
<nav class="pagination" aria-label="Pagination">
  <span>Page <?= (int) $page ?> of <?= (int) $pages ?></span>
  <div>
    <?php
if ($page > 1):
    $query["page"] =
        $page - 1; ?>
    <a class="button secondary small" href="?<?= e(
    http_build_query($query),
) ?>" rel="prev">← Previous</a>
    <?php
endif;
if ($page < $pages):
    $query["page"] =
        $page + 1; ?>
    <a class="button secondary small" href="?<?= e(
    http_build_query($query),
) ?>" rel="next">Next →</a>
    <?php
endif;
?>
  </div>
</nav>
<?php
endif; ?>
