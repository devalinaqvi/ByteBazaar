<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Byte Bazaar') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php if (!isset($nav_rendered)): $nav_rendered = true; ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Byte Bazaar</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/products">Products</a></li>
                <?php if ($is_logged_in ?? false): ?>
                    <li><a class="nav-link" href="/cart">Cart</a></li>
                    <li><a class="nav-link" href="/logout">Logout</a></li>
                <?php else: ?>
                    <li><a class="nav-link" href="/login">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
<?php endif; ?>

<main class="container mt-4">
    <?php if (isset($view) && !isset($page_rendered)):
        $page_rendered = true;
        $pagePath = __DIR__ . '/../pages/' . basename($view) . '.php';
        if (file_exists($pagePath)): ?>
            <?php include $pagePath; ?>
        <?php else: ?>
            <div class="alert alert-warning">View not found: <?= htmlspecialchars($view) ?></div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>