<!DOCTYPE html>
<html lang="en" class="<?= $html_class ?? '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Computer Zone') ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="<?= $body_class ?? '' ?>">
<div class="min-h-full">
<?php if (!isset($nav_rendered)): $nav_rendered = true; ?>

    <?php (isset($admin_nav_active) && $admin_nav_active === 'active')
            ? require __DIR__ . '/../partials/admin-header.php'
            : require __DIR__ . '/../partials/website-header.php';
    ?>

<?php endif; ?>

<main>
    <?php if (isset($header)){ ?>
        <header class="relative bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
            </div>
        </header>
    <?php } ?>
    <?php if (isset($view) && !isset($page_rendered)):
        logMessage('Rendering view: ' . $view);
        $page_rendered = true;
        if(isset($is_admin) && ($is_admin === true))
        {
            $pagePath = __DIR__ . '/../pages/admin/' . basename($view) . '.php';
        }
        else if(isset($is_user) && ($is_user === true))
        {
            $pagePath = __DIR__ . '/../pages/user/' . basename($view) . '.php';
        }
        else
        {
            $pagePath = __DIR__ . '/../pages/' . basename($view) . '.php';
        }
        if (file_exists($pagePath)): ?>
            <?php include $pagePath; ?>
        <?php else: ?>
            <div class="alert alert-warning">View not found: <?= htmlspecialchars($view) ?></div>
        <?php endif; ?>
    <?php endif; ?>
</main>
    <?php if (!isset($footer_rendered)): $footer_rendered = true; ?>
    <?php (isset($admin_footer_active) && $admin_footer_active === 'active')
        ? require_once __DIR__ . '/../partials/admin-footer.php'
        : require_once __DIR__ . '/../partials/website-footer.php';
    ?>
    <?php endif; ?>
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>