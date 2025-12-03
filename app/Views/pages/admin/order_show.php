<div class="mx-auto max-w-2xl pt-8 pb-24 sm:px-6 sm:pt-16 lg:max-w-7xl lg:px-8">
        <div class="space-y-2 px-4 sm:flex sm:items-baseline sm:justify-between sm:space-y-0 sm:px-0">
            <div class="flex sm:items-baseline sm:space-x-4">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Order #<?= $order->id ?? 'N/A' ?></h1>
            </div>
            <p class="text-sm text-gray-600">Order placed: <time datetime="<?= $order->order_date ?? 'N/A' ?>" class="font-medium text-gray-900"><?= $order->order_date ?? 'N/A' ?></time></p>
        </div>

        <!-- Products -->
        <section aria-labelledby="products-heading" class="mt-6">
            <h2 id="products-heading" class="sr-only">Products purchased</h2>

            <div class="space-y-8">
                <?php foreach ($order->items as $item): ?>
                <div class="border-t border-b border-gray-200 bg-white shadow-xs sm:rounded-lg sm:border">
                    <div class="px-4 py-6 sm:px-6 lg:grid lg:grid-cols-12 lg:gap-x-8 lg:p-8">
                        <div class="sm:flex lg:col-span-7">
                            <img src="<?= $item['product_image'] ?? '' ?>" alt="Insulated bottle with white base and black snap lid." class="aspect-square w-full shrink-0 rounded-lg object-cover sm:size-40" />

                            <div class="mt-6 sm:mt-0 sm:ml-6">
                                <h3 class="text-base font-medium text-gray-900">
                                    <a href="#"><?= $item['product_name'] ?? 'N/A' ?></a>
                                </h3>
                                <p class="mt-2 text-sm font-medium text-gray-900">$<?= $item['product_price'] ?? 'N/A' ?></p>
                                <p class="mt-3 text-sm text-gray-500"><?= $item['product_description'] ?? 'No description available' ?></p>
                                <p class="mt-3 text-sm text-gray-500">Quantity: <?= $item['quantity'] ?? 'N/A' ?></p>
                            </div>
                        </div>

                        <div class="mt-6 lg:col-span-5 lg:mt-0">
                            <dl class="grid grid-cols-2 gap-x-6 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-900">Delivery address</dt>
                                    <dd class="mt-3 text-gray-500">
                                        <span class="block"><?= $order->shipping_name ?? '' ?></span>
                                        <span class="block"><?= $order->shipping_address ?? '' ?></span>
                                        <span class="block"><?= $order->shipping_city ?? '' ?>, <?= $order->shipping_postal_code ?? '' ?></span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-900">Shipping updates</dt>
                                    <dd class="mt-3 space-y-3 text-gray-500">
                                        <p><?= $order->customer_email ?? '' ?></p>
                                        <p><?= $order->phone ?? '' ?></p>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

<!--                    <div class="border-t border-gray-200 px-4 py-6 sm:px-6 lg:p-8">-->
<!--                        <h4 class="sr-only">Status</h4>-->
<!--                        <p class="text-sm font-medium text-gray-900">Preparing to ship</p>-->
<!--                        <div aria-hidden="true" class="mt-6">-->
<!--                            <div class="overflow-hidden rounded-full bg-gray-200">-->
<!--                                <div style="width: calc((1 * 2 + 1) / 8 * 100%)" class="h-2 rounded-full bg-indigo-600"></div>-->
<!--                            </div>-->
<!--                            <div class="mt-6 hidden grid-cols-4 text-sm font-medium text-gray-600 sm:grid">-->
<!--                                <div class="text-indigo-600">Order placed</div>-->
<!--                                <div class="text-center text-indigo-600">Processing</div>-->
<!--                                <div class="text-center">Shipped</div>-->
<!--                                <div class="text-right">Delivered</div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Billing -->
        <section aria-labelledby="summary-heading" class="mt-16 bg-white shadow-xs sm:rounded-lg">
            <h2 id="summary-heading" class="sr-only">Billing Summary</h2>

            <div class="bg-white px-4 py-6 sm:rounded-lg sm:px-6 lg:grid lg:grid-cols-12 lg:gap-x-8 lg:px-8 lg:py-8">
                <dl class="grid grid-cols-2 gap-6 text-sm md:gap-x-8 lg:col-span-7">
                    <div>
                        <dt class="font-medium text-gray-900">Billing address</dt>
                        <dd class="mt-3 text-gray-500">
                            <span class="block"><?= isset($order->customer_name) ? $order->customer_name : 'N/A' ?></span>
                            <span class="block"><?= isset($order->shipping_address) ? $order->shipping_address : 'N/A' ?></span>
                            <span class="block"><?= isset($order->shipping_city) ? $order->shipping_city : 'N/A' ?></span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900">Payment information</dt>
                        <dd class="-mt-1 -ml-4 flex flex-wrap">
                            <div class="mt-4 ml-4">
                                <p class="text-gray-900">Cash on Delivery (COD)</p>
                            </div>
                        </dd>
                    </div>
                </dl>

                <dl class="mt-8 divide-y divide-gray-200 text-sm lg:col-span-5 lg:mt-0">
                    <div class="flex items-center justify-between pb-4">
                        <dt class="text-gray-600">Subtotal</dt>
                        <dd class="font-medium text-gray-900">$<?= $order->total_price ?? 'N/A' ?></dd>
                    </div>
                    <div class="flex items-center justify-between pt-4">
                        <dt class="font-medium text-gray-900">Order total</dt>
                        <dd class="font-medium text-indigo-600">$<?= $order->total_price ?? 'N/A' ?></dd>
                    </div>
                </dl>
            </div>
        </section>
    </div>