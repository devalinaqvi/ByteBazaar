<div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:px-0">
    <h1 class="text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Shopping Cart</h1>

    <form class="mt-12">
        <section aria-labelledby="cart-heading">
            <h2 id="cart-heading" class="sr-only">Items in your shopping cart</h2>

            <ul role="list" class="divide-y divide-gray-200 border-t border-b border-gray-200">
                <?php if(isset($cartItems) && count($cartItems) > 0) : ?>
                <?php foreach ($cartItems as $item) : ?>
                    <li class="flex py-6" data-item-id="<?php echo e($item['id']); ?>">
                        <div class="shrink-0">
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['product_name']; ?>" class="size-24 rounded-md object-cover sm:size-32" />
                        </div>

                        <div class="ml-4 flex flex-1 flex-col sm:ml-6">
                            <div>
                            <div class="flex justify-between">
                                <h4 class="text-sm">
                                    <a href="#" class="font-medium text-gray-700 hover:text-gray-800"><?php echo e($item['product_name']); ?></a>
                                </h4>
                                <p class="ml-4 text-sm font-medium text-gray-900">$<?php echo e($item['subtotal']); ?></p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-1 items-end justify-between">
                            <p class="flex items-center space-x-2 text-sm text-gray-700">
                                <span>Quantity: <?php echo e($item['quantity']) ?></span>
                                <span>Price: <?php echo e($item['price']) ?></span>
                            </p>
                            <div class="ml-4">
                                <button data-id="<?php echo e($item['id']) ?>" type="button" class="remove text-sm font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer">
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </section>

        <!-- Order summary -->
        <section aria-labelledby="summary-heading" class="mt-10">
            <h2 id="summary-heading" class="sr-only">Order summary</h2>

            <div>
                <dl class="space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-base font-medium text-gray-900">Subtotal</dt>
                        <dd class="ml-4 text-base font-medium text-gray-900">$<?php echo e($cartTotal); ?></dd>
                    </div>
                </dl>
                <p class="mt-1 text-sm text-gray-500">Shipping and taxes will be calculated at checkout.</p>
            </div>

            <div class="mt-10 w-full">
                <a href="<?php echo route('checkout') ?>" class="flex justify-center w-full rounded-md border border-transparent bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-xs hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 focus:outline-hidden">Checkout</a>
            </div>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>
                    or
                    <a href="<?php echo route('products') ?>" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Continue Shopping
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </p>
            </div>
        </section>
    </form>
</div>

<!-- Policy grid -->
<section aria-labelledby="policies-heading" class="border-t border-gray-200 bg-gray-50">
    <h2 id="policies-heading" class="sr-only">Our policies</h2>

    <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="grid grid-cols-1 gap-y-12 sm:grid-cols-2 sm:gap-x-6 lg:grid-cols-4 lg:gap-x-8 lg:gap-y-0">
            <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                <div class="md:shrink-0">
                    <div class="flow-root">
                        <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-returns-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                    </div>
                </div>
                <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                    <h3 class="text-base font-medium text-gray-900">Free returns</h3>
                    <p class="mt-3 text-sm text-gray-500">Not what you expected? Place it back in the parcel and attach the pre-paid postage stamp.</p>
                </div>
            </div>
            <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                <div class="md:shrink-0">
                    <div class="flow-root">
                        <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-calendar-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                    </div>
                </div>
                <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                    <h3 class="text-base font-medium text-gray-900">Same day delivery</h3>
                    <p class="mt-3 text-sm text-gray-500">We offer a delivery service that has never been done before. Checkout today and receive your products within hours.</p>
                </div>
            </div>
            <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                <div class="md:shrink-0">
                    <div class="flow-root">
                        <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-gift-card-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                    </div>
                </div>
                <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                    <h3 class="text-base font-medium text-gray-900">All year discount</h3>
                    <p class="mt-3 text-sm text-gray-500">Looking for a deal? You can use the code &quot;ALLYEAR&quot; at checkout and get money off all year round.</p>
                </div>
            </div>
            <div class="text-center md:flex md:items-start md:text-left lg:block lg:text-center">
                <div class="md:shrink-0">
                    <div class="flow-root">
                        <img src="https://tailwindcss.com/plus-assets/img/ecommerce/icons/icon-planet-light.svg" alt="" class="mx-auto -my-1 h-24 w-auto" />
                    </div>
                </div>
                <div class="mt-6 md:mt-0 md:ml-4 lg:mt-6 lg:ml-0">
                    <h3 class="text-base font-medium text-gray-900">For the planet</h3>
                    <p class="mt-3 text-sm text-gray-500">We’ve pledged 1% of sales to the preservation and restoration of the natural environment.</p>
                </div>
            </div>
        </div>
    </div>
</section>