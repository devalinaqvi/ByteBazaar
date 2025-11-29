<?php if (!isset($title)) $title = 'Home'; ?>
<div class="row">
    <div class="col-md-8">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p>Welcome to Byte Bazaar! Browse our selection of computers, laptops, desktops, and accessories.</p>
        <a href="/products" class="btn btn-primary">Shop Now</a>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Featured</h5>
                <p class="card-text">Check out our latest deals.</p>
            </div>
        </div>
    </div>
</div>